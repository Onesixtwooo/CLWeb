<?php

require __DIR__.'/vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;

// Instructions:
// 1. Create a project in Google Cloud Console
// 2. Enable Google Drive API
// 3. Create "OAuth 2.0 Client IDs" -> Application type: "Desktop app"
// 4. Put your Client ID and Client Secret below
// 5. Run this script: php get_refresh_token.php

$clientId = '861177720356-4loerd4aqgmu0l13qc0b1u7gephkqg8r.apps.googleusercontent.com';
$clientSecret = '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';

$client = new Client();
$client->setClientId($clientId);
$client->setClientSecret($clientSecret);
$client->setRedirectUri('http://localhost:8080');
$client->addScope(Drive::DRIVE);
$client->setAccessType('offline');
$client->setPrompt('select_account consent');

if (empty($clientId) || $clientId === 'YOUR_CLIENT_ID') {
    echo "ERROR: Please set your Client ID and Client Secret in this file before running.\n";
    exit;
}

$authUrl = $client->createAuthUrl();

echo "1. Open the following link in your browser:\n\n$authUrl\n\n";
echo "2. Authorize the application.\n";
echo "3. After authorizing, your browser will redirect to http://localhost:8080/?code=...\n";
echo "   It might say 'This site can’t be reached', THAT IS OK!\n";
echo "4. Copy the 'code' value from the browser's address bar.\n";
echo "5. Paste the Authorization Code here: ";

$authCode = trim(fgets(STDIN));

try {
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
    if (isset($accessToken['error'])) {
        throw new Exception(join(', ', $accessToken));
    }
    
    file_put_contents('refresh_token.txt', $accessToken['refresh_token']);
    echo "\n--------------------------------------------\n";
    echo "Your Refresh Token has been saved to: refresh_token.txt\n";
    echo "Please copy it from that file and update your .env\n";
    echo "--------------------------------------------\n";
} catch (Exception $e) {
    echo "Error fetching access token: " . $e->getMessage() . "\n";
}
