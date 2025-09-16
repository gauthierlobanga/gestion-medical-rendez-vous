<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisponibiliteMedecin extends Model
{
    use HasFactory;

    protected $fillable = [
        'medecin_id',
        'jour_semaine',
        'heure_debut',
        'heure_fin',
        'date_specifique',
        'est_exception',
        'raison_exception',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'heure_debut' => 'datetime:H:i',
            'heure_fin' => 'datetime:H:i',
            'date_specifique' => 'date',
            'est_exception' => 'boolean',
        ];
    }

    public function medecin(): BelongsTo
    {
        return $this->belongsTo(Medecin::class);
    }

    public function getJourSemaineNameAttribute(): string
    {
        $jours = [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche',
        ];

        return $jours[$this->jour_semaine] ?? 'Inconnu';
    }
}
