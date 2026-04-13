<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProviderNewBookingNotification extends Notification implements ShouldQueue
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
        $client = $this->booking->user;
        $provider = $this->booking->provider;

        return (new MailMessage)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->subject('Nouvelle demande de réservation - ' . $client->name)
            ->line('📝 **Vous avez reçu une nouvelle demande de réservation!**')
            ->line('')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('👤 **INFORMATION CLIENT**')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('Nom: ' . $client->name)
            ->line('Email: ' . $client->email)
            ->line($client->phone ? 'Téléphone: ' . $client->phone : '')
            ->line('')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('📅 **DÉTAILS DE LA PRESTATION**')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('Date événement: ' . $this->booking->event_date->format('d/m/Y à H:i'))
            ->line('Budget proposé: **' . number_format($this->booking->total_price, 0, ',', ' ') . ' XOF**')
            ->when($this->booking->event_details, fn ($message) => 
                $message->line('')->line('💬 **MESSAGE DU CLIENT:**')->line($this->booking->event_details)
            )
            ->line('')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('⚡ **QUE FAIRE?**')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('Vous avez 2 options:')
            ->line('✅ **Accepter** la demande pour confirmer la prestation')
            ->line('❌ **Refuser** en expliquant pourquoi')
            ->line('')
            ->line('Vous pouvez répondre directement depuis votre tableau de bord.')
            ->action('Consulter la demande', url('/prestataire/reservations'))
            ->line('')
            ->line('---')
            ->line('Upfiesta - Connecter les talents avec les créateurs d\'événements!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $client = $this->booking->user;
        
        return [
            'booking_id' => $this->booking->id,
            'client_id' => $this->booking->user_id,
            'client_name' => $client->name,
            'client_email' => $client->email,
            'client_phone' => $client->phone,
            'event_date' => $this->booking->event_date->format('d/m/Y H:i'),
            'amount' => number_format($this->booking->total_price, 0, ',', ' ') . ' XOF',
            'total_price' => $this->booking->total_price,
            'message' => $this->booking->event_details,
            'action_url' => '/prestataire/reservations',
            'type' => 'new_booking_request',
            'title' => 'Nouvelle demande de réservation',
            'description' => 'Demande de ' . $client->name . ' pour le ' . $this->booking->event_date->format('d/m/Y'),
        ];
    }
}
