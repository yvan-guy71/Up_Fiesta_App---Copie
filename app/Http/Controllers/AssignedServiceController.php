<?php

namespace App\Http\Controllers;

use App\Models\AssignedService;
use App\Models\Provider;
use App\Models\ServiceRequest;
use App\Notifications\ServiceAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return back()->with('success', 'Service assignment accepted! You can now start working on it.');
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

