<?php

use App\Models\User;
require __DIR__ . '/vendor/autoload.php';
$user = new User();

echo $user->resetAccount();