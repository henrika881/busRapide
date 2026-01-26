<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$columns = Illuminate\Support\Facades\Schema::getColumnListing('tickets');
echo "Colonnes TICKETS: " . implode(', ', $columns) . "\n";
