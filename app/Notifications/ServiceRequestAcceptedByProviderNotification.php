<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceRequestAcceptedByProviderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public ServiceRequest $serviceRequest)
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
        $provider = $this->serviceRequest->provider;

        return (new MailMessage)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Bonne nouvelle! Le prestataire a accepté votre demande de service.')
            ->line('**Détails:**')
            ->line('Prestataire: ' . $provider->name)
            ->line('Email: ' . $provider->email)
            ->line('Service: ' . $this->serviceRequest->subject)
            ->line('Date prévue: ' . $this->serviceRequest->event_date->format('d/m/Y H:i'))
            ->line('Budget confirmé: ' . number_format($this->serviceRequest->budget, 0) . ' XOF')
            ->line('Le prestataire vous contactera bientôt pour confirmer les détails.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'service_request_id' => $this->serviceRequest->id,
            'provider_name' => $this->serviceRequest->provider->name,
            'message' => 'Le prestataire ' . $this->serviceRequest->provider->name . ' a accepté votre demande pour "' . $this->serviceRequest->subject . '".',
            'action_url' => '/mes-demandes/' . $this->serviceRequest->id,
        ];
    }
}
