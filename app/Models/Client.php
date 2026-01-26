<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Client extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'clients';
    protected $primaryKey = 'id_client';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'motDePasse',
        'telephone',
        'numeroCNI',
        'statut'
    ];

    protected $hidden = [
        'motDePasse',
        'remember_token',
    ];

    protected $casts = [
        'dateInscription' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->motDePasse;
    }

    // Relation avec les tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'id_client', 'id_client');
    }

    // Relation avec les paiements
    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'id_client', 'id_client');
    }

    // Relation avec les notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'id_client', 'id_client');
    }

    // Relation avec le programme VIP
    public function vip()
    {
        return $this->hasOne(ClientVIP::class, 'id_client', 'id_client');
    }

    // Relation avec les interactions chatbot
    public function interactions()
    {
        return $this->hasMany(Interaction::class, 'id_client', 'id_client');
    }

    // Relation avec les réservations temporaires
    public function reservationsTemp()
    {
        return $this->hasMany(ReservationTemp::class, 'id_client', 'id_client');
    }

    // Méthodes utiles
    public function estVIP()
    {
        return $this->vip !== null && $this->vip->statutAbonnement === 'actif';
    }

    public function getNombreTickets()
    {
        return $this->tickets()->where('statut', 'confirme')->count();
    }

    public function getMontantTotalDepense()
    {
        return $this->tickets()->where('statut', 'confirme')->sum('prixPaye');
    }
}