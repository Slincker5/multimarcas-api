<?php

use App\Models\User;
require __DIR__ . '/vendor/autoload.php';
$users = new User();

echo json_encode($users->notificarPremium());
