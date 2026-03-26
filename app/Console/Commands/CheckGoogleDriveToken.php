<?php

namespace App\Console\Commands;

use App\Services\GoogleDriveService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckGoogleDriveToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:check-token {--update : Update token from refresh_token.txt if available}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Google Drive token status and optionally update from file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking Google Drive token status...');

        $driveService = app(GoogleDriveService::class);
        
        if ($driveService->isConfigured()) {
            $this->info('✓ Google Drive service is configured and working');
            
            // Test by trying to list files
            try {
                $files = $driveService->listFiles();
                $this->info('✓ Successfully connected to Google Drive (' . count($files) . ' files found)');
            } catch (\Exception $e) {
                $this->error('✗ Connection test failed: ' . $e->getMessage());
            }
        } else {
            $this->error('✗ Google Drive service is not configured');
        }

        // Check for new token in file
        $tokenFile = base_path('refresh_token.txt');
        if (file_exists($tokenFile)) {
            $newToken = trim(file_get_contents($tokenFile));
            $currentToken = config('filesystems.disks.google.refresh_token');
            
            if ($newToken && $newToken !== $currentToken) {
                $this->warn('New refresh token found in refresh_token.txt');
                
                if ($this->option('update') || $this->confirm('Update stored token with the new one?')) {
                    $this->updateToken($newToken);
                    $this->info('✓ Token updated successfully');
                }
            } else {
                $this->info('✓ Token in file matches stored token');
            }
        }

        $this->line('');
        $this->info('To get a new token when needed:');
        $this->line('  php get_refresh_token.php');
        $this->line('');
        $this->info('To run this check manually:');
        $this->line('  php artisan google:check-token');
        $this->line('  php artisan google:check-token --update');
    }

    protected function updateToken(string $newToken): void
    {
        // Update database setting
        $setting = \App\Models\Setting::where('key', 'google_drive_refresh_token')->first();
        if ($setting) {
            $setting->update(['value' => $newToken]);
        }
        
        // Update .env file
        $envFile = base_path('.env');
        if (file_exists($envFile)) {
            $envContent = file_get_contents($envFile);
            $envContent = preg_replace(
                '/GOOGLE_DRIVE_REFRESH_TOKEN=.*/',
                'GOOGLE_DRIVE_REFRESH_TOKEN=' . $newToken,
                $envContent
            );
            file_put_contents($envFile, $envContent);
        }
        
        Log::info('Google Drive refresh token updated via command');
    }
}
