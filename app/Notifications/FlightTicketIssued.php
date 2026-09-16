<?php

namespace App\Notifications;

use App\Models\FlightTicket;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FlightTicketIssued extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public FlightTicket $ticket)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticket = $this->ticket;
        $company = Setting::get('company_name');

        $mail = (new MailMessage)
            ->subject("Your e-ticket {$ticket->number} from {$company}")
            ->markdown('emails.ticket.issued', ['ticket' => $ticket]);

        try {
            $pdf = Pdf::loadView('pdf.ticket', ['ticket' => $ticket]);
            $mail->attachData($pdf->output(), "{$ticket->number}.pdf", ['mime' => 'application/pdf']);
        } catch (\Throwable $e) {
            report($e); // still send the email even if PDF generation fails
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
