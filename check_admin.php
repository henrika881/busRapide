<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');

// Boot the application
$app->make('Illuminate\Contracts\Http\Kernel');

// Now we can access models
$admin = \App\Models\Admin::where('email', 'gestionnaire@example.com')->first();

if ($admin) {
    echo "Admin found: " . $admin->nom . " " . $admin->prenom . "\n";
    echo "Role: " . $admin->role . "\n";
    echo "Statut: " . $admin->statut . "\n";
    echo "ID: " . $admin->id . "\n";
} else {
    echo "Admin not found\n";
    echo "Available admins:\n";
    $admins = \App\Models\Admin::all();
    foreach ($admins as $a) {
        echo "- " . $a->email . " (" . $a->role . ")\n";
    }
}
?>
