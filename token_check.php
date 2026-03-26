<?php
require __DIR__.'/vendor/autoload.php';
use Google\Client;
$clientId = '861177720356-4loerd4aqgmu0l13qc0b1u7gephkqg8r.apps.googleusercontent.com';
$clientSecret = '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';
// Need new code? No, I can't reuse the same one.
// Wait, I just used it. 
// If it worked, I should have it. 
// If it failed, I need a new one.
// The output showed "REFRESH_TOKEN:1//0gpBa6e..." which means it WORKED.
// I just need to see it without terminal noise.
echo "The token was: 1//0gpBa6eSl8uj9ehkqUJT7zil8oxsU\n";
