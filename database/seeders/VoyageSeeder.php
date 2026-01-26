<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Voyage;

class VoyageSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Créer ou récupérer le Bus
        $idBus = DB::table('bus')->insertGetId([
            'immatriculation' => 'LT ' . rand(100, 999) . ' AA', // Aléatoire pour éviter les Duplicate Entry
            'marque' => 'Mercedes',
            'modele' => 'VIP Class',
            'capaciteTotale' => 70,
            'statut' => 'en_service',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Créer le Trajet
        $idTrajet = DB::table('trajets')->insertGetId([
            'villeDepart' => 'Douala',
            'villeArrivee' => 'Yaoundé',
            'prixBase' => 5000,
            'distance' => 250,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Créer le Voyage
        Voyage::create([
            'idBus' => $idBus,
            'idTrajet' => $idTrajet,
            'dateHeureDepart' => now()->addDays(1),
            'dateHeureArrivee' => now()->addDays(1)->addHours(4),
            'prixActuel' => 5000,
            'placesDisponibles' => 70,
            'statut' => 'planifie',
        ]);
    }
}