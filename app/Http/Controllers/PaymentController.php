<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function checkout(Booking $booking, $method)
    {
        if ($booking->payment_status === 'paid') {
            return redirect()->route('bookings.index')->with('info', 'Cette réservation est déjà payée.');
        }

        $result = PaymentService::initializePayment($booking, $method);

        if (!empty($result['success'])) {
            return redirect($result['redirect_url']);
        }

        return back()->with('error', 'Impossible d\'initialiser le paiement.');
    }

    public function callback(Request $request, Booking $booking)
    {
        if ($request->status === 'success') {
            $booking->update([
                'payment_status' => 'pending',
                'transaction_id' => $request->txn ?? $request->transaction,
                'payment_method' => $request->method,
            ]);

            return redirect()->route('bookings.index')->with('success', 'Paiement initié. En cours de validation par PayGate (quelques minutes).');
        }

        return redirect()->route('bookings.index')->with('error', 'Le paiement a échoué ou a été annulé.');
    }

    public function webhook(Request $request)
    {
        $signature = $request->header('X-Paygate-Signature');
        $expected = env('PAYGATE_WEBHOOK_TOKEN');
        if ($expected && $signature !== $expected) {
            return response()->json(['error' => 'Signature invalide'], 401);
        }

        $transactionId = $request->input('transaction');
        $bookingId = $request->input('booking_id');
        $reference = $request->input('reference');

        if (!$bookingId && $reference && str_starts_with($reference, 'UPF-')) {
            $parts = explode('-', $reference);
            $bookingId = $parts[1] ?? null;
        }

        if (!$transactionId || !$bookingId) {
            return response()->json(['error' => 'Paramètres manquants'], 400);
        }

        $booking = Booking::find($bookingId);
        if (!$booking) {
            return response()->json(['error' => 'Réservation introuvable'], 404);
        }

        if (PaymentService::verifyPayment($transactionId)) {
            $total = $booking->total_price;
            $commissionRate = 0.15;
            $platformFee = round($total * $commissionRate, 2);
            $providerAmount = max($total - $platformFee, 0);

            $booking->update([
                'payment_status' => 'paid',
                'transaction_id' => $transactionId,
                'status' => 'confirmed',
                'commission_rate' => $commissionRate,
                'platform_fee' => $platformFee,
                'provider_amount' => $providerAmount,
                'payout_status' => 'pending',
                'payout_date' => null,
            ]);

            return response()->json(['status' => 'ok']);
        }

        return response()->json(['status' => 'failed'], 400);
    }
}
