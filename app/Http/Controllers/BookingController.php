<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use App\Models\Booking;
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

    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['provider', 'review'])
            ->latest()
            ->paginate(10);
        
        return view('bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        // Check if user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['provider', 'review', 'user']);
        
        return view('bookings.show', compact('booking'));
    }
}
