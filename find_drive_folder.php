<?php

require __DIR__ . '/vendor/autoload.php';

$serviceAccountPath = 'd:\htdocs\CLSU\storage\app\clsu-489108-5337e42bc031.json';

$client = new Google\Client();
$client->setAuthConfig($serviceAccountPath);
$client->addScope(Google\Service\Drive::DRIVE);

$service = new Google\Service\Drive($client);

$optParams = [
    'q' => "name = 'CLSU Web' and mimeType = 'application/vnd.google-apps.folder'",
    'fields' => 'files(id, name)'
];

try {
    $results = $service->files->listFiles($optParams);
    if (count($results->getFiles()) == 0) {
        echo "No folder found with name 'CLSU Web'.\n";
    } else {
        foreach ($results->getFiles() as $file) {
            printf("Found Folder: %s (ID: %s)\n", $file->getName(), $file->getId());
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
