<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Employe extends Authenticatable
{
    use HasFactory, Notifiable,HasApiTokens;

    protected $table = 'employes';
    protected $primaryKey = 'idEmploye';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'email',
        'motDePasse',
        'poste',
        'dateEmbauche',
        'statut'
    ];

    protected $hidden = [
        'motDePasse',
        'remember_token',
    ];

    // Relations
    public function embarquements()
    {
        return $this->hasMany(Embarquement::class, 'idEmploye', 'idEmploye');
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class, 'idEmploye', 'idEmploye');
    }

    public function administrateur()
    {
        return $this->hasOne(Administrateur::class, 'idEmploye', 'idEmploye');
    }

    public function estAdministrateur()
    {
        return $this->administrateur !== null;
    }

    public function getNiveauAcces()
    {
        return $this->administrateur ? $this->administrateur->niveauAcces : null;
    }

    public function getAuthPassword()
    {
        return $this->motDePasse;
    }
}