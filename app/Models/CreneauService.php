<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreneauService extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'jour_semaine',
        'heure_debut',
        'heure_fin',
        'nombre_creneaux',
        'duree_creneau',
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
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
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
