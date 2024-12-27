<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\{Content, Envelope};
use Illuminate\Queue\SerializesModels;

class M001 extends Mailable
{
    use Queueable;
    use SerializesModels;

    public array $paramsMail;

    /**
     * Create a new message instance.
     *
     * @param array $paramsMail
     * @return void
     */
    public function __construct(array $paramsMail) {
        $this->paramsMail = $paramsMail;
    }

     /**
     * Get the message envelope.
     */
    public function envelope(): Envelope {
        return new Envelope(
            subject: '[Attendance Application] Password Reset Requirement',
        );
    }

     /**
     * Get the message content definition.
     */
    public function content(): Content {
        return new Content(
            html: 'emails.html.m001',
            with: $this->paramsMail,
        );
    }
}
