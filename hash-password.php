<?php

/**
 * Password Hash Generator for Laravel
 * 
 * This script generates a proper bcrypt password hash that can be used
 * directly in the database for Laravel authentication.
 * 
 * Usage: php hash-password.php
 */

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel application
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get password from command line argument or prompt
if (isset($argv[1])) {
    $password = $argv[1];
} else {
    echo "Enter password to hash: ";
    $password = trim(fgets(STDIN));
}

if (empty($password)) {
    echo "Error: Password cannot be empty.\n";
    exit(1);
}

// Generate the hash
$hash = Hash::make($password);

echo "\n";
echo "Password: " . $password . "\n";
echo "Bcrypt Hash: " . $hash . "\n";
echo "\n";
echo "You can now copy this hash and update it in phpMyAdmin:\n";
echo "UPDATE users SET password = '$hash' WHERE email = 'engAdmin@clsu.edu';\n";
echo "\n";
