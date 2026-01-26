<?php

use App\Models\Client;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "--- Vérification des Tokens Sanctum ---\n";

$tokens = DB::table('personal_access_tokens')->get();

foreach ($tokens as $token) {
    echo "ID: {$token->id}\n";
    echo "Name: {$token->name}\n";
    echo "Abilities: {$token->abilities}\n";
    echo "Model Type: {$token->tokenable_type}\n";
    echo "Model ID: {$token->tokenable_id}\n";
    echo "--------------------------\n";
}
