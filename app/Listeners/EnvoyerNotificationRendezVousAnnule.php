<?php

namespace App\Listeners;

use App\Events\RendezVousAnnule;
use Illuminate\Support\Facades\Mail;
use App\Mail\AnnulationRendezVousMail;
use App\Models\NotificationRendezVous;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EnvoyerNotificationRendezVousAnnule
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
    public function handle(RendezVousAnnule $event)
    {
        $rendezvous = $event->rendezvous;
        $patient = $rendezvous->patient;
        $medecin = $rendezvous->medecin;

        // Envoyer l'email d'annulation au patient
        Mail::to($patient->user->email)->send(new AnnulationRendezVousMail($rendezvous, 'patient'));

        // Envoyer l'email d'annulation au médecin
        Mail::to($medecin->user->email)->send(new AnnulationRendezVousMail($rendezvous, 'medecin'));

        // Enregistrer la notification dans la base de données
        NotificationRendezVous::create([
            'rendezvous_id' => $rendezvous->id,
            'type_notification' => 'annulation',
            'destinataire' => $patient->user->email,
            'sujet' => 'Votre rendez-vous a été annulé',
            'contenu' => "Votre rendez-vous du {$rendezvous->date_heure->format('d/m/Y à H:i')} a été annulé.",
            'date_envoi' => now(),
            'statut' => 'envoye',
        ]);

        NotificationRendezVous::create([
            'rendezvous_id' => $rendezvous->id,
            'type_notification' => 'annulation',
            'destinataire' => $medecin->user->email,
            'sujet' => 'Rendez-vous annulé',
            'contenu' => "Le rendez-vous du {$rendezvous->date_heure->format('d/m/Y à H:i')} avec {$patient->user->name} a été annulé.",
            'date_envoi' => now(),
            'statut' => 'envoye',
        ]);
    }
}
