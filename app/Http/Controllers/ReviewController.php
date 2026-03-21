<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, $bookingId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $booking = Booking::where('id', $bookingId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Allow review if booking is completed OR if provider has marked it done and review was requested
        if (!($booking->status === 'completed' || ($booking->provider_done && $booking->require_client_review))) {
            abort(403, 'Cette réservation n\'est pas prête pour une évaluation.');
        }

        if ($booking->review()->exists()) {
            return back()->with('error', 'Vous avez déjà laissé un avis pour cette prestation.');
        }

        Review::create([
            'user_id' => Auth::id(),
            'provider_id' => $booking->provider_id,
            'booking_id' => $booking->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Merci pour votre avis !');
    }
}
