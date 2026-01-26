<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agence extends Model
{
    use HasFactory;

    protected $table = 'agence';
    protected $primaryKey = 'nom_agence';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nom_agence',
        'adresse',
        'telephone',
        'email',
        'date_creation',
    ];

    protected $casts = [
        'date_creation' => 'date',
    ];

    /**
     * Relation avec le modèle Employe
     */
    public function employes(): HasMany
    {
        return $this->hasMany(Employe::class, 'nom_agence', 'nom_agence');
    }

    /**
     * Relation avec le modèle Bus
     */
    public function bus(): HasMany
    {
        return $this->hasMany(Bus::class, 'nom_agence', 'nom_agence');
    }

    /**
     * Relation avec le modèle Voyage
     */
    public function voyages(): HasMany
    {
        return $this->hasMany(Voyage::class, 'nom_agence', 'nom_agence');
    }

    /**
     * Relation avec le modèle PlanVoyage
     */
    public function plansVoyage(): HasMany
    {
        return $this->hasMany(PlanVoyage::class, 'nom_agence', 'nom_agence');
    }
}