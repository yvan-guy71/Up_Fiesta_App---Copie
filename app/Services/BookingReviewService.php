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
     * Vérifie le service par l'admin après la fin du service et l'avis du client
     */
    public function verifyBookingByAdmin(Booking $booking, int $adminId): void
    {
        if ($booking->admin_verification_status === 'verified') {
            return; // Déjà vérifié
        }

        // UpFiesta juge la qualité du service et du prestataire
        // Plus de gestion de commission ou de payout ici, car le paiement est direct

        // Mettre à jour le booking avec le statut de vérification
        $booking->update([
            'admin_verification_status' => 'verified',
            'admin_verified_at' => now(),
            'admin_verified_by' => $adminId,
            'payout_status' => 'completed', // Marqué comme complété au niveau administratif
        ]);

        // Notifier le prestataire que son service a été validé par l'admin
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
