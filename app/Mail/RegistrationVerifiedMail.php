<?php

namespace App\Mail;

use App\Models\CompetitionRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public CompetitionRegistration $registration;

    /**
     * Create a new message instance.
     */
    public function __construct(CompetitionRegistration $registration)
    {
        $this->registration = $registration;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $fromAddress = config('mail.from.address', 'noreply@pmrwira.com');
        $fromName = config('mail.from.name', 'Panitia SUA BHAKTI BERKARYA - PMR WIRA');

        $subject = 'Bukti Pembayaran (e-Kwitansi) & Kartu Peserta Lomba [' . $this->registration->registration_code . '] - ' . $this->registration->school_name;

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-verified',
            with: [
                'registration' => $this->registration,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
