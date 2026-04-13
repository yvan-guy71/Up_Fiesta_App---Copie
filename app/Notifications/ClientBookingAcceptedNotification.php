<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientBookingAcceptedNotification extends Notification implements ShouldQueue
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
            ->line('Bonne nouvelle! Le prestataire a accepté votre demande de réservation!')
            ->line('')
            ->line('**Prestataire:** ' . $provider->name)
            ->line('**Date convenue:** ' . $this->booking->event_date->format('d/m/Y'))
            ->line('**Tarif:** ' . number_format($this->booking->total_price, 0, ',', ' ') . ' XOF')
            ->line('')
            ->line('Vous pouvez à présent communiquer directement avec le prestataire pour les détails finaux et la préparation de votre événement.')
            ->action('Contacter le prestataire', url('/messages/' . $provider->user_id))
            ->line('Upfiesta - Connecter les talents avec les créateurs d\'événements!');
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
            'amount' => $this->booking->total_price,
            'message' => $this->booking->provider->name . ' a accepté votre demande de réservation!',
            'action_url' => '/messages/' . $this->booking->provider->user_id,
            'type' => 'booking_accepted',
        ];
    }
}
