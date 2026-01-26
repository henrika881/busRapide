<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Voyage extends Model
{
    use HasFactory;
    // use HasFactory, SoftDeletes;

    protected $table = 'voyages';
    protected $primaryKey = 'idVoyage';

    protected $fillable = [
        'idBus',
        'idTrajet',
        'dateHeureDepart',
        'dateHeureArrivee',
        'prixStandard',
        'prixVIP',
        'prixActuel',
        'siegesStandardDisponibles',
        'siegesVIPDisponibles',
        'placesDisponiblesTotal',
        'placesDisponibles',
        'statut'
    ];

    protected $casts = [
        'dateHeureDepart' => 'datetime',
        'dateHeureArrivee' => 'datetime',
        'prixStandard' => 'decimal:2',
        'prixVIP' => 'decimal:2',
        'prixActuel' => 'decimal:2'
    ];

    // Relations
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'idBus', 'idBus');
    }
    public function trajet()
    {
        return $this->belongsTo(Trajet::class, 'idTrajet', 'idTrajet');
    }
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'idVoyage', 'idVoyage');
    }

    // Méthodes métier
    public function estComplet()
    {
        return $this->placesDisponiblesTotal <= 0;
    }

    public function mettreAJourDisponibilite()
    {
        // On compte les sièges du bus qui n'ont pas de tickets validés pour ce voyage
        $siegesOccupes = $this->tickets()->whereIn('statut', ['confirme', 'utilise'])->pluck('idSiege')->toArray();

        $this->siegesStandardDisponibles = $this->bus->siegesStandard()
            ->whereNotIn('idSiege', $siegesOccupes)->count();

        $this->siegesVIPDisponibles = $this->bus->siegesVIP()
            ->whereNotIn('idSiege', $siegesOccupes)->count();

        $this->placesDisponiblesTotal = $this->siegesStandardDisponibles + $this->siegesVIPDisponibles;
        $this->save();
    }

    public function getOccupationRate()
    {
        if (!$this->bus || $this->bus->capaciteTotale <= 0)
            return 0;
        $placesOccupees = $this->bus->capaciteTotale - $this->placesDisponiblesTotal;
        return ($placesOccupees / $this->bus->capaciteTotale) * 100;
    }

    public function getSiegesDisponibles($classe = 'tous')
    {
        $siegesOccupes = $this->tickets()->whereIn('statut', ['confirme', 'utilise'])->pluck('idSiege')->toArray();
        $query = $this->bus->sieges()->whereNotIn('idSiege', $siegesOccupes);

        if ($classe !== 'tous' && $classe !== null) {
            $query->where('classe', $classe);
        }
        return $query->get();
    }
}