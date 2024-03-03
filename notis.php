<?php

use App\Models\User;
use App\Models\Notification;

require __DIR__ . '/vendor/autoload.php';

$users = new User();

$lista = $users->notificarPremium();
foreach($lista as $user) {
    try {
        var_dump($user["username"]);
    } catch (Exception $e) {
        echo "Excepción capturada: ",  $e->getMessage(), "<br>";
    }
}
