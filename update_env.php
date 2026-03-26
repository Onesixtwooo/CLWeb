<?php
$token = trim(file_get_contents('refresh_token.txt'));
$envFile = '.env';
$content = file_get_contents($envFile);
$newContent = preg_replace('/GOOGLE_DRIVE_REFRESH_TOKEN=.*/', 'GOOGLE_DRIVE_REFRESH_TOKEN=' . $token, $content);
file_put_contents($envFile, $newContent);
echo "Updated .env with token from refresh_token.txt\n";
