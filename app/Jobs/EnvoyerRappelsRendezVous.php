<?php

namespace App\Jobs;

use App\Mail\RappelRendezVousMail;
use App\Models\NotificationRendezVous;
use App\Models\Rendezvous;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnvoyerRappelsRendezVous implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $maxExceptions = 5;
    public $timeout = 300;
    public $backoff = [30, 60, 120];

    public function __construct()
    {
        $this->onQueue('notifications');
    }

    public function handle()
    {
        Log::info('Début de l\'envoi des rappels de rendez-vous');

        // Récupérer les rendez-vous CONFIRMÉS des prochaines 24 heures (23-25h)
        $rendezvous24h = Rendezvous::with(['patient.user', 'medecin.user', 'service'])
            ->where('statut', 'confirme') // ← SEULEMENT LES RENDEZ-VOUS CONFIRMÉS
            ->whereBetween('date_heure', [now()->addHours(23), now()->addHours(25)])
            ->orderBy('date_heure')
            ->get();

        Log::info("Nombre de rappels 24h à envoyer: " . $rendezvous24h->count());

        foreach ($rendezvous24h as $index => $rdv) {
            try {
                // Vérifier si un rappel a déjà été envoyé pour ce rendez-vous
                $dejaEnvoye = NotificationRendezVous::where('rendezvous_id', $rdv->id)
                    ->where('type_notification', 'rappel_24h')
                    ->exists();

                if (!$dejaEnvoye) {
                    // Envoyer l'email de rappel au patient
                    Mail::to($rdv->patient->user->email)
                        ->send(new RappelRendezVousMail($rdv, '24h'));

                    // Enregistrer la notification dans la base de données
                    NotificationRendezVous::create([
                        'rendezvous_id' => $rdv->id,
                        'type_notification' => 'rappel_24h',
                        'destinataire' => $rdv->patient->user->email,
                        'sujet' => 'Rappel: Rendez-vous dans 24 heures - ' . $rdv->date_heure->format('d/m/Y à H:i'),
                        'contenu' => "Rappel: Vous avez un rendez-vous demain à {$rdv->date_heure->format('H:i')} avec le Dr {$rdv->medecin->user->name}",
                        'date_envoi' => now(),
                        'statut' => 'envoye',
                    ]);

                    Log::info("Rappel 24h envoyé pour le rendez-vous ID: {$rdv->id}");
                }
            } catch (\Exception $e) {
                Log::error("Erreur lors de l'envoi du rappel 24h pour le rendez-vous ID: {$rdv->id}", [
                    'error' => $e->getMessage(),
                    'rendezvous_id' => $rdv->id
                ]);
            }
        }

        // Récupérer les rendez-vous CONFIRMÉS de la prochaine heure (55-65 minutes)
        $rendezvous1h = Rendezvous::with(['patient.user', 'medecin.user', 'service'])
            ->where('statut', 'confirme') // ← SEULEMENT LES RENDEZ-VOUS CONFIRMÉS
            ->whereBetween('date_heure', [now()->addMinutes(55), now()->addMinutes(65)])
            ->orderBy('date_heure')
            ->get();

        Log::info("Nombre de rappels 1h à envoyer: " . $rendezvous1h->count());

        foreach ($rendezvous1h as $index => $rdv) {
            try {
                // Vérifier si un rappel a déjà été envoyé pour ce rendez-vous
                $dejaEnvoye = NotificationRendezVous::where('rendezvous_id', $rdv->id)
                    ->where('type_notification', 'rappel_1h')
                    ->exists();

                if (!$dejaEnvoye) {
                    // Envoyer l'email de rappel au patient
                    Mail::to($rdv->patient->user->email)
                        ->send(new RappelRendezVousMail($rdv, '1h'));

                    // Enregistrer la notification dans la base de données
                    NotificationRendezVous::create([
                        'rendezvous_id' => $rdv->id,
                        'type_notification' => 'rappel_1h',
                        'destinataire' => $rdv->patient->user->email,
                        'sujet' => 'Rappel: Rendez-vous dans 1 heure - ' . $rdv->date_heure->format('d/m/Y à H:i'),
                        'contenu' => "Rappel: Vous avez un rendez-vous dans 1 heure à {$rdv->date_heure->format('H:i')} avec le Dr {$rdv->medecin->user->name}",
                        'date_envoi' => now(),
                        'statut' => 'envoye',
                    ]);

                    Log::info("Rappel 1h envoyé pour le rendez-vous ID: {$rdv->id}");
                }
            } catch (\Exception $e) {
                Log::error("Erreur lors de l'envoi du rappel 1h pour le rendez-vous ID: {$rdv->id}", [
                    'error' => $e->getMessage(),
                    'rendezvous_id' => $rdv->id
                ]);
            }
        }

        Log::info('Fin de l\'envoi des rappels de rendez-vous');
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Échec du job EnvoyerRappelsRendezVous', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
