<?php

    namespace App\Mail;

    use App\Models\User;
    use Illuminate\Bus\Queueable;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Mail\Mailable;
    use Illuminate\Mail\Mailables\Content;
    use Illuminate\Mail\Mailables\Envelope;
    use Illuminate\Queue\SerializesModels;
    use Illuminate\Mail\Mailables\Address;


    class WelcomeMail extends Mailable implements ShouldQueue
    {
        use Queueable, SerializesModels;

        public $user;

        /**
         * Crée une nouvelle instance de message.
         *
         * @param User $user
         */
        public function __construct(User $user)
        {
            $this->user = $user;
        }

        /**
         * Définit l'enveloppe du message.
         *
         * @return Envelope
         */
        public function envelope(): Envelope
        {
            return new Envelope(
                from: new Address(config('mail.from.address'), config('mail.from.name')),
                subject: 'Bienvenue sur ' . config('app.name') . ', ' . $this->user->name . ' !'
            );
        }

        /**
         * Définit le contenu du message.
         *
         * @return Content
         */
        public function content(): Content
        {
            return new Content(
                markdown: 'emails.welcome',
                with: [
                    'userName' => $this->user->name,
                    'appUrl' => config('app.url'),
                ]
            );
        }

        /**
         * Définit les pièces jointes pour le message.
         *
         * @return array<int, \Illuminate\Mail\Mailables\Attachment>
         */
        public function attachments(): array
        {
            return [];
        }
    }
