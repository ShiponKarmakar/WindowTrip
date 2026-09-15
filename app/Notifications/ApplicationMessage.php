<?php

namespace App\Notifications;

use App\Models\VisaApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationMessage extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public VisaApplication $application,
        public string $subjectLine,
        public string $body,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subjectLine)
            ->markdown('emails.application.message', [
                'application' => $this->application,
                'body' => $this->body,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
