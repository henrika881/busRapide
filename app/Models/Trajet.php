<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trajet extends Model
{
    use HasFactory;

    protected $table = 'trajets';
    protected $primaryKey = 'idTrajet';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'villeDepart',
        'villeArrivee',
        'slug',
        'distance',
        'dureeEstimee',
        'arretsIntermediaires',
        'prixBase',
        'prixStandard',
        'prixVIP'
    ];

    /**
     * Boot the model to generate slug on saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($trajet) {
            if (empty($trajet->slug)) {
                $trajet->slug = \Illuminate\Support\Str::slug($trajet->villeDepart . '-' . $trajet->villeArrivee . '-' . uniqid());
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $casts = [
        'distance' => 'decimal:2',
        'prixBase' => 'decimal:2',
        'dureeEstimee' => 'datetime:H:i'
    ];

    // Relations
    public function voyages()
    {
        return $this->hasMany(Voyage::class, 'idTrajet', 'idTrajet');
    }

    public function bus()
    {
        return $this->hasManyThrough(Bus::class, Voyage::class, 'idTrajet', 'idBus', 'idTrajet', 'idBus');
    }

    public function tarifs()
    {
        return $this->hasMany(Tarif::class, 'idTrajet', 'idTrajet');
    }

    // Méthodes
    public function getVoyagesProchains($limit = 10)
    {
        return $this->voyages()
            ->where('dateHeureDepart', '>=', now())
            ->where('statut', 'planifie')
            ->orderBy('dateHeureDepart')
            ->limit($limit)
            ->get();
    }

    public function getTarifActuel($classe = 'standard')
    {
        $tarif = $this->tarifs()
            ->where('classe', $classe)
            ->where(function($query) {
                $query->where('dateFin', '>=', now())
                      ->orWhereNull('dateFin');
            })
            ->where('dateDebut', '<=', now())
            ->orderBy('dateDebut', 'desc')
            ->first();
            
        return $tarif ? $tarif->montant : $this->prixBase;
    }
}