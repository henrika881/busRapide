<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\VoyageSiege;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tickets';
    protected $primaryKey = 'idTicket';

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'numeroBillet';
    }

    protected $fillable = [
        'numeroBillet',
        'idVoyage',
        'id_client',
        'idSiege',
        'prixPaye',
        'classeBillet',
        'surcoutVIP',
        'prixBase',
        'codeQR',
        'datePaiement',
        'statut',
        'modePaiement',
        'ref_transaction', // Ajouté pour le lien avec paiement
        'promotionVIP',
        'reductionVIP'
    ];

    protected $casts = [
        'prixPaye' => 'decimal:2',
        'surcoutVIP' => 'decimal:2',
        'prixBase' => 'decimal:2',
        'datePaiement' => 'datetime',
        'promotionVIP' => 'boolean',
        'reductionVIP' => 'decimal:2'
    ];

    // Relations
    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }

    public function voyage()
    {
        return $this->belongsTo(Voyage::class, 'idVoyage', 'idVoyage')->with('trajet');
    }

    public function siege()
    {
        return $this->belongsTo(Siege::class, 'idSiege', 'idSiege');
    }

    public function embarquement()
    {
        return $this->hasOne(Embarquement::class, 'idTicket', 'idTicket');
    }

    public function paiement()
    {
        return $this->belongsTo(Paiement::class, 'ref_transaction', 'ref_transaction');
    }

    // Accessor pour compatibilité avec le dashboard
    public function getCodeBilletAttribute()
    {
        return $this->numeroBillet;
    }

    public function getPrixTotalAttribute()
    {
        return $this->prixPaye;
    }

    public function getDateReservationAttribute()
    {
        return $this->datePaiement;
    }

    // Méthodes métier
    public function genererQR()
    {
        $this->loadMissing(['voyage.trajet', 'siege']);
        
        // Utilisation de SimpleQRCode pour générer une image base64
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
            ->size(200)
            ->margin(1)
            ->generate($this->numeroBillet);
            
        $this->codeQR = base64_encode($qrCode);
        $this->save();
        
        return $this->codeQR;
    }

    public function annuler()
    {
        if (in_array($this->statut, ['en_attente', 'confirme'])) {
            $this->statut = 'annule';
            $this->save();

            if ($this->siege) {
                // Libérer la place pour ce voyage spécifique
                VoyageSiege::where('idVoyage', $this->idVoyage)
                    ->where('idSiege', $this->idSiege)
                    ->update(['statut' => 'libre', 'numeroBillet' => null]);

                // On ne touche PLUS au statut global (déjà libre normalement)
                // $this->siege->update(['statut' => 'libre']);
            }

            if ($this->voyage) {
                $this->voyage->mettreAJourDisponibilite();
            }
            return true;
        }
        return false;
    }

    public function estValide()
    {
        return $this->statut === 'confirme' && $this->voyage->dateHeureDepart->isFuture();
    }

    // Scope pour les tickets actifs
    public function scopeActifs($query)
    {
        return $query->whereIn('statut', ['confirme', 'reserve']);
    }
}