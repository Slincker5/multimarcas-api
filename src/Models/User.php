<?php

namespace App\Models;

use App\Models\Database;

class User extends Database
{

    private $user_uuid;

    public function __construct($user_uuid)
    {
        $this->user_uuid = $user_uuid;
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
            "totalCintillos" => $this->totalCintillos(),
            "totalCintillosGenerados" => $this->totalCintillosGenerados(),
            "totalRotulos" => $this->totalRotulos(),
            "totalRotulosGenerados" => $this->totalRotulosGenerados(),
        ];
        return $stats;
    }


}
