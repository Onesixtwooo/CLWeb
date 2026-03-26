<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Google\Client;
use Google\Service\Drive;

try {
    $clientId = config('filesystems.disks.google.client_id');
    $clientSecret = config('filesystems.disks.google.client_secret');
    $refreshToken = config('filesystems.disks.google.refresh_token');

    echo "Client ID: " . substr($clientId, 0, 10) . "...\n";
    echo "Refresh Token: " . substr($refreshToken, 0, 10) . "...\n";

    $client = new Client();
    $client->setClientId($clientId);
    $client->setClientSecret($clientSecret);
    $client->addScope(Drive::DRIVE);
    
    // Attempt to refresh
    echo "Attempting to fetch access token with refresh token...\n";
    $token = $client->fetchAccessTokenWithRefreshToken($refreshToken);
    
    if (isset($token['error'])) {
        echo "Error refreshing token: " . json_encode($token) . "\n";
    } else {
        echo "Successfully fetched access token!\n";
        $service = new Drive($client);
        $files = $service->files->listFiles(['pageSize' => 1]);
        echo "Successfully listed files! Drive API is working.\n";
    }

} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
