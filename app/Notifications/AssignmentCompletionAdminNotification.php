<?php

namespace App\Notifications;

use App\Models\AssignedService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentCompletionAdminNotification extends Notification implements ShouldQueue
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
        $client = $serviceRequest->user;

        return (new MailMessage)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Le prestataire a signalé la finalisation du service.')
            ->line('**Informations du service:**')
            ->line('Sujet: ' . $serviceRequest->subject)
            ->line('Prestataire: ' . $provider->name)
            ->line('Client: ' . $client->name)
            ->line('Date d\'exécution prévue: ' . $serviceRequest->event_date->format('d/m/Y H:i'))
            ->action('Voir les détails', route('filament.admin.resources.assigned-services.view', $this->assignedService->id))
            ->line('Veuillez contacter le client pour confirmer et finaliser le service.');
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
            'message' => 'Le prestataire ' . $this->assignedService->provider->name . ' a signalé la finalisation du service "' . $this->assignedService->serviceRequest->subject . '".',
            'action_url' => '/up-fiesta-kygj/assigned-services/' . $this->assignedService->id,
        ];
    }
}
