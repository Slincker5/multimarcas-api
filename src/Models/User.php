<?php

namespace App\Models;


use Firebase\JWT\JWT;
use App\Models\Database;

class User extends Database
{

    private $user_uuid;
    private $key = "georginalissethyvladi";

    public function __construct($user_uuid)
    {
        $this->user_uuid = $user_uuid;
    }

    private function datosUsuario()
    {
        $sql = 'SELECT user_uuid, username, email, photo, rol, suscripcion, fin_suscripcion FROM usuarios WHERE user_uuid = ?';
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
    
    public function verEstado(){
        $sql = 'SELECT mvc FROM usuarios WHERE  user_uuid = ?';
        $response = $this->ejecutarConsulta($sql, [$this->user_uuid]);
        $datos = $response->fetchAll(\PDO::FETCH_ASSOC);
        return $datos;
    }

    public function updateToken(){
        $sql = 'UPDATE usuarios SET mvc = ? WHERE user_uuid = ?';
        $response = $this->ejecutarConsulta($sql, ['si', $this->user_uuid]);
    }

    public function estadisticasGlobal()
    {
        $stats = [
            "profile" => $this->datosUsuario(),
            "totalCintillos" => $this->totalCintillos(),
            "totalCintillosGenerados" => $this->totalCintillosGenerados(),
            "totalRotulos" => $this->totalRotulos(),
            "totalRotulosGenerados" => $this->totalRotulosGenerados(),
        ];
        return $stats;
    }

    public function generatedToken () {
        $datos = $this->datosUsuario();
        $payload = array(
            "iss" => "multimarcas",
            "aud" => $user_uuid,
            "iat" => time(),
            "nbf" => time(),
            "data" => array(
                "user_uuid" => $datos["user_uuid"],
                "username" => $datos["username"],
                "email" => $datos["email"],
                "photo" => $datos["photo"],
                "rol" => $datos["rol"],
                "suscripcion" => $datos["suscripcion"],
                "fin_suscripcion" => $datos["fin_suscripcion"]

            ),
        );
        $alg = "HS256";
        $token = JWT::encode($payload, $this->key, $alg);
        $this->response['status'] = 'OK';
        $this->response['message'] = 'token generado con exito.';
        $this->response["usera"] = $datos;
        $this->response['token'] = $token;
        return $this->response;
    }

}
