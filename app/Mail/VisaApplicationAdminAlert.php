<?php

namespace App\Mail;

use App\Models\VisaApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VisaApplicationAdminAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public VisaApplication $application)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New visa application — '.$this->application->countryName().' ('.$this->application->reference.')',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.visa.admin-alert',
            with: ['application' => $this->application],
        );
    }
}
