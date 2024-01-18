<?php

namespace App\Models;

use App\Models\Database;

class Transaccion extends Database {
    private $response;

    public function savedTransaction($IdTransaccion, $ResultadoTransaccion, $Monto, $FechaTransaccion){
        $sql = 'INSERT INTO transacciones (IdTransaccion, ResultadoTransaccion, Monto, FechaTransaccion) VALUES (?, ?, ?, ?)';
        $transaccion = $this->ejecutarConsulta($sql, [$IdTransaccion, $ResultadoTransaccion, $Monto, $FechaTransaccion]);
        if($transaccion){
            $this->response['status'] = 'OK';
            $this->response['message'] = 'Operacion exitosa';
            return $this->response;
        }
    }
}