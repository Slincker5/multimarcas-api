<?php

use App\Models\Notification;
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';

$users = new User();

$lista = $users->notificarPremium();

$usersNotificados = []; // Arreglo para mantener un registro de usuarios notificados

foreach ($lista as $user) {
    try {
        $fechaInicio = new DateTime(); // Fecha actual
        $fechaFin = new DateTime($user['fin_suscripcion']); // Fecha de fin de suscripción del usuario
        $diferencia = $fechaInicio->diff($fechaFin); // Diferencia entre las fechas
        if ($diferencia->days == 2 && $fechaFin > $fechaInicio) {
            if (!in_array($user['user_uuid'], $usersNotificados)) { // Verificar si el usuario ya fue notificado
                #$instanciaNotificacion = new Notification();
                #$cuerpoNotificacion = "Prepárate para renovar y seguir disfrutando de nuestros servicios sin interrupciones.";
                #$instanciaNotificacion->crearNotificacion("🔔👀!SOLO TIENES 3 DIAS!", $cuerpoNotificacion); // Crear notificación
                #echo "Faltan " . $diferencia->days . " días para que la suscripción de " . $user['user_uuid'] . " termine.<br>"; // Muestra la diferencia en días
#
                array_push($usersNotificados, $user['user_uuid']); // Agregar el usuario al arreglo de notificados
            }
        }
    } catch (Exception $e) {
        echo "Excepción capturada: ", $e->getMessage(), "<br>";
    }
}

echo json_encode($usersNotificados);
