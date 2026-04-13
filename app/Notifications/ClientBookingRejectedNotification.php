<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientBookingRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Booking $booking)
    {
        $this->onQueue('default');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $provider = $this->booking->provider;

        return (new MailMessage)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Malheureusement, le prestataire a refusé votre demande de réservation.')
            ->line('')
            ->line('**Prestataire:** ' . $provider->name)
            ->line('**Date demandée:** ' . $this->booking->event_date->format('d/m/Y'))
            ->when($this->booking->rejection_reason, fn ($message) => 
                $message->line('')->line('**Raison du refus:**')->line($this->booking->rejection_reason)
            )
            ->line('')
            ->line('Vous pouvez explorer d\'autres prestataires proposant des services similaires sur Upfiesta.')
            ->action('Retour à l\'accueil', url('/'))
            ->line('Upfiesta vous remercie de votre confiance.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'provider_id' => $this->booking->provider_id,
            'provider_name' => $this->booking->provider->name,
            'event_date' => $this->booking->event_date->format('d/m/Y'),
            'rejection_reason' => $this->booking->rejection_reason,
            'message' => $this->booking->provider->name . ' a refusé votre demande de réservation' . ($this->booking->rejection_reason ? ' pour la raison suivante: ' . $this->booking->rejection_reason : '.'),
            'action_url' => '/',
            'type' => 'booking_rejected',
        ];
    }
}
