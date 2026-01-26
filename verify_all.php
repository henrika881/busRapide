<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "--- DEBUT VERIFICATION ---\n";
echo "NB TICKETS: " . \App\Models\Ticket::count() . "\n";
echo "NB PAIEMENTS: " . \App\Models\Paiement::count() . "\n";
echo "NB PASSAGERS: " . \App\Models\Passager::count() . "\n";

$lastTicket = \App\Models\Ticket::latest()->first();
if ($lastTicket) {
    echo "DERNIER TICKET:\n";
    echo "ID: " . $lastTicket->idTicket . "\n";
    echo "NUMERO: " . $lastTicket->numeroBillet . "\n";
    echo "STATUT: " . $lastTicket->statut . "\n";
    echo "DATE: " . $lastTicket->created_at . "\n";
} else {
    echo "AUCUN TICKET TROUVE.\n";
}
echo "--- FIN VERIFICATION ---\n";
