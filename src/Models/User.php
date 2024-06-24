<?php

namespace App\Models;

use App\Models\Database;
use Firebase\JWT\JWT;
use App\Models\Notification;

class User extends Database
{
    private $response;
    private $user_uuid;
    public $instanciaNotificacion;
    private $key = "georginalissethyvladi";
    private $routePhotoProfile;
    private $allowedTypes;
    private $nombres = '/^[a-zA-ZñÑáéíóúÁÉÍÓÚ]+(?:\s[a-zA-ZñÑáéíóúÁÉÍÓÚ]+){0,2}$/';
    private $telefonoRegex = '/^\d{8}$/';


    public function __construct($user_uuid = "")
    {
        $this->user_uuid = $user_uuid;
        $this->routePhotoProfile = "/var/www/multimarcas-api/public/perfiles/";
        $this->allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $this->instanciaNotificacion = new Notification();
    }

    private function datosUsuario()
    {
        $sql = 'SELECT user_uuid, username, nombre, apellido, telefono, email, photo, rol, fecha, suscripcion, fin_suscripcion, token_fcm_static FROM usuarios WHERE user_uuid = ?';
        $response = $this->ejecutarConsulta($sql, [$this->user_uuid]);
        $datos = $response->fetchAll(\PDO::FETCH_ASSOC);
        return $datos;
    }

