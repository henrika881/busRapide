<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

// Initialize
$app->bootstrapWith([
    \Illuminate\Bootstrap\LoadEnvironmentVariables::class,
    \Illuminate\Bootstrap\HandleExceptions::class,
    \Illuminate\Bootstrap\RegisterFacades::class,
    \Illuminate\Bootstrap\RegisterProviders::class,
    \Illuminate\Bootstrap\BootProviders::class,
]);

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

// Run migrations
echo "Running migrations...\n";
try {
    $code = $kernel->call('migrate', ['--force' => true]);
    echo "Migration result: " . $code . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
