<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medecin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'specialite',
        'numero_ordre',
        'tarif_consultation',
        'annees_experience',
        'diplomes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tarif_consultation' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function disponibilites(): HasMany
    {
        return $this->hasMany(DisponibiliteMedecin::class);
    }

    public function rendezvous(): HasMany
    {
        return $this->hasMany(Rendezvous::class);
    }

    public function getFullNameAttribute(): ?string
    {
        return $this->user->name ?? null;
    }

    public function getTitleAttribute(): ?string
    {
        return $this->user
            ? $this->user->name . ' - ' . $this->specialite
            : null;
    }

    /**
     * Scope pour les médecins actifs
     */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
