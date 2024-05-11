<?php

namespace App\Models;

use App\Models\Database;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

class Notification extends Database
{
    public function getTokenFcmAdmin()
    {
        $sql = 'SELECT token_fcm FROM usuarios WHERE rol = "Admin"';
        $response = $this->ejecutarConsulta($sql, null);
        return $response->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTokenFcmAll()
    {
        $sql = 'SELECT token_fcm
        FROM usuarios WHERE fin_suscripcion  > NOW() AND token_fcm IS NOT NULL';
        $response = $this->ejecutarConsulta($sql);
        return $response->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTokenFcmNow()
    {
        $sql = 'SELECT token_fcm
      FROM usuarios
      WHERE fin_suscripcion > DATE_SUB(NOW(), INTERVAL 1 MINUTE)
        AND fin_suscripcion <= NOW()
        AND token_fcm IS NOT NULL';
        $response = $this->ejecutarConsulta($sql);
        return $response->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTokenFcmPremiumEnd()
    {
        $sql = 'SELECT token_fcm
        FROM usuarios
        WHERE fin_suscripcion BETWEEN DATE_SUB(NOW(), INTERVAL 48 HOUR) AND NOW()
          AND token_fcm IS NOT NULL';
        $response = $this->ejecutarConsulta($sql);
        return $response->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTokenFcmTop()
    {
        $sql = <<<SQL
WITH CalculatedData AS (
    SELECT
        u.token_fcm,
        ROW_NUMBER() OVER (ORDER BY (
            COALESCE(rm.total_rotulos_mini, 0) +
            COALESCE(c.total_codigos, 0) +
            COALESCE(rmb.total_rotulos_mini_baja, 0)
        ) DESC) AS rank
    FROM
        usuarios u
    LEFT JOIN (
        SELECT
            user_uuid,
            COUNT(*) AS total_rotulos_mini
        FROM
            rotulos_mini
        WHERE
            fecha BETWEEN DATE_SUB(CURRENT_DATE - INTERVAL (WEEKDAY(CURRENT_DATE) + 1) DAY, INTERVAL 7 DAY)
                         AND DATE_SUB(CURRENT_DATE - INTERVAL (WEEKDAY(CURRENT_DATE) + 1) DAY, INTERVAL 1 DAY)
        GROUP BY
            user_uuid
    ) rm ON u.user_uuid = rm.user_uuid
    LEFT JOIN (
        SELECT
            user_uuid,
            COUNT(*) AS total_codigos
        FROM
            codigos
        WHERE
            fecha BETWEEN DATE_SUB(CURRENT_DATE - INTERVAL (WEEKDAY(CURRENT_DATE) + 1) DAY, INTERVAL 7 DAY)
                         AND DATE_SUB(CURRENT_DATE - INTERVAL (WEEKDAY(CURRENT_DATE) + 1) DAY, INTERVAL 1 DAY)
        GROUP BY
            user_uuid
    ) c ON u.user_uuid = c.user_uuid
    LEFT JOIN (
        SELECT
            user_uuid,
            COUNT(*) AS total_rotulos_mini_baja
        FROM
            rotulos_mini_baja
        WHERE
            fecha BETWEEN DATE_SUB(CURRENT_DATE - INTERVAL (WEEKDAY(CURRENT_DATE) + 1) DAY, INTERVAL 7 DAY)
                         AND DATE_SUB(CURRENT_DATE - INTERVAL (WEEKDAY(CURRENT_DATE) + 1) DAY, INTERVAL 1 DAY)
        GROUP BY
            user_uuid
    ) rmb ON u.user_uuid = rmb.user_uuid
)
SELECT
    token_fcm,
    rank
FROM
    CalculatedData
ORDER BY
    rank
LIMIT 5;
SQL;

        $response = $this->ejecutarConsulta($sql);
        return $response->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getTokenAuth()
    {
        $credential = new ServiceAccountCredentials(
            "https://www.googleapis.com/auth/firebase.messaging",
            json_decode(file_get_contents("../key.json"), true)
        );

        return $credential->fetchAuthToken(HttpHandlerFactory::build());
    }

    private function getTokenAuthCron()
    {
        $credential = new ServiceAccountCredentials(
            "https://www.googleapis.com/auth/firebase.messaging",
            json_decode(file_get_contents("/var/www/key.json"), true)
        );

        return $credential->fetchAuthToken(HttpHandlerFactory::build());
    }

    public function createNotification($title = "MULTIMARCAS", $body, $link = "")
    {

        $token = $this->getTokenAuth();

        foreach ($this->getTokenFcmAdmin() as $user_token) {
            $ch = curl_init("https://fcm.googleapis.com/v1/projects/multimarcasapp-2fa97/messages:send");

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token['access_token'],
            ]);

            curl_setopt($ch, CURLOPT_POSTFIELDS, '{
                "message": {
                  "token": "' . $user_token["token_fcm"] . '",
                  "data": {
                    "title": "' . $title . '",
                    "body": "' . $body . '",
                    "link": "' . $link . '"
                  }
                }
              }');

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_exec($ch);

            curl_close($ch);
        }
    }

    public function createNotificationGlobal($title = "MULTIMARCAS", $body, $link = "")
    {
        $contador = 0;
        $token = $this->getTokenAuth();

        foreach ($this->getTokenFcmAll() as $user_token) {
            $ch = curl_init("https://fcm.googleapis.com/v1/projects/multimarcasapp-2fa97/messages:send");

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token['access_token'],
            ]);

            curl_setopt($ch, CURLOPT_POSTFIELDS, '{
                "message": {
                  "token": "' . $user_token["token_fcm"] . '",
                  "data": {
                    "title": "' . $title . '",
                    "body": "' . $body . '",
                    "link": "' . $link . '"
                  }
                }
              }');

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_exec($ch);

            curl_close($ch);
            $contador++;
        }
        return "Se enviaron " . $contador . " notificaciones";
    }

    public function createNotificationPremiumEnd($title = "MULTIMARCAS", $body, $link = "")
    {
        $contador = 0;
        $token = $this->getTokenAuth();

        foreach ($this->getTokenFcmPremiumEnd() as $user_token) {
            $ch = curl_init("https://fcm.googleapis.com/v1/projects/multimarcasapp-2fa97/messages:send");

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token['access_token'],
            ]);

            curl_setopt($ch, CURLOPT_POSTFIELDS, '{
                "message": {
                  "token": "' . $user_token["token_fcm"] . '",
                  "data": {
                    "title": "' . $title . '",
                    "body": "' . $body . '",
                    "link": "' . $link . '"
                  }
                }
              }');

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_exec($ch);

            curl_close($ch);
            $contador++;
        }
        return "Se enviaron " . $contador . " notificaciones";
    }

    public function cronNotification()
    {
        if (count($this->getTokenFcmNow()) === 0) {
            return "No hay tokens para enviar notificaciones.";
        }
        $contador = 0;
        $token = $this->getTokenAuthCron();
        $title = "⏰ Suscripción Premium Finalizada";
        $body = "Tu suscripción premium ha finalizado. Renueva ahora para seguir disfrutando de todos los beneficios exclusivos.";
        $link = "";
        foreach ($this->getTokenFcmNow() as $user_token) {
            $ch = curl_init("https://fcm.googleapis.com/v1/projects/multimarcasapp-2fa97/messages:send");

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token['access_token'],
            ]);

            curl_setopt($ch, CURLOPT_POSTFIELDS, '{
              "message": {
                "token": "' . $user_token["token_fcm"] . '",
                "data": {
                  "title": "' . $title . '",
                  "body": "' . $body . '",
                  "link": "' . $link . '"
                }
              }
            }');

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_exec($ch);

            curl_close($ch);
            $contador++;
        }
        return "Se enviaron " . $contador . " notificaciones";
    }

    public function cronNotificationTop()
    {
        $contador = 0;
        $token = $this->getTokenAuthCron();
        $title = "Entraste al top semanal";
        $link = "";
        foreach ($this->getTokenFcmNow() as $user_token) {

          if($user_token["token_fcm"] === NULL){
            continue;
          }

          $body = "En hora buena!! eres el top # " . $user_token["rank"] . " de la semana.";
            $ch = curl_init("https://fcm.googleapis.com/v1/projects/multimarcasapp-2fa97/messages:send");

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token['access_token'],
            ]);

            curl_setopt($ch, CURLOPT_POSTFIELDS, '{
              "message": {
                "token": "' . $user_token["token_fcm"] . '",
                "data": {
                  "title": "' . $title . '",
                  "body": "' . $body . '",
                  "link": "' . $link . '"
                }
              }
            }');

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_exec($ch);

            curl_close($ch);
            $contador++;
        }
        return "Se enviaron " . $contador . " notificaciones";
    }
}
