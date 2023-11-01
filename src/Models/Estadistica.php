<?php


require_once $_SERVER['DOCUMENT_ROOT'] . '/api/clases/Database.php';

class Estadistica extends Database {
    private $user_uuid;

    public function __construct($user_uuid){
        $this->user_uuid = $user_uuid;
    }

    private function totalCintillos () {
        $sql = 'SELECT COUNT(*) AS conteo FROM codigos WHERE  user_uuid = ?';
        $response = $this->ejecutarConsulta($sql, [$this->user_uuid]);
        $datos = $response->fetchAll(PDO::FETCH_ASSOC);
        return $datos[0]['conteo'];
    }

    private function totalCintillosGenerados () {
        $sql = 'SELECT COUNT(*) AS conteo FROM codigos WHERE user_uuid = ? AND path_uuid IS NOT NULL';
        $response = $this->ejecutarConsulta($sql, [$this->user_uuid]);
        $datos = $response->fetchAll(PDO::FETCH_ASSOC);
        return $datos[0]['conteo'];
    }

    private function totalRotulos () {
        $sql = 'SELECT COUNT(*) AS conteo FROM rotulos WHERE user_uuid = ?';
        $response = $this->ejecutarConsulta($sql, [$this->user_uuid]);
        $datos = $response->fetchAll(PDO::FETCH_ASSOC);
        return $datos[0]['conteo'];
    }

    private function totalRotulosGenerados () {
        $sql = 'SELECT COUNT(*) AS conteo FROM rotulos WHERE user_uuid = ? AND path_uuid IS NOT NULL';
        $response = $this->ejecutarConsulta($sql, [$this->user_uuid]);
        $datos = $response->fetchAll(PDO::FETCH_ASSOC);
        return $datos[0]['conteo'];
    }

    public function estadisticasGlobal () {
        $stats = [
            "totalCintillos" => $this->totalCintillos(),
            "totalCintillosGenerados" => $this->totalCintillosGenerados(),
            "totalRotulos" => $this->totalRotulos(),
            "totalRotulosGenerados" => $this->totalRotulosGenerados()
        ];
        return $stats;
    }
}