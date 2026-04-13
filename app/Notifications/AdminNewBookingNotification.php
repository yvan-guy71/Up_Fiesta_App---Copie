<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AdminNewBookingNotification extends Notification implements ShouldQueue
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
        return ['database'];
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
            'client_name' => $this->booking->user->name,
            'provider_name' => $this->booking->provider->name,
            'event_date' => $this->booking->event_date->format('d/m/Y'),
            'amount' => $this->booking->total_price,
            'message' => 'Nouvelle réservation: ' . $this->booking->user->name . ' a réservé ' . $this->booking->provider->name,
            'action_url' => '/admin/bookings/' . $this->booking->id,
            'type' => 'admin_new_booking',
        ];
    }
}
