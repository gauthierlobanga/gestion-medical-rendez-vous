<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationRendezVous extends Model
{
    protected $table = "notification_rendez_vous";
    use HasFactory;

    protected $fillable = [
        'rendezvous_id',
        'type_notification',
        'destinataire',
        'sujet',
        'contenu',
        'date_envoi',
        'statut',
        'erreur',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_envoi' => 'datetime',
        ];
    }

    const TYPES_NOTIFICATION = [
        'confirmation' => 'Confirmation de rendez-vous',
        'rappel_24h' => 'Rappel 24h avant',
        'rappel_1h' => 'Rappel 1h avant',
        'annulation' => 'Annulation de rendez-vous',
        'modification' => 'Modification de rendez-vous',
    ];

    const STATUTS = [
        'en_attente' => 'En attente',
        'envoye' => 'Envoyé',
        'erreur' => 'Erreur',
    ];

    public function rendezvous(): BelongsTo
    {
        return $this->belongsTo(Rendezvous::class);
    }
}
