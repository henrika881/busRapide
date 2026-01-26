<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$status = $kernel->call('migrate', ['--force' => true]);
echo "Migration Exit Code: " . $status . "\n";
echo "Output: " . \Illuminate\Support\Facades\Artisan::output() . "\n";
