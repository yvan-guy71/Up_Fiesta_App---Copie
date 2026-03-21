<?php

namespace App\Notifications;

use App\Models\AssignedService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceRequestAssignedToProviderNotification extends Notification implements ShouldQueue
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
            ->line('Votre demande de service a été assignée à un prestataire.')
            ->line('**Détails du prestataire:**')
            ->line('Nom: ' . $provider->name)
            ->line('Catégorie: ' . $provider->category->name)
            ->line('**Votre demande:**')
            ->line('Sujet: ' . $serviceRequest->subject)
            ->line('Date prévue: ' . $serviceRequest->event_date->format('d/m/Y H:i'))
            ->line('Le prestataire a reçu votre demande et Up-fiesta vous contactera pour confirmer son acceptation.')
            ->line('Veuillez garder votre téléphone à proximité.');
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
            'provider_name' => $this->assignedService->provider->name,
            'message' => 'Votre demande "' . $this->assignedService->serviceRequest->subject . '" a été assignée à ' . $this->assignedService->provider->name . '.',
            'action_url' => '/mes-demandes/' . $this->assignedService->service_request_id,
        ];
    }
}
