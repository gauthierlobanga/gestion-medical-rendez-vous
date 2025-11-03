<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Rendezvous;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // public function index()
    // {
    //     $medecin = Auth::user()->medecin;

    //     // Rendez-vous aujourd'hui
    //     $rdvAujourdhui = Rendezvous::pourMedecin($medecin->id)
    //         ->aujourdhui()
    //         ->count();

    //     // Rendez-vous cette semaine
    //     $rdvSemaine = Rendezvous::pourMedecin($medecin->id)
    //         ->whereBetween('date_heure', [now(), now()->addDays(7)])
    //         ->count();

    //     // Patients totaux
    //     $totalPatients = Patient::whereHas('rendezvous', function ($query) use ($medecin) {
    //         $query->pourMedecin($medecin->id);
    //     })->distinct()->count();

    //     // Revenus du mois
    //     $revenusMois = Rendezvous::pourMedecin($medecin->id)
    //         ->whereMonth('date_heure', now()->month)
    //         ->whereYear('date_heure', now()->year)
    //         ->where('est_paye', true)
    //         ->sum('prix_consultation');

    //     // Prochains rendez-vous
    //     $prochainsRendezVous = Rendezvous::pourMedecin($medecin->id)
    //         ->with('patient.user')
    //         ->where('date_heure', '>=', now())
    //         ->orderBy('date_heure')
    //         ->limit(5)
    //         ->get();

    //     // Statistiques des rendez-vous
    //     $statsRendezVous = Rendezvous::pourMedecin($medecin->id)
    //         ->selectRaw('statut, count(*) as count')
    //         ->groupBy('statut')
    //         ->pluck('count', 'statut')
    //         ->toArray();

    //     $totalRendezVous = array_sum($statsRendezVous);

    //     return view('dashboard', [
    //         'rdvAujourdhui' => $rdvAujourdhui,
    //         'rdvSemaine' => $rdvSemaine,
    //         'totalPatients' => $totalPatients,
    //         'revenusMois' => $revenusMois,
    //         'prochainsRendezVous' => $prochainsRendezVous,
    //         'statsRendezVous' => $statsRendezVous,
    //         'totalRendezVous' => $totalRendezVous
    //     ]);
    // }

    public function index()
    {
        $user = Auth::user();

        // Si l'utilisateur n'a pas de médecin associé, on gère le cas
        if (!$user || !$user->medecin) {
            return view('dashboard', [
                'rdvAujourdhui' => 0,
                'rdvSemaine' => 0,
                'totalPatients' => 0,
                'revenusMois' => 0,
                'prochainsRendezVous' => [],
                'statsRendezVous' => [],
                'totalRendezVous' => 0,
            ]);
        }

        $medecin = $user->medecin;

        $rdvAujourdhui = Rendezvous::pourMedecin($medecin->id)->aujourdhui()->count();

        $rdvSemaine = Rendezvous::pourMedecin($medecin->id)
            ->whereBetween('date_heure', [now(), now()->addDays(7)])
            ->count();

        $totalPatients = Patient::whereHas('rendezvous', function ($query) use ($medecin) {
            $query->pourMedecin($medecin->id);
        })->distinct()->count();

        $revenusMois = Rendezvous::pourMedecin($medecin->id)
            ->whereMonth('date_heure', now()->month)
            ->whereYear('date_heure', now()->year)
            ->where('est_paye', true)
            ->sum('prix_consultation');

        $prochainsRendezVous = Rendezvous::pourMedecin($medecin->id)
            ->with('patient.user')
            ->where('date_heure', '>=', now())
            ->orderBy('date_heure')
            ->limit(5)
            ->get();

        $statsRendezVous = Rendezvous::pourMedecin($medecin->id)
            ->selectRaw('statut, count(*) as count')
            ->groupBy('statut')
            ->pluck('count', 'statut')
            ->toArray();

        $totalRendezVous = array_sum($statsRendezVous);

        return view('dashboard', compact(
            'rdvAujourdhui',
            'rdvSemaine',
            'totalPatients',
            'revenusMois',
            'prochainsRendezVous',
            'statsRendezVous',
            'totalRendezVous'
        ));
    }
}
