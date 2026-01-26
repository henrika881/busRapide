<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bagage extends Model
{
    use HasFactory;

    protected $table = 'bagage';
    protected $primaryKey = 'tag_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tag_id',
        'numero_billet',
        'poids',
        'type_bagage',
        'dimensions',
        'statut',
        'date_enregistrement',
    ];

    protected $casts = [
        'poids' => 'decimal:2',
        'date_enregistrement' => 'datetime',
    ];

    /**
     * Relation avec le modèle Ticket
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'numero_billet', 'numero_billet');
    }
}