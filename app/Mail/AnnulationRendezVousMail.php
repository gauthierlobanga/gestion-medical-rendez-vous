<?php

namespace App\Mail;

use App\Models\Rendezvous;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AnnulationRendezVousMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $rendezvous;
    public $destinataire;
    public $raison;

    /**
     * Create a new message instance.
     */
    public function __construct(Rendezvous $rendezvous, string $destinataire, ?string $raison = null)
    {
        $this->rendezvous = $rendezvous;
        $this->destinataire = $destinataire;
        $this->raison = $raison;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->destinataire === 'patient'
            ? 'Annulation de votre rendez-vous du ' . $this->rendezvous->date_heure->format('d/m/Y')
            : 'Annulation du rendez-vous du ' . $this->rendezvous->date_heure->format('d/m/Y');

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
            view: 'emails.rendezvous.annulation',
            with: [
                'rendezvous' => $this->rendezvous,
                'destinataire' => $this->destinataire,
                'raison' => $this->raison,
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
