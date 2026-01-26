<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Passager extends Model
{
    use HasFactory;

    protected $table = 'passager';
    protected $primaryKey = 'id_passager';

    protected $fillable = [
        'idTicket',
        'nom_passager',
        'prenom_passager',
        'date_naissance',
        'type_piece',
        'numero_piece',
        'telephone_passager',
        'email_passager',
        'numero_billet',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    /**
     * Relation avec le modèle Ticket
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'numero_billet', 'numero_billet');
    }
}