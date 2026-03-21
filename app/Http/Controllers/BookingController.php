<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use App\Models\Booking;
use App\Models\AssignedService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'status' => 'pending',
        ]);

        // Notification SMS
        try {
            SmsService::notifyNewBooking($booking);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur SMS: " . $e->getMessage());
        }

        return back()->with('success', "Up-Fiesta se charge de tout, nous contactons les prestataires pour vous.");
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
            'payment_status' => 'unpaid',
        ]);

        // Notification SMS
        try {
            SmsService::notifyNewBooking($booking);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur SMS: " . $e->getMessage());
        }

        return redirect()->route('bookings.show', $booking->id)
            ->with('success', 'Réservation créée! Procédez au paiement pour finaliser.');
    }

    public function index()
    {
        $page = request('page', 1);
        $perPage = 10;
        
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['provider', 'review'])
            ->latest()
            ->get();
        
        // Also load assigned services that are accepted (but not yet booked)
        $assignedServices = AssignedService::whereHas('serviceRequest', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->where('status', 'accepted')
            ->doesntHave('bookings') // Use doesntHave instead of whereDoesntHave
            ->with(['provider', 'serviceRequest'])
            ->latest()
            ->get();
        
        // Merge them together and sort by date
        $allItems = collect()
            ->merge($bookings)
            ->merge($assignedServices)
            ->sortByDesc(function ($item) {
                return $item->created_at;
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
}
