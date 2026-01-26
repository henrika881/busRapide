<?php
$file = 'storage/logs/laravel.log';
if (!file_exists($file)) {
    echo "Log file not found at $file";
    exit;
}
$lines = array_slice(file($file), -100);
foreach ($lines as $line) {
    echo $line;
}
