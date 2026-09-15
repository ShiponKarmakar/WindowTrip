<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestEmailNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Window Trip — Test Email')
            ->line('This is a test email from your Window Trip admin panel.')
            ->line('If you received this, your email settings are working correctly. ✅');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
