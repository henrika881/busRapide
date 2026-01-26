<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Paiement extends Model
{
    use HasFactory;

    protected $table = 'paiement';
    protected $primaryKey = 'ref_transaction';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ref_transaction',
        'id_client',
        'montant',
        'date_paiement',
        'mode_paiement',
        'statut',
        'banque',
        'numero_autorisation',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_paiement' => 'datetime',
    ];

    /**
     * Relation avec le modèle Client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }

    /**
     * Relation avec le modèle Ticket
     */
    public function ticket(): HasOne
    {
        return $this->hasOne(Ticket::class, 'ref_transaction', 'ref_transaction');
    }
}