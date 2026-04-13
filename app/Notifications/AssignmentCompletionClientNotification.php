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
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $serviceRequest = $this->assignedService->serviceRequest;
        $provider = $this->assignedService->provider;
        $booking = \App\Models\Booking::where('assigned_service_id', $this->assignedService->id)->first();
        $targetId = $booking ? $booking->id : $this->assignedService->id;

        return (new MailMessage)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre service a été complété par le prestataire.')
            ->line('**Détails du service complété:**')
            ->line('Sujet: ' . $serviceRequest->subject)
            ->line('Prestataire: ' . $provider->name)
            ->line('**Action requise:** Veuillez laisser une note et un commentaire sur la prestation.')
            ->action('Noter le service', url('/mes-reservations/' . $targetId))
            ->line('Votre avis est essentiel pour valider le travail et permettre d\'assurer la qualité du prestataire.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $booking = \App\Models\Booking::where('assigned_service_id', $this->assignedService->id)->first();
        $targetId = $booking ? $booking->id : $this->assignedService->id;

        return [
            'assigned_service_id' => $this->assignedService->id,
            'booking_id' => $booking?->id,
            'service_request_id' => $this->assignedService->service_request_id,
            'provider_id' => $this->assignedService->provider_id,
            'message' => 'Le service "' . $this->assignedService->serviceRequest->subject . '" est terminé. Merci de laisser votre avis pour valider le travail.',
            'action_url' => '/mes-reservations/' . $targetId,
            'require_review' => true,
        ];
    }
}
