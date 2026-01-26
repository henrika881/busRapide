<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Client;
use App\Models\Voyage;
use App\Models\Siege;
use Illuminate\Http\Request;

// On récupère un client de test
$client = Client::first();
if (!$client) {
    echo "Pas de client found.\n";
    exit;
}

// On récupère un voyage de test
$voyage = Voyage::where('statut', 'planifie')->first();
if (!$voyage) {
    echo "Pas de voyage found.\n";
    exit;
}

// On récupère des sièges de test
$sieges = Siege::where('idBus', $voyage->idBus)->take(2)->get();
if ($sieges->count() < 1) {
    echo "Pas de sièges found.\n";
    exit;
}

echo "Test avec Client ID: {$client->id_client}, Voyage ID: {$voyage->idVoyage}\n";

$data = [
    'idVoyage' => $voyage->idVoyage,
    'idSieges' => $sieges->pluck('idSiege')->toArray(),
    'classe' => 'standard',
    'modePaiement' => 'orange',
    'passagers' => [
        ['nom' => 'Test', 'prenom' => 'User', 'cni' => '123456789'],
        ['nom' => 'Autre', 'prenom' => 'Passager']
    ]
];

// Simuler l'authentification
auth()->login($client);

$request = Request::create('/api/tickets', 'POST', $data);
$request->setUserResolver(fn() => $client);

$controller = app(App\Http\Controllers\TicketController::class);
$response = $controller->store($request);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Body: " . $response->getContent() . "\n";
