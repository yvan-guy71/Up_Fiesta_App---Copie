<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceRequestDirectNotification extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Un client vous a sélectionné pour une demande de service !')
            ->line('**Détails du service:**')
            ->line('Sujet: ' . $this->serviceRequest->subject)
            ->line('Description: ' . substr($this->serviceRequest->description, 0, 150) . '...')
            ->line('Budget proposé: ' . number_format($this->serviceRequest->budget, 0) . ' XOF')
            ->line('Date souhaitée: ' . $this->serviceRequest->event_date->format('d/m/Y H:i'))
            ->line('Localisation: ' . $this->serviceRequest->location)
            ->line('**Client:**')
            ->line('Nom: ' . $this->serviceRequest->user->name)
            ->line('Veuillez accepter ou refuser cette demande dans votre espace professionnel.');
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
            'client_name' => $this->serviceRequest->user->name,
            'subject' => $this->serviceRequest->subject,
            'budget' => $this->serviceRequest->budget,
            'event_date' => $this->serviceRequest->event_date,
            'message' => 'Le client ' . $this->serviceRequest->user->name . ' vous a sélectionné pour: "' . $this->serviceRequest->subject . '"',
            'action_url' => '/prestataire/service-requests/' . $this->serviceRequest->id,
        ];
    }
}
