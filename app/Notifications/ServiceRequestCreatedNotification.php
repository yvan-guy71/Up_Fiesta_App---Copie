<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ServiceRequestCreatedNotification extends Notification
{
    use Queueable;

    protected ServiceRequest $request;
    protected bool $forAdmin;

    public function __construct(ServiceRequest $request, bool $forAdmin = false)
    {
        $this->request = $request;
        $this->forAdmin = $forAdmin;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        if ($this->forAdmin) {
            return [
                'request_id' => $this->request->id,
                'message'    => "Nouvelle demande de service : {$this->request->subject}",
                'action_url' => '/admin/service-requests/' . $this->request->id,
            ];
        }

        // client message
        return [
            'request_id' => $this->request->id,
            'message'    => "Votre demande \"{$this->request->subject}\" a bien été envoyée.",
            'action_url' => '/mes-demandes/' . $this->request->id,
        ];
    }
}
