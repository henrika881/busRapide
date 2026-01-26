<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrateur extends Model
{
    use HasFactory;

    protected $table = 'administrateurs';
    protected $primaryKey = 'idAdmin';
    public $timestamps = true;

    protected $fillable = [
        'idEmploye',
        'niveauAcces',
        'permissions'
    ];

    protected $casts = [
        'permissions' => 'array'
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class, 'idEmploye', 'idEmploye');
    }
}

// Model `Chatbot`



