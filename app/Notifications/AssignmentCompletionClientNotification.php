<?php

namespace App\Notifications;

use App\Models\AssignedService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentCompletionClientNotification extends Notification implements ShouldQueue
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
            ->line('Votre service a été complété par le prestataire.')
            ->line('**Détails du service complété:**')
            ->line('Sujet: ' . $serviceRequest->subject)
            ->line('Prestataire: ' . $provider->name)
            ->line('Merci de laisser un avis sur la qualité du service reçu.')
            ->action('Laisser un avis', route('bookings.show', $this->assignedService->id))
            ->line('Votre avis aide les autres clients à trouver les meilleurs prestataires.');
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
            'message' => 'Votre service "' . $this->assignedService->serviceRequest->subject . '" a été complété par ' . $this->assignedService->provider->name . '.',
            'action_url' => '/mes-reservations/' . $this->assignedService->id,
        ];
    }
}
