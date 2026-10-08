<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompetitionBroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    public $broadcastData;
    public $recipientInfo;

    /**
     * Create a new message instance.
     */
    public function __construct(array $broadcastData, array $recipientInfo = [])
    {
        $this->broadcastData = $broadcastData;
        $this->recipientInfo = $recipientInfo;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $fromAddress = config('mail.from.address', 'noreply@pmrwira.com');
        $fromName = config('mail.from.name', 'Panitia SUA BHAKTI BERKARYA - PMR WIRA');

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            subject: $this->broadcastData['subject'] ?? 'Informasi Kegiatan & Lomba SUA BHAKTI BERKARYA',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.competition-broadcast',
            with: [
                'headline' => $this->broadcastData['headline'] ?? '',
                'contentBody' => $this->broadcastData['content'] ?? '',
                'buttonText' => $this->broadcastData['button_text'] ?? null,
                'buttonUrl' => $this->broadcastData['button_url'] ?? null,
                'notes' => $this->broadcastData['notes'] ?? null,
                'recipientInfo' => $this->recipientInfo,
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
