<?php

namespace App\Notifications;

use App\Models\AssignedService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentRejectedNotification extends Notification implements ShouldQueue
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
        return ['database', 'mail'];
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
            ->line('Malheureusement, le prestataire a refusé votre demande de service.')
            ->line('**Détails:**')
            ->line('Prestataire: ' . $provider->name)
            ->line('Service: ' . $serviceRequest->subject)
            ->when($this->assignedService->rejection_reason, fn ($message) => $message->line('**Raison du refus:** ' . $this->assignedService->rejection_reason))
            ->line('Nous cherchons actuellement un autre prestataire pour réaliser votre demande. Vous serez notifié dès qu\'une nouvelle assignation sera faite.');
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
            'provider_name' => $this->assignedService->provider->name,
            'rejection_reason' => $this->assignedService->rejection_reason,
            'message' => 'Le prestataire ' . $this->assignedService->provider->name . ' a refusé votre demande "' . $this->assignedService->serviceRequest->subject . '".',
            'action_url' => '/mes-demandes/' . $this->assignedService->service_request_id,
        ];
    }
}
