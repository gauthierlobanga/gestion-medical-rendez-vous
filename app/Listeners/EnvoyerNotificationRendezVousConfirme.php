<?php

namespace App\Listeners;

use App\Events\RendezVousConfirme;
use Illuminate\Support\Facades\Log;
use App\Mail\RendezVousConfirmeMail;
use Illuminate\Support\Facades\Mail;
use App\Models\NotificationRendezVous;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EnvoyerNotificationRendezVousConfirme implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'notifications';
    public $tries = 3;

    public function handle(RendezVousConfirme $event)
    {
        $rendezvous = $event->rendezvous;
        $patient = $rendezvous->patient;

        // Envoyer l'email de confirmation au patient
        Mail::to($patient->user->email)
            ->send(new RendezVousConfirmeMail($rendezvous));

        // Enregistrer la notification dans la base de données
        NotificationRendezVous::create([
            'rendezvous_id' => $rendezvous->id,
            'type_notification' => 'confirmation',
            'destinataire' => $patient->user->email,
            'sujet' => 'Votre rendez-vous a été confirmé',
            'contenu' => "Votre rendez-vous du {$rendezvous->date_heure->format('d/m/Y à H:i')} a été confirmé.",
            'date_envoi' => now(),
            'statut' => 'envoye',
        ]);
    }

    public function failed(RendezVousConfirme $event, \Throwable $exception)
    {
        Log::error('Échec de l\'envoi de notification de confirmation', [
            'rendezvous_id' => $event->rendezvous->id,
            'error' => $exception->getMessage()
        ]);
    }
}
