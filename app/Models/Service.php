<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'responsable_id',
        'couleur',
        'duree_rendezvous',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function medecins(): HasMany
    {
        return $this->hasMany(Medecin::class);
    }

    public function creneaux(): HasMany
    {
        return $this->hasMany(CreneauService::class);
    }

    public function responsable()
    {
        return $this->belongsTo(Medecin::class, 'responsable_id');
    }
}
