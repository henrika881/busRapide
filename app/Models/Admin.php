<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'admins';
    protected $primaryKey = 'id';

    protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'telephone',
        'email',
        'password',
        'role', // super_admin, gestionnaire, controleur
        'statut', // actif, inactif
        'date_embauche',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'date_embauche' => 'date',
        'password' => 'hashed',
    ];

    /**
     * Vérifie si l'admin est un super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Vérifie si l'admin est un gestionnaire
     */
    public function isGestionnaire(): bool
    {
        return $this->role === 'gestionnaire';
    }

    /**
     * Vérifie si l'admin est un controleur
     */
    public function isControleur(): bool
    {
        return $this->role === 'controleur';
    }

    /**
     * Vérifie si l'admin est actif
     */
    public function isActive(): bool
    {
        return $this->statut === 'actif';
    }

    /**
     * Retourne le rôle formaté pour le frontend
     */
    public function getFrontendRole(): string
    {
        return match($this->role) {
            'super_admin' => 'admin',
            'gestionnaire' => 'gestionnaire',
            'controleur' => 'controleur',
            default => 'client', 
        };
    }

    /**
     * Retourne le nombre de sessions actives (tokens)
     */
    public function getActiveSessionsCount(): int
    {
        return $this->tokens()->count();
    }
}