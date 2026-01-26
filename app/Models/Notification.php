<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $primaryKey = 'idNotification';
    public $timestamps = true;

    protected $fillable = [
        'id_client',
        'idTicket',
        'idVoyage',
        'typeNotification',
        'titre',
        'contenu',
        'typeEnvoi',
        'dateLecture',
        'statut'
    ];

    protected $casts = [
        'dateEnvoi' => 'datetime',
        'dateLecture' => 'datetime'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'idTicket', 'idTicket');
    }

    public function voyage()
    {
        return $this->belongsTo(Voyage::class, 'idVoyage', 'idVoyage');
    }
}