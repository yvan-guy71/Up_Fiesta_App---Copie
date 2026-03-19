<?php

namespace App\Notifications;

use App\Models\AssignedService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AssignmentRejectedAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public AssignedService $assignedService)
    {
        $this->onQueue('default');
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'assigned_service_id' => $this->assignedService->id,
            'message' => 'Le prestataire ' . $this->assignedService->provider->name . ' a REFUSÉ la mission pour : ' . $this->assignedService->serviceRequest->subject,
            'action_url' => '/up-fiesta-kygj/assigned-services/' . $this->assignedService->id,
        ];
    }
}
