<?php

namespace App\Models;

class Database
{
    private static $parametros = "mysql:host=localhost;dbname=datos";
    private static $usuario = "multimarcas";
    private static $clave = "21AnimewapK";

    public function conectar()
    {
        try {
            $con = new \PDO(self::$parametros, self::$usuario, self::$clave);
            return $con;
        } catch (\PDOException $e) {
            echo "ERROR: " . $e->getMessage();
        }
    }

    protected function ejecutarConsulta($sql, $params = [])
    {
        $conexion = $this->conectar();
        $consulta = $conexion->prepare($sql);
        $consulta->execute($params);
        return $consulta;
    }

}
