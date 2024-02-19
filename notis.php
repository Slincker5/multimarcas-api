<?php

use App\Models\User;
require __DIR__ . '/vendor/autoload.php';

$users = new User();

$lista = $users->notificarPremium();
$listaDos = [];
foreach($lista as $user) {
    try {
        $fechaInicio = new DateTime(); // Fecha actual
        $fechaFin = new DateTime($user['fin_suscripcion']); // Fecha de fin de suscripción del usuario
        $diferencia = $fechaInicio->diff($fechaFin); // Diferencia entre las fechas
        if ($diferencia->days == 3 && $fechaFin > $fechaInicio) { // Verifica si faltan exactamente 3 días
            echo "Faltan " . $diferencia->days . " días para que la suscripción de " . $user['user_uuid'] . " termine.<br>"; // Muestra la diferencia en días
        }
    } catch (Exception $e) {
        echo "Excepción capturada: ",  $e->getMessage(), "<br>";
    }
}
