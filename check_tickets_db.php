<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ticket;
use App\Models\Client;

$count = Ticket::count();
echo "Total tickets: " . $count . "\n";

$latest = Ticket::with(['client', 'voyage.trajet'])->latest()->first();
if ($latest) {
    echo "Latest Ticket:\n";
    echo "ID: " . $latest->idTicket . "\n";
    echo "Num: " . $latest->numeroBillet . "\n";
    echo "Client: " . ($latest->client->nom ?? 'N/A') . "\n";
    echo "Statut: " . $latest->statut . "\n";
    echo "QR Length: " . strlen($latest->codeQR) . "\n";
} else {
    echo "No tickets found.\n";
}
