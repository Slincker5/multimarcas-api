<?php

use App\Models\User;
require __DIR__ . '/vendor/autoload.php';

$users = new User();

$lista = $users->notificarPremium();
$listaDos = [];
foreach($lista as $user) { // Corrected here
    $fechaInicio = new DateTime(); // Today's date
    $fechaFin = new DateTime($user['fin_suscripcion']); // Subscription end date from $user array
    $diferencia = $fechaInicio->diff($fechaFin); 
    if($diferencia === 3){
        array_push($listaDos, $user);
    }
}
echo json_encode($listaDos);
