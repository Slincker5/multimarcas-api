<?php

use App\Models\User;
use App\Models\Notification;

require __DIR__ . '/vendor/autoload.php';

$users = new User();
$noti = new Notification();
$titulo = "MULTIMARCAS APP";
$cuerpo = "💳 Ya puedes renovar tu suscripcion, pagando en linea";
$lista = $users->notificarPremium();
foreach($lista as $user) {
    try {
        $noti->crearNotificacionUsers($user["user_uuid"], $titulo, $cuerpo);
        echo "enviada a " . $user["username"] . "<br>";
    } catch (Exception $e) {
        echo "Excepción capturada: ",  $e->getMessage(), "<br>";
    }
}
