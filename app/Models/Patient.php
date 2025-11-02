<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'numero_securite_sociale',
        'mutuelle',
        'numero_mutuelle',
        'antecedents_medicaux',
        'allergies',
        'traitements_chroniques',
        'informations_urgence',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rendezvous(): HasMany
    {
        return $this->hasMany(Rendezvous::class);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->user && $this->user->date_of_birth
            ? $this->user->date_of_birth->age
            : null;
    }

    public function getFullNameAttribute(): ?string
    {
        return $this->user->name ?? null;
    }
}
