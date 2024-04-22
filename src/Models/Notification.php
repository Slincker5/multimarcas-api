<?php

namespace App\Models;
use App\Models\Database;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

class Notification extends Database
{
    public function getTokenFcmAdmin(){
        $sql = 'SELECT token_fcm FROM usuarios WHERE rol = "Admin"';
        $response = $this->ejecutarConsulta($sql, null);
        return $response->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getTokenAuth(){
        $credential = new ServiceAccountCredentials(
            "https://www.googleapis.com/auth/firebase.messaging",
            json_decode(file_get_contents("../key.json"), true)
        );
        
        return $credential->fetchAuthToken(HttpHandlerFactory::build());
    }

    public function createNotification($title = "MULTIMARCAS", $body, $link = ""){

        $token = $this->getTokenAuth();

        foreach ($this->getTokenFcmAdmin() as $user_token) {
            $ch = curl_init("https://fcm.googleapis.com/v1/projects/multimarcasapp-2fa97/messages:send");
        
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer '.$token['access_token']
            ]);
        
            curl_setopt($ch, CURLOPT_POSTFIELDS, '{
                "message": {
                  "token": "'.$user_token["token_fcm"].'",
                  "data": {
                    "title": "'. $title .'",
                    "body": "'. $body .'",
                    "link": "'. $link .'"
                  }
                }
              }');
        
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
            $response = curl_exec($ch);
        
            if ($response) {
                return "enviada " . count($this->getTokenFcmAdmin());
            } else {
                return "Hubo un error al enviar la notificaciÃ³n a un usuario.";
            }
        
            curl_close($ch);
        }
    }

}
?>