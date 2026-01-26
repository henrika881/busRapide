<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$app->bootstrapWith([
    \Illuminate\Bootstrap\LoadEnvironmentVariables::class,
    \Illuminate\Bootstrap\HandleExceptions::class,
    \Illuminate\Bootstrap\RegisterFacades::class,
    \Illuminate\Bootstrap\RegisterProviders::class,
    \Illuminate\Bootstrap\BootProviders::class,
]);

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== Vérification table trajets ===\n";

if (!Schema::hasColumn('trajets', 'prixStandard')) {
    echo "✗ Colonne prixStandard manquante\n";
    echo "Ajout en cours...\n";
    try {
        Schema::table('trajets', function ($table) {
            $table->decimal('prixStandard', 10, 2)->nullable()->after('prixBase');
            $table->decimal('prixVIP', 10, 2)->nullable()->after('prixStandard');
        });
        echo "✓ Colonnes ajoutées\n";
    } catch (\Exception $e) {
        echo "✗ Erreur: " . $e->getMessage() . "\n";
    }
} else {
    echo "✓ Colonne prixStandard existe\n";
}

// Vérifier la structure
echo "\nStructure table trajets:\n";
$columns = DB::select("SHOW COLUMNS FROM trajets");
foreach ($columns as $col) {
    echo "  - {$col->Field} ({$col->Type})\n";
}

echo "\n✓ Test terminé\n";
