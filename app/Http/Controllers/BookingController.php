<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use App\Models\Booking;
use App\Models\AssignedService;
use App\Models\User;
use App\Notifications\AdminNewBookingNotification;
use App\Notifications\ProviderNewBookingNotification;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class BookingController extends Controller
{
    public function store(Request $request, $providerId)
    {
        $request->validate([
            'event_date' => 'required|date|after:today',
            'event_details' => 'nullable|string',
        ]);

        $provider = Provider::findOrFail($providerId);

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'provider_id' => $provider->id,
            'event_date' => $request->event_date,
            'event_details' => $request->event_details,
            'total_price' => $provider->base_price ?? 0,
            'status' => 'pending_provider_response',
        ]);

        // Notifier le prestataire
        $providerUser = $provider->user;
        if ($providerUser) {
            $providerUser->notify(new ProviderNewBookingNotification($booking));
        }

        // Notifier les admins
        $admins = User::where('role', 'admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new AdminNewBookingNotification($booking));
        }

        // Notification SMS au prestataire
        try {
            SmsService::notifyNewBooking($booking);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur SMS: " . $e->getMessage());
        }

        return back()->with('success', "Votre demande a été envoyée au prestataire!");
    }

    public function createFromAssignedService($assignedServiceId)
    {
        $assignedService = AssignedService::findOrFail($assignedServiceId);
        
        // Check if user owns this assigned service
        if ($assignedService->serviceRequest->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if the assignment has been accepted
        if ($assignedService->status !== 'accepted') {
            return back()->with('error', 'Ce service n\'a pas été acceptée par le prestataire.');
        }

        // Create a booking from the assigned service
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'provider_id' => $assignedService->provider_id,
            'service_request_id' => $assignedService->service_request_id,
            'assigned_service_id' => $assignedService->id,
            'event_date' => $assignedService->serviceRequest->event_date,
            'event_details' => $assignedService->serviceRequest->description,
            'total_price' => $assignedService->serviceRequest->budget ?? $assignedService->provider->base_price ?? 0,
            'status' => 'confirmed',
            'payment_status' => 'paid', // Mark as paid to bypass payment flow
        ]);

        // Notification SMS
        try {
            SmsService::notifyNewBooking($booking);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur SMS: " . $e->getMessage());
        }

        return redirect()->route('bookings.show', $booking->id)
            ->with('success', 'Réservation confirmée! Vous pouvez maintenant échanger directement avec le prestataire.');
    }

    public function index()
    {
        $page = request('page', 1);
        $perPage = 10;
        
        // Load all bookings with provider relation eagerly
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['provider.category', 'review'])
            ->latest()
            ->get();
        
        // Also load assigned services that are accepted (but not yet booked)
        $assignedServices = AssignedService::query()
            ->join('service_requests', 'service_requests.id', '=', 'assigned_services.service_request_id')
            ->where('service_requests.user_id', Auth::id())
            ->where('assigned_services.status', 'accepted')
            ->whereNotIn('assigned_services.id', function ($query) {
                $query->select('assigned_service_id')
                    ->from('bookings')
                    ->whereNotNull('assigned_service_id');
            })
            ->select('assigned_services.*')
            ->with(['provider.category', 'serviceRequest'])
            ->latest('assigned_services.created_at')
            ->get()
            ->map(function ($assignedService) {
                // Ensure we can check the relation on assigned service
                $assignedService->load('provider.category', 'serviceRequest');
                return $assignedService;
            });
        
        // Merge them together and sort by date
        $allItems = collect()
            ->merge($bookings)
            ->merge($assignedServices)
            ->sortByDesc(function ($item) {
                if ($item instanceof Booking) {
                    return $item->created_at ?? now();
                }
                return $item->created_at ?? now();
            })
            ->values();
        
        // Paginate manually
        $items = $allItems->forPage($page, $perPage);
        $total = $allItems->count();
        $paginator = new \Illuminate\Pagination\Paginator(
            $items,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
        
        return view('bookings.index', ['allReservations' => $paginator]);
    }

    public function show($id)
    {
        // Try to find a Booking first
        $booking = Booking::find($id);
        
        if ($booking) {
            // Check if user owns this booking
            if ($booking->user_id !== Auth::id()) {
                abort(403);
            }
            
            $booking->load(['provider', 'review', 'user']);
            return view('bookings.show', compact('booking'));
        }
        
        // Try to find an AssignedService
        $assignedService = AssignedService::find($id);
        
        if ($assignedService) {
            // Check if user owns this assigned service (as the client)
            if ($assignedService->serviceRequest->user_id !== Auth::id()) {
                abort(403);
            }
            
            $assignedService->load([
                'provider.category', 
                'provider.city',
                'serviceRequest.user'
            ]);
            
            // Return a specialized view for assigned services
            return view('bookings.assigned-service-show', compact('assignedService'));
        }
        
        // Not found
        abort(404);
    }

    /**
     * Prestataire accepte une réservation (Web)
     */
    public function acceptBooking(Request $request, Booking $booking)
    {
        // Vérification que le prestataire qui accepte est bien le propriétaire de la reservation
        $user = Auth::user();
        if ($booking->provider->user_id !== $user->id) {
            abort(403);
        }

        // La reservation ne doit pas avoir déjà reçu une réponse
        if ($booking->status !== 'pending_provider_response') {
            return back()->with('error', 'Cette réservation a déjà reçu une réponse.');
        }

        // Marquer la réservation comme acceptée
        $booking->update([
            'status' => 'confirmed',
            'provider_response_at' => now(),
        ]);

        // Notifier le client que le prestataire a accepté
        $client = $booking->user;
        if ($client) {
            $client->notify(new \App\Notifications\ClientBookingAcceptedNotification($booking));
        }

        return back()->with('success', 'Réservation acceptée! Le client a été notifié et peut vous contacter.');
    }

    /**
     * Prestataire refuse une réservation (Web)
     */
    public function rejectBooking(Request $request, Booking $booking)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        // Vérification que le prestataire qui refuse est bien le propriétaire de la reservation
        $user = Auth::user();
        if ($booking->provider->user_id !== $user->id) {
            abort(403);
        }

        // La reservation ne doit pas avoir déjà reçu une réponse
        if ($booking->status !== 'pending_provider_response') {
            return back()->with('error', 'Cette réservation a déjà reçu une réponse.');
        }

        // Marquer la réservation comme refusée
        $booking->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('rejection_reason'),
            'provider_response_at' => now(),
        ]);

        // Notifier le client que le prestataire a refusé
        $client = $booking->user;
        if ($client) {
            $client->notify(new \App\Notifications\ClientBookingRejectedNotification($booking));
        }

        return back()->with('success', 'Réservation refusée. Le client a été notifié.');
    }

}



