<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\VoyageSiege;

class Embarquement extends Model
{
    use HasFactory;

    protected $table = 'embarquements';
    protected $primaryKey = 'idEmbarquement';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'idTicket',
        'admin_id',
        'porteEmbarquement',
        'statut',
        'commentaire',
        'dateHeureValidation'
    ];

    protected $casts = [
        'dateHeureValidation' => 'datetime'
    ];

    // Relations
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'idTicket', 'idTicket');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    // Scopes
    public function scopeValide($query)
    {
        return $query->where('statut', 'valide');
    }

    public function scopeAujourdhui($query)
    {
        return $query->whereDate('dateHeureValidation', today());
    }

    // Méthodes
    public function valider()
    {
        $this->statut = 'valide';
        $this->dateHeureValidation = now();

        if ($this->save()) {
            // Mettre à jour le statut du ticket
            $this->ticket->update(['statut' => 'utilise']);

            // Mise à jour de la disponibilité dans la table pivot (voyage-spécifique)
            VoyageSiege::where('idVoyage', $this->ticket->idVoyage)
                ->where('idSiege', $this->ticket->idSiege)
                ->update(['statut' => 'occupe']);

            // On ne touche PLUS au statut global du siège
            // $this->ticket->siege->statut = 'occupe';
            // $this->ticket->siege->save();

            return true;
        }

        return false;
    }

    public function refuser($commentaire = null)
    {
        $this->statut = 'refuse';
        $this->commentaire = $commentaire;
        return $this->save();
    }
}