    private function totalCintillosGenerados()
    {
        $sql = 'SELECT COUNT(*) AS conteo FROM codigos WHERE user_uuid = ? AND path_uuid IS NOT NULL';
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

    public function estadisticasGlobal()
    {
        $stats = [
            "profile" => $this->datosUsuario(),
            "totalCintillosGenerados" => $this->totalCintillosGenerados(),
            "totalRotulosGenerados" => $this->totalRotulosGenerados(),
        ];
        return $stats;
    }

    private function guardarRutaImagen($ruta)
    {
        $sql = 'UPDATE usuarios SET photo = ? WHERE user_uuid = ?';
        $this->ejecutarConsulta($sql, [$ruta, $this->user_uuid]);
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
                } else {
                    rename($temporaryPath, $completePath);
                    $this->guardarRutaImagen($pathDatabase);
                }
                $cuerpoNotificacion = "Se ha cargado nueva foto";
                $this->instanciaNotificacion->createNotification("FOTO DE PERFIL", $cuerpoNotificacion);
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

    public function updateTokenNotification($token)
    {
        $sql = "UPDATE usuarios SET token_fcm = ? WHERE user_uuid = ?";
        $this->ejecutarConsulta($sql, [$token, $this->user_uuid]);
        $this->response['status'] = 'OK';
        $this->response['message'] = 'token_fcm actualizado';
        return $this->response;
    }

    public function getTopAll()
    {
        $sql = "
SELECT 
    u.user_uuid, 
    u.username,
    u.nombre,
    u.apellido,
    u.photo,
    u.fin_suscripcion,
    u.suscripcion,
    u.premio,
    u.fecha AS registro,
    COALESCE(rm.total_rotulos_mini, 0) AS total_rotulos_mini,
    COALESCE(c.total_codigos, 0) AS total_codigos,
    COALESCE(rmb.total_rotulos_mini_baja, 0) AS total_rotulos_mini_baja,
    CAST(ROUND((
        COALESCE(rm.total_rotulos_mini, 0) + 
        COALESCE(c.total_codigos, 0) + 
        COALESCE(rmb.total_rotulos_mini_baja, 0)
    ) / 3.0) AS SIGNED) AS total_global,
    ROW_NUMBER() OVER (ORDER BY total_global DESC) AS top,
    CONCAT(
        DATE_FORMAT(DATE_SUB(CURRENT_DATE - INTERVAL (WEEKDAY(CURRENT_DATE) + 1) DAY, INTERVAL 7 DAY), '%Y-%m-%d'),
        ' al ',
        DATE_FORMAT(DATE_SUB(CURRENT_DATE - INTERVAL (WEEKDAY(CURRENT_DATE) + 1) DAY, INTERVAL 1 DAY), '%Y-%m-%d')
    ) AS periodo_top,
    (SELECT g.receptor
     FROM generados g
     WHERE g.user_uuid = u.user_uuid
     AND g.fecha BETWEEN DATE_SUB(CURRENT_DATE - INTERVAL (WEEKDAY(CURRENT_DATE) + 1) DAY, INTERVAL 7 DAY) 
                      AND DATE_SUB(CURRENT_DATE - INTERVAL (WEEKDAY(CURRENT_DATE) + 1) DAY, INTERVAL 1 DAY)
     GROUP BY g.receptor
     ORDER BY COUNT(*) DESC
     LIMIT 1) AS sala
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
ORDER BY 
    total_global DESC 
LIMIT 5;
";


        $list = $this->ejecutarConsulta($sql);

        $tops = $list->fetchAll(\PDO::FETCH_ASSOC);

        return $tops;
    }

    public function editProfile($nombre, $apellido, $telefono)
    {
        if (empty($nombre) || empty($apellido) || empty($telefono)) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'Debes llenar todos los campos del formulario';
            return $this->response;
        } else if (!preg_match($this->nombres, trim($nombre)) || !preg_match($this->nombres, trim($apellido))) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'El campo nombre y apellido solo admiten letras.';
            return $this->response;
        } else if (strlen($nombre) > 30 || strlen($apellido) > 30) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'El nombre o apellido no puede tener mas de 30 caracteres.';
            return $this->response;
        } else if (strlen($nombre) < 3 || strlen($apellido) < 3) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'El nombre o apellido debe tener al menos 3 caracteres.';
            return $this->response;
        } else if (!preg_match($this->telefonoRegex, trim($telefono))) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'El número de teléfono debe tener exactamente 8 dígitos';
            return $this->response;
        } else {
            $sql = 'UPDATE usuarios SET username = NULL, nombre = ?, apellido = ?, telefono = ? where user_uuid = ?';
            $this->ejecutarConsulta($sql, [$nombre, $apellido, $telefono, $this->user_uuid]);
            $cuerpoNotificacion = $nombre . " " . $apellido . " ha editado su perfil";
            $this->instanciaNotificacion->createNotification("PERFIL ACTUALIZADO", $cuerpoNotificacion);
            $this->response['status'] = 'OK';
            $this->response['message'] = 'Perfil actualizado correctamente.';
            return $this->response;
        }
    }

    public function editPasswordProfile($passwordNow, $password, $newPassword)
    {
        $sql = 'SELECT pass FROM usuarios WHERE user_uuid = ?';
        $logIn = $this->ejecutarConsulta($sql, [$this->user_uuid]);
        $accountData = $logIn->fetchAll(\PDO::FETCH_ASSOC);
        if (count($accountData) === 1) {
            if (!password_verify($passwordNow, $accountData[0]['pass'])) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'La contraseña anterior es incorecta.';
                return $this->response;
            } else if ($password !== $newPassword) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'La contraseña nueva no coincide. Vuelve a intentarlo.';
                return $this->response;
            } else {
                #ENCRIPTADO DE CLAVE
                $options = ['cost' => 12];
                $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT, $options);

                $sql = 'UPDATE usuarios SET pass = ? WHERE user_uuid = ?';
                $this->ejecutarConsulta($sql, [$passwordHash, $this->user_uuid]);

                $this->response['status'] = 'OK';
                $this->response['message'] = 'Contraseña actualizado correctamente.';
                return $this->response;
            }
        }
    }

    public function editPasswordRecovery($email, $password, $newPassword)
    {
        if(strlen($password) < 8 || strlen($newPassword) < 8){
            $this->response['status'] = 'error';
            $this->response['message'] = 'La contraseña debe tener al menos 8 caracteres.';
            return $this->response;
        } else if(strlen($password) > 16 || strlen($newPassword) > 16){
            $this->response['status'] = 'error';
            $this->response['message'] = 'La contraseña debe ser menor a 17 caracteres.';
            return $this->response;
        }else if ($password !== $newPassword) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'La contraseña nueva no coincide. Vuelve a intentarlo.';
            return $this->response;
        } else {
            #ENCRIPTADO DE CLAVE
            $options = ['cost' => 12];
            $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT, $options);

            $sql = 'UPDATE usuarios SET pass = ? WHERE email = ?';
            $this->ejecutarConsulta($sql, [$passwordHash, $email]);

            $this->response['status'] = 'OK';
            $this->response['message'] = 'Contraseña actualizado correctamente.';
            return $this->response;
        }
    }

    public function resetAccount(){
        $sql = 'UPDATE usuarios SET token_fcm_static = NULL';
        $this->ejecutarConsulta($sql);
    }
}
