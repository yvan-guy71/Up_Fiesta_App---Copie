<?php

namespace App\Notifications;

use App\Models\Provider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProviderRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Provider $provider,
        public string $rejectionReason = ''
    ) {
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
        $message = (new MailMessage)
            ->subject(__('notifications.provider_rejected_subject'))
            ->greeting(__('notifications.provider_rejected_greeting', ['name' => $notifiable->name]))
            ->line(__('notifications.provider_rejected_message'));

        if ($this->rejectionReason) {
            $message->line(__('notifications.rejection_reason') . ':')
                ->line($this->rejectionReason);
        }

        $message->line(__('notifications.provider_fix_info_message'))
            ->action(__('notifications.update_profile'), route('provider.update-profile'))
            ->line(__('notifications.contact_support', ['email' => config('mail.from.address')]));

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => __('notifications.provider_rejected_message'),
            'action_url' => route('provider.update-profile'),
            'rejection_reason' => $this->rejectionReason,
        ];
    }
}
