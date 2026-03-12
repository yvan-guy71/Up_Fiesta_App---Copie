<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingMarkedDoneNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Booking $booking)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Prestation marquée comme faite')
            ->line('Le prestataire a marqué la prestation comme faite.')
            ->line('Client: ' . ($this->booking->user->name ?? ''))
            ->line('Prestataire: ' . ($this->booking->provider->name ?? ''))
            ->line('Réservation #' . $this->booking->id)
            ->action('Voir la réservation', url('/up-fiesta-kygj'));
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'title' => 'Prestation marquée comme faite',
            'body' => "Le prestataire a indiqué que la réservation #{$this->booking->id} est terminée.",
            'booking_id' => $this->booking->id,
            'provider_id' => $this->booking->provider_id,
            'user_id' => $this->booking->user_id,
            'message' => "Réservation #{$this->booking->id} marquée comme faite",
            'action_url' => url('/mes-reservations/' . $this->booking->id),
        ]);
    }
}
