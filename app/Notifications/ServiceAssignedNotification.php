<?php

namespace App\Notifications;

use App\Models\AssignedService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceAssignedNotification extends Notification implements ShouldQueue
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
        
        return (new MailMessage)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Un nouveau service vous a été assigné !')
            ->line('**Détails du service:**')
            ->line('Sujet: ' . $serviceRequest->subject)
            ->line('Description: ' . substr($serviceRequest->description, 0, 100) . '...')
            ->line('Budget: ' . number_format($serviceRequest->budget, 2) . ' XOF')
            ->line('Date: ' . $serviceRequest->event_date->format('d/m/Y H:i'))
            ->action('Voir le détail', route('provider.assignments.show', $this->assignedService->id))
            ->line('Veuillez accepter ou refuser cette assignation dans votre espace professionnel.');
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
            'subject' => $this->assignedService->serviceRequest->subject,
            'client_name' => $this->assignedService->serviceRequest->user->name,
            'budget' => $this->assignedService->serviceRequest->budget,
            'event_date' => $this->assignedService->serviceRequest->event_date,
        ];
    }
}

         