<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientVIP extends Model
{
    use HasFactory;

    protected $table = 'client_vip'; // Assumption based on standard naming or controller usage

    protected $fillable = [
        'idClient',
        'niveauVIP', // bronze, argent, or, platine
        'dateAdhesion',
        'dateRenouvellement',
        'statutAbonnement', // actif, inactif
        'prioriteEmbarquement',
        'reductionPermanente'
    ];

    protected $casts = [
        'dateAdhesion' => 'date',
        'dateRenouvellement' => 'date',
        'prioriteEmbarquement' => 'integer',
        'reductionPermanente' => 'integer'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'idClient', 'idClient');
    }
}
