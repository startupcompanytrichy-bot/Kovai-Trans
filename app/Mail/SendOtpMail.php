<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $recipientEmail;
    public string $recipientName;

    public function __construct(string $otp, string $recipientEmail, string $recipientName)
    {
        $this->otp = $otp;
        $this->recipientEmail = $recipientEmail;
        $this->recipientName = $recipientName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Password Reset Code - Transport ERP',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.send-otp',
            text: 'emails.send-otp-text',
        );
    }
}
