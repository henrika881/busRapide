<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chatbot extends Model
{
    use HasFactory;

    protected $table = 'chatbots';
    protected $primaryKey = 'idChatbot';
    public $timestamps = true;

    protected $fillable = [
        'nomChatbot',
        'version',
        'statut',
        'parametres'
    ];

    protected $casts = [
        'parametres' => 'array',
        'dateMiseAJour' => 'datetime'
    ];

    public function interactions()
    {
        return $this->hasMany(Interaction::class, 'idChatbot', 'idChatbot');
    }
}
