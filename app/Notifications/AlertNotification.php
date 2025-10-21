<?php

namespace App\Notifications;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Alert $alert
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = match($this->alert->level) {
            'critical' => '⚠️ Critical Health Alert',
            'warn' => '⚡ Health Alert',
            default => 'ℹ️ Health Notification',
        };

        return (new MailMessage)
            ->subject($subject)
            ->line($this->alert->message)
            ->action('View Dashboard', url('/dashboard'))
            ->line('This alert was triggered on ' . $this->alert->triggered_at->format('F j, Y \a\t g:i A'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'alert_id' => $this->alert->id,
            'type' => $this->alert->type,
            'level' => $this->alert->level,
            'message' => $this->alert->message,
            'triggered_at' => $this->alert->triggered_at->toISOString(),
        ];
    }
}

