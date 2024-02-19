<?php

use App\Models\User;
require __DIR__ . '/vendor/autoload.php';

$users = new User();

$lista = $users->notificarPremium();
$listaDos = [];
foreach($lista as $user) {
    try {
        echo "Fin de suscripción: " . $user['fin_suscripcion'] . "<br>"; // Depuración
        $fechaInicio = new DateTime(); // Fecha actual
        $fechaFin = new DateTime($user['fin_suscripcion']); // Fecha de fin de suscripción del usuario
        $diferencia = $fechaInicio->diff($fechaFin); // Diferencia entre las fechas
        echo "Días restantes: " . $diferencia . " días<br>"; // Muestra la diferencia en días
    } catch (Exception $e) {
        echo "Excepción capturada: ",  $e->getMessage(), "<br>";
    }
}
