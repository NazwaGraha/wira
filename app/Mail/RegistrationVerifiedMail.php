<?php

namespace App\Mail;

use App\Models\CompetitionRegistration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RegistrationVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public CompetitionRegistration $registration;

    /**
     * Create a new message instance.
     */
    public function __construct(CompetitionRegistration $registration)
    {
        $this->registration = $registration->loadMissing(['event', 'teams.category']);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $fromAddress = config('mail.from.address', 'noreply@pmrwira.com');
        $fromName = config('mail.from.name', 'Panitia SUA BHAKTI BERKARYA - PMR WIRA');

        $subject = '✅ [TERVERIFIKASI SAH] e-Kwitansi & Kartu Peserta Lomba ['.$this->registration->registration_code.'] - '.$this->registration->school_name;

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
        $attachments = [];
        $cleanCode = str_replace(['/', '\\'], '-', $this->registration->registration_code ?: 'REG');

        // 1. Lampiran PDF e-Kwitansi Resmi Lunas
        try {
            $receiptPdf = Pdf::loadView('pdf.receipt', [
                'registration' => $this->registration,
            ])->setPaper('a4', 'portrait')->output();

            $attachments[] = Attachment::fromData(
                fn () => $receiptPdf,
                'e-Kwitansi-Lunas-'.$cleanCode.'.pdf'
            )->withMime('application/pdf');
        } catch (\Throwable $e) {
            Log::warning('Gagal menghasilkan lampiran PDF Kwitansi pada RegistrationVerifiedMail: '.$e->getMessage());
        }

        // 2. Lampiran PDF Kartu Peserta Lomba Resmi
        try {
            $cardsPdf = Pdf::loadView('pdf.cards', [
                'registration' => $this->registration,
            ])->setPaper('a4', 'portrait')->output();

            $attachments[] = Attachment::fromData(
                fn () => $cardsPdf,
                'Kartu-Peserta-Resmi-'.$cleanCode.'.pdf'
            )->withMime('application/pdf');
        } catch (\Throwable $e) {
            Log::warning('Gagal menghasilkan lampiran PDF Kartu Peserta pada RegistrationVerifiedMail: '.$e->getMessage());
        }

        return $attachments;
    }
}
