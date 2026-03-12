<?php

namespace App\Notifications;

use App\Models\Event;
use App\Models\Provider;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class EventParticipationStatusNotification extends Notification
{
    use Queueable;

    protected $event;
    protected $provider;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(Event $event, Provider $provider, string $status)
    {
        $this->event = $event;
        $this->provider = $provider;
        $this->status = $status;
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
        $statusLabel = $this->status === 'confirmed' ? 'accepté' : 'refusé';
        
        return [
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'provider_name' => $this->provider->name,
            'status' => $this->status,
            'message' => "Le professionnel {$this->provider->name} a {$statusLabel} votre invitation pour l'événement : {$this->event->title}",
            'action_url' => "/admin/events/{$this->event->id}", // Ajuster selon la route réelle
        ];
    }
}
