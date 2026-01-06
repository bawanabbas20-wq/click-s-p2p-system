<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

use App\Models\User;

// Find the first user and add a phone number
$user = User::first();
if ($user) {
    $user->phone = '07701810622';
    $user->save();
    echo "Phone number '07701810622' added to user: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
} else {
    echo "No users found in database\n";
}
