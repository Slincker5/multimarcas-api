<?php

namespace App\Models;

use Pusher\PushNotifications\PushNotifications;

class Notification {

    public function crearNotificacion($titulo, $cuerpo, $imagen='https://cintillos-plazamundo.netlify.app/android-chrome-192x192.png') {

        $beamsClient = new PushNotifications(array(
            "instanceId" => "90b80143-5f43-4ed9-a447-8ad08e3ca889",
            "secretKey" => "B76E6AA02AA068FB1525A42D8A7734DE2CF669DEBCCB4421DB380844762DD706",
        ));

        $data = array(
            "title" => $titulo,
            "body" => $cuerpo,
            "icon" => $imagen,
            "deep_link" => "https://cintillos-plazamundo.netlify.app"
        );

        $publishResponse = $beamsClient->publishToInterests(
            array("cintillos"),
            array(
                "web" => array(
                    "notification" => $data
                ),
            )
        );

        $mensaje = [
            "status" => "ok",
            "message" => "Notificacion enviada"
        ];

        return $mensaje;

    }

}