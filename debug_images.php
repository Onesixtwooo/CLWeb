<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\GoogleDriveService;

$service = app(GoogleDriveService::class);
$fileIds = [
    '1W7gDZTpd-dr_FJp4NyD1K5cc_RKzlfpV',
    '1Rd6gz5MwlOG9uKOFcQx5ezRtxsQNHklv',
    '1HSadaZgje01U0TN6FvBbVOKMz-Hnetf_'
];

try {
    // Get drive service
    $refl = new ReflectionClass($service);
    $prop = $refl->getProperty('service');
    $prop->setAccessible(true);
    $drive = $prop->getValue($service);

    foreach ($fileIds as $fileId) {
        echo "\nChecking File ID: $fileId\n";
        try {
            $permissions = $drive->permissions->listPermissions($fileId, ['supportsAllDrives' => true]);
            $public = false;
            foreach ($permissions->getPermissions() as $p) {
                echo "  Permission: Type={$p->getType()}, Role={$p->getRole()}\n";
                if ($p->getType() === 'anyone' && $p->getRole() === 'reader') $public = true;
            }
            echo "  Is Public? " . ($public ? "YES" : "NO") . "\n";
            
            echo "  Testing URL formats:\n";
            $urls = [
                "UC_FORMAT" => "https://drive.google.com/uc?export=view&id=" . $fileId,
                "LH3_FORMAT" => "https://lh3.googleusercontent.com/d/" . $fileId,
            ];

            foreach ($urls as $name => $url) {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_exec($ch);
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $target = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                echo "    $name: $code (Target: $target)\n";
                curl_close($ch);
            }
        } catch (Exception $e) {
            echo "  Error: " . $e->getMessage() . "\n";
        }
    }

} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}
