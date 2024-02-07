<?php

namespace App\Models;

use App\Models\Database;
use Firebase\JWT\JWT;
use Ramsey\Uuid\UuidFactory;

class Auth extends Database
{

    #PROPIEDADES CLASE

    private $expReg = '/^[a-zA-Z0-9 ñÑ ]+$/';
    private $nombres = '/^[a-zA-ZñÑ]+$/';
    private $response = [];
    public $key = "georginalissethyvladi";

    #METODOS CLASE
    private function usernameStock($user)
    {
        $sql = 'SELECT COUNT(*) FROM usuarios WHERE username = ?';
        $getData = $this->ejecutarConsulta($sql, [$user]);
        $total = $getData->fetchColumn();
        return $total;
    }

    public function emailStock($email)
    {
        $sql = 'SELECT * FROM usuarios WHERE email = ?';
        $getData = $this->ejecutarConsulta($sql, [$email]);
        $datos = $getData->fetchAll(\PDO::FETCH_ASSOC);
        return $datos;
    }

    public function telefonoStock($telefono)
    {
        $sql = 'SELECT COUNT(*) FROM usuarios WHERE telefono = ?';
        $getData = $this->ejecutarConsulta($sql, [$telefono]);
        $total = $getData->fetchColumn();
        return $total;

    }

    public function getDataUser($email)
    {
        $sql = 'SELECT * FROM usuarios WHERE email = ?';
        $getData = $this->ejecutarConsulta($sql, [$email]);
        $datos = $getData->fetchAll(\PDO::FETCH_ASSOC);
        return $datos;
    }

