<?php

namespace App\Models;

use Pusher\PushNotifications\PushNotifications;

class Notification
{

    public function crearNotificacion($titulo, $cuerpo, $imagen = 'https://cintillos-plazamundo.netlify.app/android-chrome-192x192.png')
    {

        $beamsClient = new PushNotifications(array(
            "instanceId" => "b963f891-0b89-4e01-84a8-698b97373219",
            "secretKey" => "B99ABC7E749BD28709D779B4EEF1D9F3E0A1A303BC57E7A58F4F2DCCB0FE8B28",
        ));

        $data = array(
            "title" => $titulo,
            "body" => $cuerpo,
            "icon" => $imagen,
            "deep_link" => "https://cintillos-plazamundo.netlify.app",
        );

        $beamsClient->publishToInterests(
            array("2c62e966-63d8-4bfd-832e-89094ae47eec"),
            array(
                "web" => array(
                    "notification" => $data,
                ),
            )
        );

        $mensaje = [
            "status" => "ok",
            "message" => "Notificacion enviada",
        ];

        return $mensaje;

    }

    public function crearNotificacionUsers($interes, $titulo, $cuerpo, $imagen = 'https://cintillos-plazamundo.netlify.app/android-chrome-192x192.png')
        {
    
            $beamsClient = new PushNotifications(array(
                "instanceId" => "b963f891-0b89-4e01-84a8-698b97373219",
                "secretKey" => "B99ABC7E749BD28709D779B4EEF1D9F3E0A1A303BC57E7A58F4F2DCCB0FE8B28",
            ));
    
            $data = array(
                "title" => $titulo,
                "body" => $cuerpo,
                "icon" => $imagen,
                "deep_link" => "https://cintillos-plazamundo.netlify.app",
            );
    
            $beamsClient->publishToInterests(
                array($interes),
                array(
                    "web" => array(
                        "notification" => $data,
                    ),
                )
            );
    
            $mensaje = [
                "status" => "ok",
                "message" => "Notificacion enviada",
            ];
    
            return $mensaje;
    
        }
}
