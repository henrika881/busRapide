<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoyageSiege extends Model
{
    use HasFactory;

    protected $table = 'voyage_siege';
    protected $primaryKey = ['idVoyage', 'idSiege'];
    public $incrementing = false;

    protected $fillable = [
        'idVoyage',
        'idSiege',
        'numeroBillet',
        'statut',
    ];

    /**
     * Indique que la clé primaire est composite
     */
    protected function setKeysForSaveQuery($query)
    {
        return $query->where('idVoyage', $this->getAttribute('idVoyage'))
            ->where('idSiege', $this->getAttribute('idSiege'));
    }

    /**
     * Relation avec le modèle Voyage
     */
    public function voyage(): BelongsTo
    {
        return $this->belongsTo(Voyage::class, 'idVoyage', 'idVoyage');
    }

    /**
     * Relation avec le modèle Siege
     */
    public function siege(): BelongsTo
    {
        return $this->belongsTo(Siege::class, 'idSiege', 'idSiege');
    }

    /**
     * Relation avec le modèle Ticket
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'numeroBillet', 'numeroBillet');
    }
}