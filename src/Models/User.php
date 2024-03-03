<?php

namespace App\Models;


use Firebase\JWT\JWT;
use App\Models\Database;
use App\Models\Premiun;

class User extends Database
{
    private $response;
    private $user_uuid;
    private $key = "georginalissethyvladi";
    public $instanciaPremium;

    public function __construct($user_uuid = "")
    {
        $this->user_uuid = $user_uuid;
        $this->instanciaPremium = new Premiun($user_uuid);
    }

    private function datosUsuario()
    {
        $sql = 'SELECT * FROM usuarios WHERE user_uuid = ?';
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

    public function notificarPremium(){
        $sql = "SELECT * FROM usuarios where fin_suscripcion is not null";
        $response = $this->ejecutarConsulta($sql);
        $datos = $response->fetchAll(\PDO::FETCH_ASSOC);
        return $datos;
    }

    public function generatedToken () {
        $datos = $this->datosUsuario();
        $nombreCompleto = $datos[0]["nombre"] . " " . $datos[0]["apellido"];
        $payload = array(
            "iss" => "multimarcas",
            "aud" => $this->user_uuid,
            "iat" => time(),
            "nbf" => time(),
            "data" => array(
                "user_uuid" => $datos[0]["user_uuid"],
                "username" => $datos[0]["username"] === NULL ? $nombreCompleto : $datos[0]["username"],
                "email" => $datos[0]["email"],
                "photo" => $datos[0]["photo"],
                "rol" => $datos[0]["rol"],
                "suscripcion" => $datos[0]["suscripcion"],
                "fin_suscripcion" => $datos[0]["fin_suscripcion"]

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
