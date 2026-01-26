<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bus extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bus';
    protected $primaryKey = 'idBus';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'immatriculation',
        'marque',
        'modele',
        'capaciteTotale',
        'statut',
        'dateMiseEnService'
    ];

    protected $casts = [
        'dateMiseEnService' => 'date',
        'capaciteTotale' => 'integer'
    ];

    // Relations
    public function sieges()
    {
        return $this->hasMany(Siege::class, 'idBus', 'idBus');
    }

    public function voyages()
    {
        return $this->hasMany(Voyage::class, 'idBus', 'idBus');
    }

    public function siegesStandard()
    {
        return $this->sieges()->where('classe', 'standard');
    }

    public function siegesVIP()
    {
        return $this->sieges()->where('classe', 'vip');
    }

    public function siegesDisponibles()
    {
        return $this->sieges()->where('statut', 'libre');
    }
}