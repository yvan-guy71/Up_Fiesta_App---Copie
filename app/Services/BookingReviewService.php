<?php

namespace App\Services;

use App\Models\Booking;
use App\Notifications\ReviewRequestedNotification;
use App\Notifications\BookingVerifiedByAdminNotification;

class BookingReviewService
{
    /**
     * Demande au client de noter le prestataire après que le prestataire ait marqué le service comme terminé
     */
    public function requestClientReview(Booking $booking): void
    {
        if ($booking->require_client_review) {
            return; // Déjà appelé
        }

        // Marquer que la notification a été enviée
        $booking->update([
            'require_client_review' => true,
            'client_review_requested_at' => now(),
        ]);

        // Notifier le client
        $booking->user->notify(new ReviewRequestedNotification($booking));
    }

    /**
     * Vérifie le service par l'admin et applique les réductions le cas échéant
     */
    public function verifyBookingByAdmin(Booking $booking, int $adminId, bool $applyCommissionReduction = true): void
    {
        if ($booking->admin_verification_status === 'verified') {
            return; // Déjà vérifié
        }

        // Calculer la réduction de commission (-15%)
        $commissionReduction = 0;
        if ($applyCommissionReduction && $booking->provider_amount > 0) {
            $commissionReduction = $booking->provider_amount * 0.15;
            $newProviderAmount = $booking->provider_amount - $commissionReduction;
        } else {
            $newProviderAmount = $booking->provider_amount;
        }

        // Mettre à jour le booking avec le statut de vérification
        $booking->update([
            'admin_verification_status' => 'verified',
            'admin_verified_at' => now(),
            'admin_verified_by' => $adminId,
            'provider_commission_reduction' => $commissionReduction,
            'provider_amount' => $newProviderAmount,
            'payout_status' => 'ready', // Marquer comme prêt pour le payout
        ]);

        // Notifier le prestataire
        $booking->provider->notify(new BookingVerifiedByAdminNotification($booking));
    }

    /**
     * Vérifier si le client a noté le prestataire
     */
    public function hasClientReviewd(Booking $booking): bool
    {
        return $booking->review()->exists();
    }
}
