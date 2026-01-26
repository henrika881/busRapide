<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanVoyage extends Model
{
    use HasFactory;

    protected $table = 'plan_voyage';
    protected $primaryKey = 'id_plan';

    protected $fillable = [
        'nom_agence',
        'jour_semaine',
        'heure_depart',
        'gare_depart',
        'gare_arrivee',
        'duree_estimee',
        'prix',
    ];

    protected $casts = [
        'prix' => 'decimal:2',
    ];

    /**
     * Relation avec le modèle Agence
     */
    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class, 'nom_agence', 'nom_agence');
    }
}