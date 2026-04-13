<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceRequestRejectedByProviderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public ServiceRequest $serviceRequest, public string $rejectionReason = '')
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
            ->line('Malheureusement, le prestataire que vous avez sélectionné a refusé votre demande de service.')
            ->line('**Détails:**')
            ->line('Prestataire: ' . $provider->name)
            ->line('Service: ' . $this->serviceRequest->subject)
            ->when($this->rejectionReason, fn ($message) => $message->line('**Raison:** ' . $this->rejectionReason))
            ->line('Vous pouvez sélectionner un autre prestataire ou contacter Upfiesta pour de l\'aide.');
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
            'rejection_reason' => $this->rejectionReason,
            'message' => 'Le prestataire ' . $this->serviceRequest->provider->name . ' a refusé votre demande pour "' . $this->serviceRequest->subject . '".',
            'action_url' => '/mes-demandes/' . $this->serviceRequest->id,
        ];
    }
}



