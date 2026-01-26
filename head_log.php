<?php
$file = 'storage/logs/laravel.log';
if (!file_exists($file)) {
    echo "Log file not found.";
    exit;
}
$lines = file($file);
for ($i = 0; $i < min(20, count($lines)); $i++) {
    echo $lines[$i];
}
