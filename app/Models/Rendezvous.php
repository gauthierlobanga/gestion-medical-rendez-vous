<?php

namespace App\Models;

use App\Events\RendezVousAnnule;
use App\Events\RendezVousConfirme;
use App\Events\RendezVousPlanifie;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rendezvous extends Model
{
    use HasFactory;

    protected $table = "rendezvous";

    protected $fillable = [
        'patient_id',
        'medecin_id',
        'service_id',
        'date_heure',
        'duree',
        'statut',
        'motif',
        'notes',
        'type_consultation',
        'prix_consultation',
        'mode_paiement',
        'est_paye',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_heure' => 'datetime',
            'prix_consultation' => 'decimal:2',
            'est_paye' => 'boolean',
        ];
    }

    const STATUTS = [
        'planifie' => 'Planifié',
        'confirme' => 'Confirmé',
        'annule' => 'Annulé',
        'termine' => 'Terminé',
        'absent' => 'Absent',
    ];

    const TYPES_CONSULTATION = [
        'premiere' => 'Première consultation',
        'suivi' => 'Consultation de suivi',
        'urgence' => 'Urgence',
        'teleconsultation' => 'Téléconsultation',
    ];

    const MODES_PAIEMENT = [
        'especes' => 'Espèces',
        'carte' => 'Carte bancaire',
        'cheque' => 'Chèque',
        'virement' => 'Virement',
        'autre' => 'Autre',
    ];

    protected static function booted()
    {
        static::created(function ($rendezvous) {
            event(new RendezVousPlanifie($rendezvous));
        });

        static::updated(function ($rendezvous) {
            if ($rendezvous->isDirty('statut')) {
                if ($rendezvous->statut === 'confirme') {
                    event(new RendezVousConfirme($rendezvous));
                } elseif ($rendezvous->statut === 'annule') {
                    event(new RendezVousAnnule($rendezvous));
                }
            }
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin(): BelongsTo
    {
        return $this->belongsTo(Medecin::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function confirmer(): void
    {
        $this->update(['statut' => 'confirme']);
    }

    public function annuler(?string $raison = null): void
    {
        $this->update([
            'statut' => 'annule',
            'notes' => $raison ? ($this->notes ? $this->notes . "\nRaison d'annulation: " . $raison : "Raison d'annulation: " . $raison) : $this->notes
        ]);
    }

    public function estConfirme(): bool
    {
        return $this->statut === 'confirme';
    }

    public function estAnnule(): bool
    {
        return $this->statut === 'annule';
    }

    public function estPlanifie(): bool
    {
        return $this->statut === 'planifie';
    }

    public function estTermine(): bool
    {
        return $this->statut === 'termine';
    }

    public function scopeProchains($query, $days = 7)
    {
        return $query->where('date_heure', '>=', now())
            ->where('date_heure', '<=', now()->addDays($days))
            ->orderBy('date_heure');
    }

    public function scopeAujourdhui($query)
    {
        return $query->whereDate('date_heure', today());
    }

    public function scopePourMedecin($query, $medecinId)
    {
        return $query->where('medecin_id', $medecinId);
    }

    public function scopePourPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function getDateAttribute()
    {
        return $this->date_heure->format('d/m/Y');
    }

    public function getHeureAttribute()
    {
        return $this->date_heure->format('H:i');
    }
}
