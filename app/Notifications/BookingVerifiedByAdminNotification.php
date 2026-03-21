<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingVerifiedByAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $reduction = number_format($this->booking->provider_commission_reduction, 2, ',', ' ') . ' XOF';
        
        return (new MailMessage)
            ->subject('Service Vérifié - Commande #' . $this->booking->id)
            ->line('Votre service pour la commande #' . $this->booking->id . ' a été vérifié par un administrateur.')
            ->line('Vous allez recevoir votre paiement bientôt.')
            ->when($this->booking->provider_commission_reduction > 0, function ($message) use ($reduction) {
                return $message->line('Note: Une réduction de ' . $reduction . ' a été appliquée suite au processus de vérification.');
            })
            ->salutation('Cordialement,');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'message' => 'Service vérifié pour la commande #' . $this->booking->id,
            'commission_reduction' => $this->booking->provider_commission_reduction,
        ];
    }
}
