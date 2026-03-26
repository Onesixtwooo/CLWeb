<?php
/**
 * Google Drive Token Maintenance Script
 *
 * This script can be run as a cron job to automatically check and update
 * Google Drive refresh tokens when they expire.
 *
 * Usage:
 * - As a cron job: php google-drive-maintenance.php
 * - Manual run: php google-drive-maintenance.php
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\GoogleDriveService;
use Illuminate\Support\Facades\Log;

echo "Google Drive Token Maintenance - " . date('Y-m-d H:i:s') . "\n";
echo "================================================\n";

$driveService = app(GoogleDriveService::class);

if ($driveService->isConfigured()) {
    echo "✓ Google Drive service is configured\n";

    // Test connection
    try {
        $files = $driveService->listFiles();
        echo "✓ Successfully connected to Google Drive (" . count($files) . " files found)\n";
        Log::info('Google Drive maintenance check passed');
    } catch (\Exception $e) {
        echo "✗ Connection test failed: " . $e->getMessage() . "\n";
        Log::error('Google Drive maintenance check failed: ' . $e->getMessage());
    }
} else {
    echo "✗ Google Drive service is not configured\n";
    Log::warning('Google Drive service not configured during maintenance check');
}

// Check for new token in file
$tokenFile = __DIR__ . '/refresh_token.txt';
if (file_exists($tokenFile)) {
    $newToken = trim(file_get_contents($tokenFile));
    $currentToken = config('filesystems.disks.google.refresh_token');

    if ($newToken && $newToken !== $currentToken) {
        echo "⚠ New refresh token found in refresh_token.txt\n";

        // Auto-update the token
        try {
            // Update database setting
            $setting = \App\Models\Setting::where('key', 'google_drive_refresh_token')->first();
            if ($setting) {
                $setting->update(['value' => $newToken]);
            }

            // Update .env file
            $envFile = __DIR__ . '/.env';
            if (file_exists($envFile)) {
                $envContent = file_get_contents($envFile);
                $envContent = preg_replace(
                    '/GOOGLE_DRIVE_REFRESH_TOKEN=.*/',
                    'GOOGLE_DRIVE_REFRESH_TOKEN=' . $newToken,
                    $envContent
                );
                file_put_contents($envFile, $envContent);
            }

            echo "✓ Token automatically updated\n";
            Log::info('Google Drive refresh token automatically updated via maintenance script');

            // Clear config cache to pick up new token
            \Illuminate\Support\Facades\Artisan::call('config:clear');

        } catch (\Exception $e) {
            echo "✗ Failed to update token: " . $e->getMessage() . "\n";
            Log::error('Failed to auto-update Google Drive token: ' . $e->getMessage());
        }
    } else {
        echo "✓ Token is up to date\n";
    }
}

echo "\nMaintenance check completed.\n";
echo "If you need a new token, run: php get_refresh_token.php\n";