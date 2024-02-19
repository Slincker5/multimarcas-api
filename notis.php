<?php

use App\Models\User;
require __DIR__ . '/vendor/autoload.php';

$users = new User();

$lista = $users->notificarPremium();
$listaDos = [];
foreach($lista as $user) { // Corrected here
    $fechaInicio = new DateTime('now'); // Today's date
    $fechaFin = new DateTime($user['fin_suscripcion']); // Subscription end date from $user array
    $diferencia = $fechaInicio->diff($fechaFin);
    echo $diferencia;
}
