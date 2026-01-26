<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$request = \Illuminate\Http\Request::capture();
$app->make(\Illuminate\Contracts\Debug\ExceptionHandler::class)->register();

$app->bootstrapWith([
    \Illuminate\Bootstrap\LoadEnvironmentVariables::class,
    \Illuminate\Bootstrap\HandleExceptions::class,
    \Illuminate\Bootstrap\RegisterFacades::class,
    \Illuminate\Bootstrap\RegisterProviders::class,
    \Illuminate\Bootstrap\BootProviders::class,
]);

echo "Session Driver: " . config('session.driver') . "\n";
$hasTable = \Illuminate\Support\Facades\Schema::hasTable('sessions');
echo "Table 'sessions' exists: " . ($hasTable ? 'YES' : 'NO') . "\n";

if (!$hasTable && config('session.driver') === 'database') {
    echo "CRITICAL: Session driver is database but table is missing!\n";
}
