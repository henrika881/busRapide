<?php
// Script simple pour compter les enregistrements
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "📊 Statistiques de la base de données:\n";
echo str_repeat("=", 50) . "\n";
echo "Voyages: " . App\Models\Voyage::count() . "\n";
echo "Trajets: " . App\Models\Trajet::count() . "\n";
echo "Bus: " . App\Models\Bus::count() . "\n";
echo "Clients: " . App\Models\Client::count() . "\n";
echo "Tickets: " . App\Models\Ticket::count() . "\n";
echo str_repeat("=", 50) . "\n\n";

// Afficher quelques trajets disponibles
echo "📍 Trajets disponibles:\n";
$trajets = App\Models\Trajet::take(5)->get();
foreach ($trajets as $t) {
    echo "  - {$t->villeDepart} → {$t->villeArrivee} (ID: {$t->idTrajet})\n";
}

echo "\n🚌 Voyages planifiés (prochains 7 jours):\n";
$voyages = App\Models\Voyage::with('trajet')
    ->where('statut', 'planifie')
    ->where('dateHeureDepart', '>=', now())
    ->where('dateHeureDepart', '<=', now()->addDays(7))
    ->take(10)
    ->get();

if ($voyages->count() > 0) {
    foreach ($voyages as $v) {
        echo "  - {$v->trajet->villeDepart} → {$v->trajet->villeArrivee} | ";
        echo "Départ: {$v->dateHeureDepart->format('Y-m-d H:i')} | ";
        echo "Places: {$v->placesDisponiblesTotal}\n";
    }
} else {
    echo "  ⚠️  Aucun voyage planifié trouvé!\n";
}
