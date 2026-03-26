<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "Simulating Request...\n";

// Create a mock request to the college page
$request = Illuminate\Http\Request::create('/college/veterinary-science-and-medicine', 'GET');

// Handle the request to trigger the controller logic (and our log)
$response = $kernel->handle($request);

echo "Request Handled. Status: " . $response->getStatusCode() . "\n";

// Read the log file
$logContent = file_get_contents(__DIR__ . '/storage/logs/laravel.log');
echo "\n--- LOG CONTENT ---\n";
echo $logContent;
echo "\n--- END LOG ---\n";
