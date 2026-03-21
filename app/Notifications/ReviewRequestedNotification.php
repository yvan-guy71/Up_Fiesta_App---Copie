<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewRequestedNotification extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('Veuillez noter votre prestataire - Up Fiesta')
            ->line('Le prestataire ' . $this->booking->provider->name . ' a marqué le service comme terminé.')
            ->line('Pour que nos administrateurs vérifient la qualité du travail, veuillez noter le prestataire sur la plateforme.')
            ->action('Noter le prestataire', url('/reviews/' . $this->booking->id))
            ->line('Votre évaluation nous aide à maintenir la qualité des services.')
            ->salutation('Cordialement,');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'provider_id' => $this->booking->provider_id,
            'provider_name' => $this->booking->provider->name,
            'message' => 'Veuillez noter le prestataire ' . $this->booking->provider->name,
        ];
    }
}
