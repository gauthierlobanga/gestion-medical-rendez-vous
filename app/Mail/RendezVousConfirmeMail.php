<?php

namespace App\Mail;

use App\Models\Rendezvous;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RendezVousConfirmeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $rendezvous;
    public $destinataire;

    /**
     * Create a new message instance.
     */
    public function __construct(Rendezvous $rendezvous, string $destinataire = 'patient')
    {
        $this->rendezvous = $rendezvous;
        $this->destinataire = $destinataire;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->destinataire === 'patient'
            ? 'Confirmation de votre rendez-vous du ' . $this->rendezvous->date_heure->format('d/m/Y')
            : 'Nouveau rendez-vous confirmé - ' . $this->rendezvous->date_heure->format('d/m/Y');

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.rendezvous.confirmation',
            with: [
                'rendezvous' => $this->rendezvous,
                'destinataire' => $this->destinataire,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
