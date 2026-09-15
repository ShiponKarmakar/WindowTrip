<?php

namespace App\Mail;

use App\Models\VisaApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VisaApplicationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public VisaApplication $application)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We received your '.$this->application->countryName().' visa application',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.visa.received',
            with: ['application' => $this->application],
        );
    }
}
