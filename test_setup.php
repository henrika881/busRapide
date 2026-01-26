<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Test 1: Vérifier que les admins existent
echo "=== Test 1: Vérifier les admins ===\n";
try {
    $admins = \App\Models\Admin::all();
    if ($admins->count() > 0) {
        echo "✓ Admins trouvés: " . $admins->count() . "\n";
        foreach ($admins as $admin) {
            echo "  - {$admin->email} ({$admin->role})\n";
        }
    } else {
        echo "✗ Aucun admin trouvé\n";
    }
} catch (\Exception $e) {
    echo "✗ Erreur: " . $e->getMessage() . "\n";
}

// Test 2: Vérifier la structure de la table 'sieges'
echo "\n=== Test 2: Vérifier la table sieges ===\n";
try {
    $columns = \Illuminate\Support\Facades\DB::getSchemaBuilder()->getColumns('sieges');
    $classeColumn = array_filter($columns, function ($col) {
        return $col['name'] === 'classe';
    });
    if (!empty($classeColumn)) {
        $col = reset($classeColumn);
        echo "✓ Colonne 'classe' trouvée\n";
        echo "  Type: " . $col['type'] . "\n";
    }
} catch (\Exception $e) {
    echo "✗ Erreur: " . $e->getMessage() . "\n";
}

// Test 3: Essayer de créer un bus
echo "\n=== Test 3: Tester la création d'un bus ===\n";
try {
    $bus = \App\Models\Bus::create([
        'immatriculation' => 'TEST-BUS-001',
        'marque' => 'Mercedes',
        'modele' => 'Sprinter',
        'capaciteTotale' => 50,
        'statut' => 'en_service',
        'dateMiseEnService' => date('Y-m-d')
    ]);
    echo "✓ Bus créé avec ID: " . $bus->idBus . "\n";
    
    // Essayer de créer des sièges
    echo "\n=== Test 4: Tester la création de sièges ===\n";
    for ($i = 1; $i <= 3; $i++) {
        \App\Models\Siege::create([
            'idBus' => $bus->idBus,
            'numeroSiege' => 'VIP-' . $i,
            'classe' => 'vip',
            'type' => 'premium',
            'statut' => 'libre'
        ]);
    }
    echo "✓ Sièges VIP créés avec succès\n";
    
    // Nettoyer
    $bus->delete();
    echo "✓ Bus test supprimé\n";
} catch (\Exception $e) {
    echo "✗ Erreur: " . $e->getMessage() . "\n";
}

echo "\n=== Tous les tests passés! ===\n";
?>
