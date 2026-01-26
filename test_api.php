<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$request = \Illuminate\Http\Request::capture();
$app->make(\Illuminate\Contracts\Debug\ExceptionHandler::class)->register();

// Initialiser Eloquent
$app->bootstrapWith([
    \Illuminate\Bootstrap\LoadEnvironmentVariables::class,
    \Illuminate\Bootstrap\HandleExceptions::class,
    \Illuminate\Bootstrap\RegisterFacades::class,
    \Illuminate\Bootstrap\RegisterProviders::class,
    \Illuminate\Bootstrap\BootProviders::class,
]);

// Récupérer un admin et un token
$admin = \App\Models\Admin::where('email', 'gestionnaire@example.com')->first();
if ($admin) {
    $token = $admin->createToken('test', ['admin'])->plainTextToken;
    echo "✓ Admin trouvé: {$admin->email}\n";
    echo "✓ Token généré: " . substr($token, 0, 30) . "...\n";
    echo "\nCreating bus...\n";
    
    // Test curl
    $busData = [
        'immatriculation' => 'TEST-' . uniqid(),
        'marque' => 'Mercedes',
        'modele' => 'Sprinter',
        'capaciteTotale' => 50,
        'nbSiegesVIP' => 5,
        'statut' => 'en_service',
        'dateMiseEnService' => date('Y-m-d')
    ];
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'http://127.0.0.1:8000/api/admin/bus',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $token",
            "Content-Type: application/json",
            "Accept: application/json"
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($busData)
    ]);
    
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Status: $status\n";
    echo "Response: " . $response . "\n";
} else {
    echo "✗ Admin non trouvé\n";
}
