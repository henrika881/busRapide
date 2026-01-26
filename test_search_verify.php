<?php
// Script de test pour vérifier la recherche
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Voyage;
use App\Models\Trajet;
use App\Models\Bus;
use Illuminate\Http\Request;
use App\Http\Controllers\VoyageController;

// 1. S'assurer qu'on a des données
echo "=== Vérification des données ===\n";
$voyage = Voyage::first();
if (!$voyage) {
    echo "Aucun voyage trouvé. Création de données factices...\n";
    // Créer des données si besoin (skip pour l'instant, on suppose la DB existante comme vu dans l'analyse)
} 

// 2. Simuler une recherche
echo "\n=== Test de Recherche ===\n";
$controller = new VoyageController();

// Paramètres de recherche (à adapter selon les données réelles en base)
$testRequest = new Request([
    'ville_depart' => 'Douala', 
    'ville_arrivee' => 'Yaoundé',
    'date_voyage' => now()->format('Y-m-d') 
]);

echo "Recherche: Douala -> Yaoundé pour aujourd'hui\n";

$response = $controller->search($testRequest);
$data = $response->getData(true); // true pour array

if ($data['success']) {
    echo "Succès ! " . $data['count'] . " résultats exacts, " . $data['similar_count'] . " similaires.\n";
    
    echo "\n--- Résultats ---\n";
    foreach (array_merge($data['data'], $data['similar'] ?? []) as $v) {
        echo "[{$v['type_bus']}] {$v['heure_depart']} -> {$v['heure_arrivee']} | Prix: {$v['prix']} | Classe: {$v['categorie']}\n";
    }
} else {
    echo "Erreur: " . json_encode($data) . "\n";
}
