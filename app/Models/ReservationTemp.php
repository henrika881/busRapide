<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationTemp extends Model
{
    use HasFactory;

    protected $table = 'reservation_temps';
    protected $primaryKey = 'idReservationTemp';

    protected $fillable = [
        'idClient',
        'idVoyage',
        'idSiege',
        'dateExpiration',
        'statut'
    ];

    protected $casts = [
        'dateExpiration' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'idClient', 'idClient');
    }

    public function voyage()
    {
        return $this->belongsTo(Voyage::class, 'idVoyage', 'idVoyage');
    }

    public function siege()
    {
        return $this->belongsTo(Siege::class, 'idSiege', 'idSiege');
    }

    public function scopeActive($query)
    {
        return $query->where('statut', 'attente')
            ->where('dateExpiration', '>', now());
    }
}
