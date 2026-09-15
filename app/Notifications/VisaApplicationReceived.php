<?php

namespace App\Notifications;

use App\Models\VisaApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VisaApplicationReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public VisaApplication $application
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(
                'We received your ' .
                $this->application->countryName() .
                ' visa application'
            )
            ->markdown('emails.visa.received', [
                'application' => $this->application,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
