<?php

namespace App\Mail;

use App\Models\Rendezvous;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RappelRendezVousMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $rendezvous;
    public $typeRappel;

    /**
     * Create a new message instance.
     */
    public function __construct(Rendezvous $rendezvous, string $typeRappel = '24h')
    {
        $this->rendezvous = $rendezvous;
        $this->typeRappel = $typeRappel;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $delai = $this->typeRappel === '24h' ? '24 heures' : '1 heure';
        $subject = "Rappel: Rendez-vous dans {$delai} - " . $this->rendezvous->date_heure->format('d/m/Y à H:i');

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
            view: 'emails.rendezvous.rappel',
            with: [
                'rendezvous' => $this->rendezvous,
                'typeRappel' => $this->typeRappel,
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
