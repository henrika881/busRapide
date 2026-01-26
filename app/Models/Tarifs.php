<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarifs extends Model
{
    use HasFactory;

    protected $table = 'tarifs';
    protected $primaryKey = 'idTarif';
    public $timestamps = true;

    protected $fillable = [
        'idTrajet',
        'classe',
        'typeTarif',
        'montant',
        'dateDebut',
        'dateFin',
        'conditions',
        'avantagesVIP',
        'prioriteEmbarquement'
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'dateDebut' => 'date',
        'dateFin' => 'date',
        'avantagesVIP' => 'array',
        'prioriteEmbarquement' => 'integer'
    ];

    public function trajet()
    {
        return $this->belongsTo(Trajet::class, 'idTrajet', 'idTrajet');
    }
}
