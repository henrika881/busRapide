<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalOperation extends Model
{
    use HasFactory;

    protected $table = 'journal_operations';
    protected $primaryKey = 'id_operation';

    protected $fillable = [
        'type_operation',
        'user_id',
        'entite_affectee',
        'details',
        'date_operation',
        'statut_operation',
    ];

    protected $casts = [
        'date_operation' => 'datetime',
    ];

    /**
     * Relation avec le modèle User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}