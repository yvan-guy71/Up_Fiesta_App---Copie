<?php

namespace App\Http\Controllers;

use App\Models\AssignedService;
use App\Models\Provider;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\Message;
use App\Models\Booking;
use App\Notifications\ServiceAssignedNotification;
use App\Notifications\AssignmentAcceptedAdminNotification;
use App\Notifications\AssignmentRejectedAdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class AssignedServiceController extends Controller
{
    /**
     * Admin: Assign a service to a provider
     */
    public function store(Request $request)
    {
        // Verify user is admin
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'service_request_id' => 'required|exists:service_requests,id',
            'provider_id' => 'required|exists:providers,id',
        ]);

        // Check if already assigned
        $existing = AssignedService::where('service_request_id', $validated['service_request_id'])
            ->where('provider_id', $validated['provider_id'])
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return response()->json(['error' => 'Service already assigned to this provider'], 400);
        }

        // Create assignment
        $assignedService = AssignedService::create([
            'service_request_id' => $validated['service_request_id'],
            'provider_id' => $validated['provider_id'],
            'admin_id' => Auth::id(),
            'status' => 'pending',
        ]);

        // Get provider and send notification
        $provider = Provider::find($validated['provider_id'])->user;
        $provider->notify(new ServiceAssignedNotification($assignedService));

        return response()->json([
            'message' => 'Service assigned successfully',
            'assigned_service' => $assignedService
        ]);
    }

    /**
     * Provider: View assigned services
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role !== 'provider') {
            abort(403);
        }

        $provider = Provider::where('user_id', $user->id)->first();
        
        if (!$provider) {
            abort(404);
        }

        $assignments = AssignedService::where('provider_id', $provider->id)
            ->with(['serviceRequest.user', 'admin'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('provider.assignments.index', compact('assignments'));
    }

    /**
     * Provider: View single assignment
     */
    public function show(AssignedService $assignedService)
    {
        $user = Auth::user();
        $provider = Provider::where('user_id', $user->id)->first();

        if (!$provider || $assignedService->provider_id !== $provider->id) {
            abort(403);
        }

        $assignedService->load(['serviceRequest.user', 'admin']);

        return view('provider.assignments.show', compact('assignedService'));
    }

    /**
     * Provider: Accept assignment
     */
    public function accept(AssignedService $assignedService)
    {
        $user = Auth::user();
        $provider = Provider::where('user_id', $user->id)->first();

        if (!$provider || $assignedService->provider_id !== $provider->id) {
            abort(403);
        }

        if (!$assignedService->isPending()) {
            return back()->with('error', 'This assignment can no longer be accepted');
        }

        $assignedService->update([
            'status' => 'accepted',
            'responded_at' => now(),
        ]);

        // Update service request status
        $assignedService->serviceRequest->update(['status' => 'assigned']);

        // Create a Booking record for this accepted service
        $booking = $this->createBookingFromAssignment($assignedService, $provider);

        // Notify admins
        $admins = User::where('role', 'admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new AssignmentAcceptedAdminNotification($assignedService));
            
            foreach ($admins as $admin) {
                Message::create([
                    'sender_id' => $user->id,
                    'receiver_id' => $admin->id,
                    'content' => "J'accepte la mission pour la demande : " . $assignedService->serviceRequest->subject,
                ]);
            }
        }


        // Notifier le client (notification avec mise en relation)
        $client = $assignedService->serviceRequest->user;
        $client->notify(new \App\Notifications\AssignmentAcceptedNotification($assignedService));

        // Message texte (optionnel)
        $admin = $admins->first() ?? User::where('role', 'admin')->first();
        if ($admin) {
            Message::create([
                'sender_id' => $admin->id,
                'receiver_id' => $client->id,
                'content' => "Bonne nouvelle ! Le prestataire " . $provider->name . " a accepté votre demande pour : " . $assignedService->serviceRequest->subject . ". Vous pouvez maintenant échanger directement avec lui dans la page Mes réservations.",
            ]);
        }

        return back()->with('success', 'Demande acceptée ! Vous pouvez maintenant échanger avec le client.');
    }

    /**
     * Create a booking from an accepted assignment
     */
    private function createBookingFromAssignment(AssignedService $assignedService, Provider $provider): Booking
    {
        $serviceRequest = $assignedService->serviceRequest;
        
        // Check if booking already exists for this assignment
        $existingBooking = Booking::where('assigned_service_id', $assignedService->id)
            ->where('service_request_id', $serviceRequest->id)
            ->first();
        
        if ($existingBooking) {
            return $existingBooking;
        }

        // Create new booking with data from service request
        $totalPrice = $serviceRequest->budget ?? 0;
        $commissionRate = 0; // UpFiesta is free
        $platformFee = 0;
        $providerAmount = $totalPrice;

        $booking = Booking::create([
            'user_id' => $serviceRequest->user_id,
            'provider_id' => $provider->id,
            'service_request_id' => $serviceRequest->id,
            'assigned_service_id' => $assignedService->id,
            'event_date' => $serviceRequest->event_date,
            'event_details' => $serviceRequest->description,
            'total_price' => $totalPrice,
            'commission_rate' => $commissionRate,
            'platform_fee' => $platformFee,
            'provider_amount' => $providerAmount,
            'status' => 'confirmed',
            'payment_status' => 'not_applicable', // Plus de paiement via l'application
            'payout_status' => 'completed',
        ]);

        return $booking;
    }

    /**
     * Provider: Reject assignment
     */
    public function reject(Request $request, AssignedService $assignedService)
    {
        $user = Auth::user();
        $provider = Provider::where('user_id', $user->id)->first();

        if (!$provider || $assignedService->provider_id !== $provider->id) {
            abort(403);
        }

        if (!$assignedService->isPending()) {
            return back()->with('error', 'This assignment can no longer be rejected');
        }

        $validated = $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        $assignedService->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['reason'],
            'responded_at' => now(),
        ]);

        // Notify admins
        $admins = User::where('role', 'admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new AssignmentRejectedAdminNotification($assignedService));
            
            foreach ($admins as $admin) {
                Message::create([
                    'sender_id' => $user->id,
                    'receiver_id' => $admin->id,
                    'content' => "Je refuse la mission pour la demande : " . $assignedService->serviceRequest->subject . ". Raison : " . $validated['reason'],
                ]);
            }
        }


        // Notifier le client (notification de refus avec raison)
        $client = $assignedService->serviceRequest->user;
        $client->notify(new \App\Notifications\AssignmentRejectedNotification($assignedService));

        // Message texte (optionnel)
        $admin = $admins->first() ?? User::where('role', 'admin')->first();
        if ($admin) {
            $otherProviders = Provider::where('id', '!=', $provider->id)
                 ->where('is_verified', true)
                 ->where(function($q) use ($provider) {
                     if ($provider->category_id) {
                         $q->where('category_id', $provider->category_id);
                     }
                 })
                 ->limit(3)
                 ->get();

            $proposal = "";
            if ($otherProviders->isNotEmpty()) {
                $names = $otherProviders->pluck('name')->implode(', ');
                $proposal = "\nNous vous proposons ces prestataires alternatifs : " . $names . ". N'hésitez pas à nous dire si l'un d'eux vous intéresse.";
            }

            Message::create([
                'sender_id' => $admin->id,
                'receiver_id' => $client->id,
                'content' => "Nous avons le regret de vous informer que le prestataire initialement choisi n'est pas disponible pour votre demande : " . $assignedService->serviceRequest->subject . ". " . $proposal,
            ]);
        }

        return back()->with('success', 'Assignment rejected successfully.');
    }

    /**
     * Provider: Mark assignment as completed
     */
    public function complete(AssignedService $assignedService)
    {
        $user = Auth::user();
        $provider = Provider::where('user_id', $user->id)->first();

        if (!$provider || $assignedService->provider_id !== $provider->id) {
            abort(403);
        }

        if (!$assignedService->isAccepted()) {
            return back()->with('error', 'Only accepted assignments can be marked as completed');
        }

        $assignedService->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Update service request status
        $assignedService->serviceRequest->update(['status' => 'completed']);

        return back()->with('success', 'Service marked as completed!');
    }

    /**
     * Admin: View all assignments
     */
    public function adminIndex()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $assignments = AssignedService::with(['serviceRequest.user', 'provider.user', 'admin'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.assignments.index', compact('assignments'));
    }

    /**
     * Admin: View assignment details
     */
    public function adminShow(AssignedService $assignedService)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $assignedService->load(['serviceRequest.user', 'provider.user', 'admin']);

        return view('admin.assignments.show', compact('assignedService'));
    }
}

