<?php
// Seed temporaire pour tester la recherche
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Bus;
use App\Models\Trajet;
use App\Models\Voyage;
use App\Models\Siege;
use Illuminate\Support\Carbon;

echo "=== Seeding Test Data ===\n";

// 1. Créer ou récupérer un Trajet
$trajet = Trajet::firstOrCreate(
    ['villeDepart' => 'Douala', 'villeArrivee' => 'Yaoundé'],
    [
        'distanceKM' => 240,
        'dureeEstimee' => '05:00',
        'prixBase' => 4000
    ]
);
echo "Trajet: {$trajet->villeDepart} -> {$trajet->villeArrivee}\n";

// 2. Créer Bus Standard
$busStd = Bus::firstOrCreate(
    ['immatriculation' => 'LT-001-STD'],
    ['marque' => 'Toyota', 'modele' => 'Coaster', 'capaciteTotale' => 30, 'statut' => 'en_service', 'dateMiseEnService' => now()]
);
// Sièges Standard
if ($busStd->sieges()->count() == 0) {
    for ($i = 1; $i <= 30; $i++) {
        Siege::create([
            'idBus' => $busStd->idBus,
            'numeroSiege' => $i,
            'classe' => 'standard',
            'statut' => 'libre'
        ]);
    }
}
echo "Bus Standard: {$busStd->immatriculation}\n";

// 3. Créer Bus VIP
$busVip = Bus::firstOrCreate(
    ['immatriculation' => 'LT-002-VIP'],
    ['marque' => 'Mercedes', 'modele' => 'Sprinter VIP', 'capaciteTotale' => 20, 'statut' => 'en_service', 'dateMiseEnService' => now()]
);
// Sièges VIP
if ($busVip->sieges()->count() == 0) {
    for ($i = 1; $i <= 20; $i++) {
        Siege::create([
            'idBus' => $busVip->idBus,
            'numeroSiege' => $i,
            'classe' => 'vip', // Important pour la détection
            'statut' => 'libre'
        ]);
    }
}
echo "Bus VIP: {$busVip->immatriculation}\n";

// 4. Créer Voyages pour Aujourd'hui
$today = Carbon::today();

// Voyage Standard à 08:00
$vStd = Voyage::create([
    'idBus' => $busStd->idBus,
    'idTrajet' => $trajet->idTrajet,
    'dateHeureDepart' => $today->copy()->setHour(8)->setMinute(0),
    'dateHeureArrivee' => $today->copy()->setHour(13)->setMinute(0),
    'prixStandard' => 4500,
    'prixVIP' => 0,
    'prixActuel' => 4500,
    'placesDisponiblesTotal' => 30,
    'placesDisponibles' => 30, // Ajout du champ manquant
    'statut' => 'planifie',
    'siegesStandardDisponibles' => 30,
    'siegesVIPDisponibles' => 0
]);
echo "Voyage Standard créé à 08:00 (ID: {$vStd->idVoyage})\n";

// Voyage VIP à 10:00
$vVip = Voyage::create([
    'idBus' => $busVip->idBus,
    'idTrajet' => $trajet->idTrajet,
    'dateHeureDepart' => $today->copy()->setHour(10)->setMinute(0),
    'dateHeureArrivee' => $today->copy()->setHour(15)->setMinute(0),
    'prixStandard' => 0,
    'prixVIP' => 8000,
    'prixActuel' => 8000,
    'placesDisponiblesTotal' => 20,
    'placesDisponibles' => 20, // Ajout du champ manquant
    'statut' => 'planifie',
    'siegesStandardDisponibles' => 0,
    'siegesVIPDisponibles' => 20
]);
echo "Voyage VIP créé à 10:00 (ID: {$vVip->idVoyage})\n";

echo "=== Seeding Terminé ===\n";
