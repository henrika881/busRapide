<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Voyage;
use App\Models\Bus;
use App\Models\Trajet;
use App\Models\Siege;

class TestReservationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Créer un Bus (Idempotent)
        $bus = Bus::firstOrCreate(
            ['immatriculation' => 'CE 777 LT'],
            [
                'marque' => 'Mercedes',
                'modele' => 'Comfort Plus',
                'capaciteTotale' => 4, // Petit nombre pour test
                'statut' => 'en_service',
                'dateMiseEnService' => '2020-01-01'
            ]
        );

        // Créer quelques sièges s'ils n'existent pas
        if ($bus->sieges()->count() === 0) {
            Siege::create(['idBus' => $bus->idBus, 'numeroSiege' => 'S1', 'classe' => 'standard', 'statut' => 'libre']);
            Siege::create(['idBus' => $bus->idBus, 'numeroSiege' => 'S2', 'classe' => 'standard', 'statut' => 'libre']);
            Siege::create(['idBus' => $bus->idBus, 'numeroSiege' => 'VIP1', 'classe' => 'vip', 'statut' => 'libre']);
            Siege::create(['idBus' => $bus->idBus, 'numeroSiege' => 'VIP2', 'classe' => 'vip', 'statut' => 'libre']);
        }

        // 2. Créer le Trajet Douala -> Kribi (Idempotent)
        $trajet = Trajet::firstOrCreate(
            ['villeDepart' => 'Douala', 'villeArrivee' => 'Kribi'],
            [
                'dureeEstimee' => '03:00',
                'distance' => 170,
                'prixBase' => 3000,
                'prixStandard' => 3500,
                'prixVIP' => 6000
            ]
        );

        // 3. Créer les voyages s'ils n'existent pas pour cette date

        // Un autre trajet pour Yaoundé (Idempotent)
        $trajetYde = Trajet::firstOrCreate(
            ['villeDepart' => 'Douala', 'villeArrivee' => 'Yaoundé'],
            [
                'dureeEstimee' => '04:00',
                'distance' => 240,
                'prixBase' => 4000,
                'prixStandard' => 4500,
                'prixVIP' => 8000
            ]
        );

        // Créer les voyages s'ils n'existent pas pour cette date
        if (Voyage::where('idTrajet', $trajet->idTrajet)->whereDate('dateHeureDepart', '2026-01-05')->count() === 0) {
            DB::table('voyages')->insert([
                'idBus' => $bus->idBus,
                'idTrajet' => $trajet->idTrajet,
                'dateHeureDepart' => '2026-01-05 08:00:00',
                'dateHeureArrivee' => '2026-01-05 11:00:00',
                'prixStandard' => 3500,
                'prixVIP' => 6000,
                'prixActuel' => 3500,
                'siegesStandardDisponibles' => 2,
                'siegesVIPDisponibles' => 2,
                'placesDisponiblesTotal' => 4,
                'placesDisponibles' => 4,
                'statut' => 'planifie',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        if (Voyage::where('idTrajet', $trajetYde->idTrajet)->whereDate('dateHeureDepart', '2026-01-05')->count() === 0) {
            DB::table('voyages')->insert([
                'idBus' => $bus->idBus,
                'idTrajet' => $trajetYde->idTrajet,
                'dateHeureDepart' => '2026-01-05 14:00:00',
                'dateHeureArrivee' => '2026-01-05 18:00:00',
                'prixStandard' => 4500,
                'prixVIP' => 8000,
                'prixActuel' => 4500,
                'siegesStandardDisponibles' => 2,
                'siegesVIPDisponibles' => 2,
                'placesDisponiblesTotal' => 4,
                'placesDisponibles' => 4,
                'statut' => 'planifie',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
