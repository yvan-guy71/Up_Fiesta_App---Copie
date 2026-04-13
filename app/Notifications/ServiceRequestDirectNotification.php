<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ServiceRequestDirectNotification extends Notification
{
    use Queueable;

    protected ServiceRequest $request;

    public function __construct(ServiceRequest $request)
    {
        $this->request = $request;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'request_id' => $this->request->id,
            'message'    => "Nouvelle demande directe de prestation : {$this->request->subject}",
            'action_url' => '/prestataire/demandes', // or appropriate URL
        ];
    }
}
