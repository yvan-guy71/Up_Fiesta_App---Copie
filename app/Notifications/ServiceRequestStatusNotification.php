<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ServiceRequestStatusNotification extends Notification
{
    use Queueable;

    protected ServiceRequest $request;
    protected string $status;
    protected bool $forAdmin;

    public function __construct(ServiceRequest $request, string $status, bool $forAdmin = false)
    {
        $this->request = $request;
        $this->status = $status;
        $this->forAdmin = $forAdmin;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $statusLabel = $this->status === 'approved' ? 'acceptée' : 'refusée';

        if ($this->forAdmin) {
            return [
                'request_id' => $this->request->id,
                'message'    => "La demande \"{$this->request->subject}\" a été {$statusLabel}.",
                'action_url' => '/admin/service-requests/' . $this->request->id,
            ];
        }

        return [
            'request_id' => $this->request->id,
            'message'    => "Votre demande \"{$this->request->subject}\" a été {$statusLabel}.",
            'action_url' => '/mes-demandes/' . $this->request->id,
        ];
    }
}
