<?php

require __DIR__.'/vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;

$clientId = '861177720356-4loerd4aqgmu0l13qc0b1u7gephkqg8r.apps.googleusercontent.com';
$clientSecret = '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';
$authCode = '4/0AfrIepBnPYUj8g_BqSA9_MgQ_i5SX7xUHg4HKtx5X_-DEowW3BPf5BPZzffgZnAVg3atjw';

$client = new Client();
$client->setClientId($clientId);
$client->setClientSecret($clientSecret);
$client->setRedirectUri('http://localhost:8080');

try {
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
    if (isset($accessToken['error'])) {
        print_r($accessToken);
    } else {
        echo "REFRESH_TOKEN:" . $accessToken['refresh_token'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
