<?php

namespace App\Notifications;

use App\Models\ExportJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExportReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public ExportJob $exportJob
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
        return (new MailMessage)
            ->subject('Your Health Data Export is Ready')
            ->line('Your requested health data export has been completed and is ready for download.')
            ->action('Download Export', url('/export'))
            ->line('This file will be available for 7 days.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'export_job_id' => $this->exportJob->id,
            'file_path' => $this->exportJob->file_path,
            'completed_at' => $this->exportJob->completed_at?->toISOString(),
        ];
    }
}

