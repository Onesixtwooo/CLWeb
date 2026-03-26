<?php
require __DIR__.'/vendor/autoload.php';
use Google\Client;
$clientId = '861177720356-4loerd4aqgmu0l13qc0b1u7gephkqg8r.apps.googleusercontent.com';
$clientSecret = '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';
$authCode = '4/0AfrIepBWFxrLGGseh4E_wiZPfb_HbTq32IRqsWSGSK7NB6Rdhg-axNymCuiP3nEUUFSBCQ';
$client = new Client();
$client->setClientId($clientId);
$client->setClientSecret($clientSecret);
$client->setRedirectUri('http://localhost:8080');
try {
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
    if (isset($accessToken['error'])) {
        echo "ERROR:" . json_encode($accessToken) . "\n";
    } else {
        echo "REFRESH_TOKEN:" . $accessToken['refresh_token'] . "\n";
    }
} catch (Exception $e) {
    echo "EXCEPTION:" . $e->getMessage() . "\n";
}
