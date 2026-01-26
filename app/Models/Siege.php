<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Siege extends Model
{
    use HasFactory;

    protected $table = 'sieges';
    protected $primaryKey = 'idSiege';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'idBus',
        'numeroSiege',
        'type',
        'classe',
        'statut',
        'estVIP',
        'surcoutVIP'
    ];

    protected $casts = [
        'estVIP' => 'boolean',
        'surcoutVIP' => 'decimal:2'
    ];

    // Relations
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'idBus', 'idBus');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'idSiege', 'idSiege');
    }

    public function reservationsTemp()
    {
        return $this->hasMany(ReservationTemp::class, 'idSiege', 'idSiege');
    }

    // Scopes
    public function scopeLibre($query)
    {
        return $query->where('statut', 'libre');
    }

    public function scopeReserve($query)
    {
        return $query->where('statut', 'reserve');
    }

    public function scopeVIP($query)
    {
        return $query->where('classe', 'vip');
    }

    public function scopeStandard($query)
    {
        return $query->where('classe', 'standard');
    }

    // Méthodes
    public function estDisponiblePourVoyage($voyageId)
    {
        // 1. Vérifier si le siège n'est pas déjà réservé/occupé pour ce voyage spécifique (via pivot)
        $pivotStatus = DB::table('voyage_siege')
            ->where('idVoyage', $voyageId)
            ->where('idSiege', $this->idSiege)
            ->first();

        if ($pivotStatus && in_array($pivotStatus->statut, ['reserve', 'occupe'])) {
            return false;
        }

        // 2. Vérifier les tickets (secours au cas où le pivot n'est pas utilisé)
        $ticketReserve = Ticket::where('idSiege', $this->idSiege)
            ->where('idVoyage', $voyageId)
            ->whereIn('statut', ['confirme', 'en_attente'])
            ->exists();

        if ($ticketReserve)
            return false;

        // 3. Vérifier les réservations temporaires
        $reservationTemp = ReservationTemp::where('idSiege', $this->idSiege)
            ->where('idVoyage', $voyageId)
            ->where('statut', 'attente')
            ->where('dateExpiration', '>', now())
            ->exists();

        if ($reservationTemp)
            return false;

        // 4. Vérifier le statut global (doit être libre physiquement)
        return $this->statut === 'libre';
    }

    public function reserver()
    {
        $this->statut = 'reserve';
        return $this->save();
    }

    public function liberer()
    {
        $this->statut = 'libre';
        return $this->save();
    }

    public function getPrixPourVoyage($voyage)
    {
        $prixBase = $this->classe === 'vip' ? $voyage->prixVIP : $voyage->prixStandard;
        return $prixBase + $this->surcoutVIP;
    }
}