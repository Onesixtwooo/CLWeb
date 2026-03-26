@echo off
title CLSU Google Drive Setup
color 0A

echo ============================================================
echo       CLSU - Google Drive Image Storage Setup
echo ============================================================
echo.
echo This script will guide you through setting up Google Drive
echo as your image storage backend.
echo.
echo ============================================================
echo  PREREQUISITES (do these first!)
echo ============================================================
echo.
echo  1. Go to https://console.cloud.google.com/
echo  2. Create a new project (or select an existing one)
echo  3. Enable the "Google Drive API":
echo     - Go to APIs ^& Services ^> Library
echo     - Search "Google Drive API" and click Enable
echo  4. Create OAuth 2.0 Credentials:
echo     - Go to APIs ^& Services ^> Credentials
echo     - Click "Create Credentials" ^> "OAuth client ID"
echo     - Application type: "Desktop app"
echo     - Download or copy the Client ID and Client Secret
echo  5. Create a folder in Google Drive for storing images
echo     - Open Google Drive, create a new folder (e.g. "CLSU Images")
echo     - Open it, copy the folder ID from the URL:
echo       https://drive.google.com/drive/folders/XXXXXXXXX
echo       The "XXXXXXXXX" part is your Folder ID
echo.
echo ============================================================
echo.
pause

echo.
echo ============================================================
echo  STEP 1: Install Google API Client
echo ============================================================
echo.
echo Installing google/apiclient via Composer...
echo.
call composer require google/apiclient:^2.19
if errorlevel 1 (
    echo.
    echo [ERROR] Composer install failed. Make sure Composer is installed.
    echo Download from: https://getcomposer.org/download/
    pause
    exit /b 1
)

echo.
echo [OK] Google API Client installed successfully!
echo.

echo ============================================================
echo  STEP 2: Enter your Google Drive credentials
echo ============================================================
echo.

set /p CLIENT_ID="Enter your Google Drive Client ID: "
set /p CLIENT_SECRET="Enter your Google Drive Client Secret: "
set /p FOLDER_ID="Enter your Google Drive Folder ID: "

echo.
echo ============================================================
echo  STEP 3: Get Refresh Token
echo ============================================================
echo.
echo We need to get a Refresh Token by authorizing with Google.
echo A browser will open - sign in and authorize the app.
echo.
echo After authorizing, you'll be redirected to a localhost URL.
echo It will show "This site can't be reached" - THAT'S OK!
echo.
echo Copy the FULL URL from the address bar and paste it when asked.
echo.
pause

php -r "require 'vendor/autoload.php'; $c = new Google\Client(); $c->setClientId('%CLIENT_ID%'); $c->setClientSecret('%CLIENT_SECRET%'); $c->setRedirectUri('http://localhost:8080'); $c->addScope(Google\Service\Drive::DRIVE); $c->setAccessType('offline'); $c->setPrompt('select_account consent'); echo PHP_EOL . 'Open this URL in your browser:' . PHP_EOL . PHP_EOL . $c->createAuthUrl() . PHP_EOL . PHP_EOL;"

echo.
echo After authorizing, your browser will redirect to something like:
echo   http://localhost:8080/?code=4/0AXXXXXXXXX...
echo.
echo Copy ONLY the code value (everything after "code=" and before any "&").
echo.

set /p AUTH_CODE="Paste the authorization code here: "

echo.
echo Exchanging code for refresh token...
echo.

php -r "require 'vendor/autoload.php'; $c = new Google\Client(); $c->setClientId('%CLIENT_ID%'); $c->setClientSecret('%CLIENT_SECRET%'); $c->setRedirectUri('http://localhost:8080'); $t = $c->fetchAccessTokenWithAuthCode('%AUTH_CODE%'); if(isset($t['error'])){echo '[ERROR] '.$t['error_description'].PHP_EOL;exit(1);} echo '[OK] Refresh Token: ' . $t['refresh_token'] . PHP_EOL; file_put_contents('refresh_token.txt', $t['refresh_token']);"

if errorlevel 1 (
    echo.
    echo [ERROR] Failed to get refresh token. Please try again.
    pause
    exit /b 1
)

set /p REFRESH_TOKEN=<refresh_token.txt

echo.
echo ============================================================
echo  STEP 4: Updating .env file
echo ============================================================
echo.

:: Use PHP to safely update .env
php -r "
$env = file_get_contents('.env');

$updates = [
    'FILESYSTEM_DISK' => 'google',
    'GOOGLE_DRIVE_CLIENT_ID' => '%CLIENT_ID%',
    'GOOGLE_DRIVE_CLIENT_SECRET' => '%CLIENT_SECRET%',
    'GOOGLE_DRIVE_REFRESH_TOKEN' => '%REFRESH_TOKEN%',
    'GOOGLE_DRIVE_FOLDER_ID' => '%FOLDER_ID%',
];

foreach ($updates as $key => $value) {
    if (preg_match('/^' . preg_quote($key, '/') . '=.*/m', $env)) {
        $env = preg_replace('/^' . preg_quote($key, '/') . '=.*/m', $key . '=' . $value, $env);
    } else {
        $env .= PHP_EOL . $key . '=' . $value;
    }
}

file_put_contents('.env', $env);
echo '[OK] .env file updated successfully!' . PHP_EOL;
"

echo.
echo ============================================================
echo  STEP 5: Testing Connection
echo ============================================================
echo.
echo Testing Google Drive connection...
echo.

php -r "
require 'vendor/autoload.php';
$c = new Google\Client();
$c->setClientId('%CLIENT_ID%');
$c->setClientSecret('%CLIENT_SECRET%');
$c->refreshToken('%REFRESH_TOKEN%');
$c->addScope(Google\Service\Drive::DRIVE);
$drive = new Google\Service\Drive($c);

try {
    $results = $drive->files->listFiles([
        'q' => \"'%FOLDER_ID%' in parents and trashed = false\",
        'pageSize' => 5,
        'fields' => 'files(id, name)',
    ]);
    echo '[OK] Successfully connected to Google Drive!' . PHP_EOL;
    echo 'Folder contains ' . count($results->getFiles()) . ' files.' . PHP_EOL;
} catch (Exception $e) {
    echo '[ERROR] Connection failed: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
"

echo.
echo ============================================================
echo  STEP 6: Clear Laravel config cache
echo ============================================================
echo.

php artisan config:clear

echo.
echo ============================================================
echo.
echo  [SUCCESS] Google Drive setup is complete!
echo.
echo  Your images will now be stored in Google Drive.
echo  The following .env values have been set:
echo.
echo    FILESYSTEM_DISK=google
echo    GOOGLE_DRIVE_CLIENT_ID=%CLIENT_ID%
echo    GOOGLE_DRIVE_CLIENT_SECRET=***hidden***
echo    GOOGLE_DRIVE_REFRESH_TOKEN=***hidden***
echo    GOOGLE_DRIVE_FOLDER_ID=%FOLDER_ID%
echo.
echo ============================================================
echo.
pause
