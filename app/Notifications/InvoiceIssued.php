<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceIssued extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Invoice $invoice)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $inv = $this->invoice;
        $company = Setting::get('company_name');

        $mail = (new MailMessage)
            ->subject("Invoice {$inv->number} from {$company}")
            ->markdown('emails.invoice.issued', ['invoice' => $inv]);

        try {
            $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $inv]);
            $mail->attachData($pdf->output(), "{$inv->number}.pdf", ['mime' => 'application/pdf']);
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
