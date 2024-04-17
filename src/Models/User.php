<?php

namespace App\Models;

use App\Models\Database;
use App\Models\Premiun;
use App\Models\Notification;
use Firebase\JWT\JWT;

class User extends Database
{
    private $response;
    private $user_uuid;
    private $key = "georginalissethyvladi";
    public $instanciaPremium;
    private $routePhotoProfile;
    private $allowedTypes;
    public $instanciaNotificacion;

    public function __construct($user_uuid = "")
    {
        $this->user_uuid = $user_uuid;
        $this->routePhotoProfile = "/var/www/multimarcas-api/public/perfiles/";
        $this->allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $this->instanciaPremium = new Premiun($user_uuid);
        $this->instanciaNotificacion = new Notification();
    }

    private function datosUsuario()
    {
        $sql = 'SELECT user_uuid, username, nombre, apellido, telefono, email, photo, rol, fecha, fin_suscripcion FROM usuarios WHERE user_uuid = ?';
        $response = $this->ejecutarConsulta($sql, [$this->user_uuid]);
        $datos = $response->fetchAll(\PDO::FETCH_ASSOC);
        return $datos;
    }

    private function totalCintillos()
    {
        $sql = 'SELECT COUNT(*) AS conteo FROM codigos WHERE  user_uuid = ?';
        $response = $this->ejecutarConsulta($sql, [$this->user_uuid]);
        $datos = $response->fetchAll(\PDO::FETCH_ASSOC);
        return $datos[0]['conteo'];
    }

    private function totalCintillosGenerados()
    {
        $sql = 'SELECT COUNT(*) AS conteo FROM codigos WHERE user_uuid = ? AND path_uuid IS NOT NULL';
        $response = $this->ejecutarConsulta($sql, [$this->user_uuid]);
        $datos = $response->fetchAll(\PDO::FETCH_ASSOC);
        return $datos[0]['conteo'];
    }

    private function totalRotulos()
    {
        $sql = 'SELECT COUNT(*) AS conteo FROM rotulos WHERE user_uuid = ?';
        $response = $this->ejecutarConsulta($sql, [$this->user_uuid]);
        $datos = $response->fetchAll(\PDO::FETCH_ASSOC);
        return $datos[0]['conteo'];
    }

    private function totalRotulosGenerados()
    {
        $sql = 'SELECT COUNT(*) AS conteo FROM rotulos WHERE user_uuid = ? AND path_uuid IS NOT NULL';
        $response = $this->ejecutarConsulta($sql, [$this->user_uuid]);
        $datos = $response->fetchAll(\PDO::FETCH_ASSOC);
        return $datos[0]['conteo'];
    }

    public function verEstado()
    {
        $sql = 'SELECT mvc FROM usuarios WHERE  user_uuid = ?';
        $response = $this->ejecutarConsulta($sql, [$this->user_uuid]);
        $datos = $response->fetchAll(\PDO::FETCH_ASSOC);
        return $datos;
    }

    public function updateToken()
    {
        $sql = 'UPDATE usuarios SET mvc = ? WHERE user_uuid = ?';
        $response = $this->ejecutarConsulta($sql, ['si', $this->user_uuid]);
    }

    public function estadisticasGlobal()
    {
        $this->instanciaPremium->validarSuscripcion();
        $stats = [
            "profile" => $this->datosUsuario(),
            "totalCintillos" => $this->totalCintillos(),
            "totalCintillosGenerados" => $this->totalCintillosGenerados(),
            "totalRotulos" => $this->totalRotulos(),
            "totalRotulosGenerados" => $this->totalRotulosGenerados(),
        ];
        return $stats;
    }

    private function guardarRutaImagen($ruta){
        $sql = 'UPDATE usuarios SET photo = ? WHERE user_uuid = ?';
        $this->ejecutarConsulta($sql, [$ruta , $this->user_uuid]);
    }

    private function comprimirImagenJPEG($rutaOriginal, $rutaGuardado, $calidad)
    {
        $imagen = imagecreatefromjpeg($rutaOriginal);
        imagejpeg($imagen, $rutaGuardado, $calidad);
        imagedestroy($imagen);
        unlink($rutaOriginal);
    }

    public function uploadPhoto($uploadedFile, $fileType, $fileSize)
    {
        $maxFileSize = 6 * 1024 * 1024;
        if (in_array($fileType, $this->allowedTypes)) {
            if ($fileSize < $maxFileSize) {
                $userDirectory = $this->routePhotoProfile . DIRECTORY_SEPARATOR . $this->user_uuid;
                if (!file_exists($userDirectory)) {
                    mkdir($userDirectory, 0755, true);
                }
                $imageUuid = uniqid();
                $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
                $filename = $imageUuid . '.' . $extension;
                $completePath = $userDirectory . DIRECTORY_SEPARATOR . $filename;
                $temporaryPath = $userDirectory . DIRECTORY_SEPARATOR . 'temp_' . $filename;
                $pathDatabase = 'https://api.multimarcas.app/public/perfiles/' . $this->user_uuid . '/' . $filename;
                $uploadedFile->moveTo($temporaryPath);
                if ($extension === 'jpg' || $extension === 'jpeg') {
                    $this->comprimirImagenJPEG($temporaryPath, $completePath, 40);
                    $this->guardarRutaImagen($pathDatabase);
                    $cuerpoNotificacion = "Ha subido nueva foto de perfil";
                    $this->instanciaNotificacion->crearNotificacion("MULTIMARCAS APP", $cuerpoNotificacion);
                } else {
                    rename($temporaryPath, $completePath);
                }

                
                return $filename;
            } else {
                $this->response['status'] = 'error';
                $this->response['message'] = 'El temaño de la imagen excede el limite, 6 MB';
                return $this->response;
            }

        } else {
            $this->response['status'] = 'error';
            $this->response['message'] = 'Formato de imagen no valida.';
            return $this->response;
        }

    }

    public function notificarPremium()
    {
        $sql = "SELECT * FROM usuarios where fin_suscripcion < CURRENT_DATE";
        $response = $this->ejecutarConsulta($sql);
        $datos = $response->fetchAll(\PDO::FETCH_ASSOC);
        return $datos;
    }

    public function generatedToken()
    {
        $datos = $this->datosUsuario();
        $nombreCompleto = $datos[0]["nombre"] . " " . $datos[0]["apellido"];
        $payload = array(
            "iss" => "multimarcas",
            "aud" => $this->user_uuid,
            "iat" => time(),
            "nbf" => time(),
            "data" => array(
                "user_uuid" => $datos[0]["user_uuid"],
                "username" => $datos[0]["username"] === null ? $nombreCompleto : $datos[0]["username"],
                "email" => $datos[0]["email"],
                "photo" => $datos[0]["photo"],
                "rol" => $datos[0]["rol"],
                "suscripcion" => $datos[0]["suscripcion"],
                "fin_suscripcion" => $datos[0]["fin_suscripcion"],

            ),
        );
        $alg = "HS256";
        $token = JWT::encode($payload, $this->key, $alg);
        $this->response['status'] = 'OK';
        $this->response['message'] = 'token generado con exito.';
        $this->response['token'] = $token;
        return $this->response;
    }

    public function  updateTokenNotification($token){
        $sql = "UPDATE usuarios SET token_fcm = ? WHERE user_uuid = ?";
        $response = $this->ejecutarConsulta($sql, [$token, $this->user_uuid]);
        $this->response['status'] = 'OK';
        $this->response['message'] = 'token_fcm actualizado';
        return $this->response;
    }
}
