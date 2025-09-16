<?php

namespace App\Listeners;

use App\Events\RendezVousPlanifie;
use App\Mail\RappelRendezVousMail;
use Illuminate\Support\Facades\Mail;
use App\Models\NotificationRendezVous;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EnvoyerNotificationRendezVousPlanifie
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RendezVousPlanifie $event)
    {
        $rendezvous = $event->rendezvous;
        $patient = $rendezvous->patient;
        $medecin = $rendezvous->medecin;

        // Envoyer l'email au patient
        Mail::to($patient->user->email)->send(new RappelRendezVousMail($rendezvous, 'patient'));

        // Envoyer l'email au médecin
        Mail::to($medecin->user->email)->send(new RappelRendezVousMail($rendezvous, 'medecin'));

        // Enregistrer la notification dans la base de données
        NotificationRendezVous::create([
            'rendezvous_id' => $rendezvous->id,
            'type_notification' => 'confirmation',
            'destinataire' => $patient->user->email,
            'sujet' => 'Confirmation de votre rendez-vous',
            'contenu' => "Votre rendez-vous a été planifié pour le {$rendezvous->date_heure->format('d/m/Y à H:i')} avec le Dr {$medecin->user->name}",
            'date_envoi' => now(),
            'statut' => 'envoye',
        ]);

        NotificationRendezVous::create([
            'rendezvous_id' => $rendezvous->id,
            'type_notification' => 'confirmation',
            'destinataire' => $medecin->user->email,
            'sujet' => 'Nouveau rendez-vous planifié',
            'contenu' => "Un nouveau rendez-vous a été planifié pour le {$rendezvous->date_heure->format('d/m/Y à H:i')} avec le patient {$patient->user->name}",
            'date_envoi' => now(),
            'statut' => 'envoye',
        ]);
    }
}