    public function createAccount($username, $pass = '', $ip)
    {
        if (empty($username) || empty($pass)) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'Completa todos los campos.';
            return $this->response;
        } else if (!preg_match($this->expReg, $username)) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'El nombre de usuario solo puede contener numeros y letras, no se permiten espacios';
            return $this->response;
        } else if (strlen($username) > 30) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'El nombre de usuario no puede tener mas de 30 caracteres.';
            return $this->response;
        } else if (strlen($username) < 4) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'El nombre de usuario debe tener al menos 4 caracteres.';
            return $this->response;
        } else if (strlen($username) > 30) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'El nombre de usuario no puede tener mas de 30 caracteres.';
            return $this->response;
        } else if (strlen($pass) < 8) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'Tu contraseña debe tener al menos 8 caracteres.';
            return $this->response;
        } else if ($this->usernameStock($username)) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'El nombre de usuario ya existe, escoge otro diferente';
            return $this->response;
        } else {

            #GENERANDO UN UUID UNICO PARA EL PERFIL
            $uuidFactory = new UuidFactory();
            $uuid = $uuidFactory->uuid4();
            $profile_uuid = $uuid->toString();

            #ENCRIPTADO DE CLAVE
            $options = ['cost' => 12];
            $passwordHash = password_hash($pass, PASSWORD_BCRYPT, $options);

            #PROCEDER AL GUARDADO PERSISTENTE
            $sql = 'INSERT INTO usuarios (user_uuid, username, pass, rol, ip) VALUES (?, ?, ?, ?, ?)';
            $signUp = $this->ejecutarConsulta($sql, [$profile_uuid, $username, $passwordHash, 'User', $ip]);

            if ($signUp) {
                $this->response['status'] = 'OK';
                $this->response['message'] = 'Registro exitoso.';
                return $this->response;
            } else {
                $this->response['status'] = 'error';
                $this->response['message'] = 'Hubo algun problema a la hora de tu registro, intenta mas tarde.';
                return $this->response;
            }
        }
    }

    public function createAccountN($nombre, $apellido, $correo, $pass, $ip)
    {
        if (empty($nombre) || empty($apellido) || empty($correo) || empty($pass)) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'Completa todos los campos.';
            return $this->response;
        } else if (!preg_match($this->nombres, $nombre) || !preg_match($this->nombres, $apellido)) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'El nombre o apellido solo puede contener letras.';
            return $this->response;
        } else if (strlen($nombre) > 30 || strlen($apellido) > 30) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'El nombre o apellido no puede tener mas de 30 caracteres.';
            return $this->response;
        } else if (strlen($nombre) < 3 || strlen($apellido) < 3) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'El nombre o apellido debe tener al menos 3 caracteres.';
            return $this->response;
        } else if (strlen($pass) < 8) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'Tu contraseña debe tener al menos 8 caracteres.';
            return $this->response;
        } else if($this->emailStock($correo)){
            $this->response['status'] = 'error';
            $this->response['message'] = 'Este correo ya esta registrado, intenta con uno difrente.';
            return $this->response;
        } else if(!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'Debes ingresar un correo electronico valido.';
            return $this->response;
        } else {

            #GENERANDO UN UUID UNICO PARA EL PERFIL
            $uuidFactory = new UuidFactory();
            $uuid = $uuidFactory->uuid4();
            $profile_uuid = $uuid->toString();

            #ENCRIPTADO DE CLAVE
            $options = ['cost' => 12];
            $passwordHash = password_hash($pass, PASSWORD_BCRYPT, $options);

            #PROCEDER AL GUARDADO PERSISTENTE
            $sql = 'INSERT INTO usuarios (user_uuid, nombre, apellido, email, pass, rol, ip) VALUES (?, ?, ?, ?, ?, ?, ?)';
            $signUp = $this->ejecutarConsulta($sql, [$profile_uuid, $nombre, $apellido, $correo, $passwordHash, 'User', $ip]);

            if ($signUp) {
                $payload = array(
                    "iss" => "multimarcas",
                    "aud" => $profile_uuid,
                    "iat" => time(),
                    "nbf" => time(),
                    "data" => array(
                        "user_uuid" => $profile_uuid,
                        "username" => $nombre . ' ' . $apellido,
                        "email" => $correo,
                        "photo" => null,
                        "rol" => "User",
                        "fecha" => "",
                        "suscripcion" => false,
                        "fin_suscripcion" => null,
                        "ip" => $ip,

                    ),
                );
                $alg = "HS256";
                $token = JWT::encode($payload, $this->key, $alg);
                $this->response['status'] = 'OK';
                $this->response['message'] = 'Registro exitoso.';
                $this->response['username'] = $nombre . " " . $apellido;
                $this->response['user_uuid'] = $profile_uuid;
                $this->response['email'] = $correo;
                $this->response['photo'] = null;
                $this->response['rol'] = "User";
                $this->response['token'] = $token;
                return $this->response;
            } else {
                $this->response['status'] = 'error';
                $this->response['message'] = 'Hubo algun problema a la hora de tu registro, intenta mas tarde.';
                return $this->response;
            }
        }
    }

    public function logIn($username, $pass)
    {
        $sql = 'SELECT * FROM usuarios WHERE email = ? OR telefono = ? OR username = ?';
        $logIn = $this->ejecutarConsulta($sql, [$username, '+503' . $username, $username]);
        $accountData = $logIn->fetchAll(\PDO::FETCH_ASSOC);
        $nombreCompleto = $accountData[0]['nombre'] . " " . $accountData[0]['apellido'];
        if (count($accountData) === 1) {
            if (password_verify($pass, $accountData[0]['pass'])) {

                // Crear un token
                $payload = array(
                    "iss" => "multimarcas",
                    "aud" => $accountData[0]['user_uuid'],
                    "iat" => time(),
                    "nbf" => time(),
                    "data" => array(
                        "user_uuid" => $accountData[0]['user_uuid'],
                        "username" => $accountData[0]['username'] === null ? $nombreCompleto : $accountData[0]['username'],
                        "email" => $accountData[0]['email'],
                        "photo" => $accountData[0]['photo'],
                        "rol" => $accountData[0]['rol'],
                        "fecha" => $accountData[0]['fecha'],
                        "suscripcion" => $accountData[0]['suscripcion'],
                        "fin_suscripcion" => $accountData[0]['fin_suscripcion'],
                        "ip" => $accountData[0]['ip'],

                    ),
                );
                $alg = "HS256";
                $token = JWT::encode($payload, $this->key, $alg);

                $this->response['status'] = 'OK';
                $this->response['message'] = 'Sesión exitosa.';
                $this->response['username'] = $accountData[0]['username'] === null ? $nombreCompleto : $accountData[0]['username'];
                $this->response['user_uuid'] = $accountData[0]['user_uuid'];
                $this->response['email'] = $accountData[0]['email'];
                $this->response['photo'] = $accountData[0]['photo'];
                $this->response['rol'] = $accountData[0]['rol'];
                $this->response['token'] = $token;
                return $this->response;
            } else {
                $this->response['status'] = 'error';
                $this->response['message'] = 'Usuario o contraseña incorrectos, valida tus datos';
                return $this->response;
            }
        } else {
            $this->response['status'] = 'error';
            $this->response['message'] = 'Usuario o contraseña incorrectos, valida tus datos';
            return $this->response;
        }
    }

    public function loginWithGoogle($username, $email, $photo)
    {

        if ($this->emailStock($email)) {
            $data = $this->getDataUser($email);
            #DATOS QUE CONTENDRÁ EL TOKEN

            $payload = array(
                "iss" => "multimarcas",
                "aud" => $data[0]['user_uuid'],
                "iat" => time(),
                "nbf" => time(),
                "data" => array(
                    "user_uuid" => $data[0]['user_uuid'],
                    "username" => $data[0]['username'] === null ? $data[0]['nombre'] . ' ' . $data[0]['apellido'] : $data[0]['username'],
                    "email" => $data[0]['email'],
                    "photo" => $data[0]['photo'],
                    "rol" => $data[0]['rol'],
                    "fecha" => $data[0]['fecha'],
                    "suscripcion" => $data[0]['suscripcion'],
                    "fin_suscripcion" => $data[0]['fin_suscripcion'],
                    "ip" => $data[0]['ip'],

                ),
            );
            $alg = "HS256";
            $token = JWT::encode($payload, $this->key, $alg);

            $this->response['status'] = 'OK';
            $this->response['message'] = 'Sesión exitosa.';
            $this->response['username'] = $data[0]['username'] === null ? $data[0]['nombre'] . ' ' . $data[0]['apellido'] : $data[0]['username'];
            $this->response['user_uuid'] = $data[0]['user_uuid'];
            $this->response['email'] = $data[0]['email'];
            $this->response['photo'] = $data[0]['photo'];
            $this->response['rol'] = $data[0]['rol'];
            $this->response['fecha'] = $data[0]['fecha'];
            $this->response['token'] = $token;
            return $this->response;
        } else {
            #GENERANDO UN UUID UNICO PARA EL PERFIL
            $uuidFactory = new UuidFactory();
            $uuid = $uuidFactory->uuid4();
            $profile_uuid = $uuid->toString();

            #PROCEDER AL GUARDADO PERSISTENTE
            $sql = 'INSERT INTO usuarios (user_uuid, username, email, photo, rol) VALUES (?, ?, ?, ?, ?)';
            $signUp = $this->ejecutarConsulta($sql, [$profile_uuid, $username, $email, $photo, 'User']);

            if ($signUp) {

                $payload = array(
                    "iss" => "multimarcas",
                    "aud" => $profile_uuid,
                    "iat" => time(),
                    "nbf" => time(),
                    "data" => array(
                        "user_uuid" => $profile_uuid,
                        "username" => $username,
                        "email" => $username,
                        "photo" => $photo,
                        "rol" => 'User',

                    ),
                );
                $alg = "HS256";
                $token = JWT::encode($payload, $this->key, $alg);

                $this->response['status'] = 'OK';
                $this->response['message'] = 'Registro exitoso.';
                $this->response['username'] = $username;
                $this->response['user_uuid'] = $profile_uuid;
                $this->response['email'] = $email;
                $this->response['photo'] = $photo;
                $this->response['token'] = $token;
                return $this->response;
            } else {
                $this->response['status'] = 'error';
                $this->response['message'] = 'Hubo algun problema a la hora de tu registro, intenta mas tarde.';
                return $this->response;
            }
        }

    }

    public function generatedToken($user_uuid, $username, $email, $photo)
    {
        $payload = array(
            "iss" => "multimarcas",
            "aud" => $user_uuid,
            "iat" => time(),
            "nbf" => time(),
            "data" => array(
                "user_uuid" => $user_uuid,
                "username" => $username,
                "email" => $email,
                "photo" => $photo,
                "rol" => 'User',

            ),
        );
        $alg = "HS256";
        $token = JWT::encode($payload, $this->key, $alg);
        $this->response['status'] = 'OK';
        $this->response['message'] = 'token generado con exito.';
        $this->response['token'] = $token;
        return $this->response;
    }

}
