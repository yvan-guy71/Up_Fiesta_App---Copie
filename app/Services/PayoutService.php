<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class PayoutService
{
    public static function transfer(Booking $booking): bool
    {
        if ($booking->payment_status !== 'paid' || $booking->status !== 'completed') {
            Log::warning("Payout non autorisé pour booking #{$booking->id}");
            return false;
        }

        try {
            // TODO: Intégrer l'API de versement (Mobile Money / virement)
            // Exemple: appeler l'opérateur pour verser $booking->provider_amount au prestataire

            $booking->update([
                'payout_status' => 'paid',
                'payout_date' => now(),
            ]);

            Log::info("Payout effectué pour booking #{$booking->id} montant {$booking->provider_amount} XOF");
            return true;
        } catch (\Throwable $e) {
            Log::error("Erreur payout booking #{$booking->id}: " . $e->getMessage());
            return false;
        }
    }
}
