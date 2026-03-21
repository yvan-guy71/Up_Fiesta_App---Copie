<?php

namespace App\Notifications;

use App\Models\AssignedService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentAcceptedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public AssignedService $assignedService)
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $serviceRequest = $this->assignedService->serviceRequest;
        $provider = $this->assignedService->provider;

        return (new MailMessage)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Bonne nouvelle ! Le prestataire a accepté votre demande.')
            ->line('**Détails du prestataire:**')
            ->line('Nom: ' . $provider->name)
            ->line('Email: ' . $provider->user->email)
            ->line('**Service demandé:**')
            ->line('Sujet: ' . $serviceRequest->subject)
            ->line('Date prévue: ' . $serviceRequest->event_date->format('d/m/Y H:i'))
            ->line('Up-Fiesta vous contactera sous peu pour finaliser les détails.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'assigned_service_id' => $this->assignedService->id,
            'service_request_id' => $this->assignedService->service_request_id,
            'provider_id' => $this->assignedService->provider_id,
            'message' => 'Le prestataire ' . $this->assignedService->provider->name . ' a accepté votre demande.',
            'action_url' => '/mes-reservations/' . $this->assignedService->id,
        ];
    }
}
