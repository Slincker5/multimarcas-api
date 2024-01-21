<?php

namespace App\Models;

use App\Models\Database;

class Transaccion extends Database
{
    private $response;

    private function existenciaIdTransaccion($IdTransaccion)
    {
        $sql = 'SELECT COUNT(*) FROM transacciones WHERE IdTRansaccion = ?';
        $getIdTransaccion = $this->ejecutarConsulta($sql, [$IdTransaccion]);
        $result = $getIdTransaccion->fetchColumn();
        return $total;
    }

    public function saveTransaction($IdTransaccion, $ResultadoTransaccion, $Monto, $FechaTransaccion)
    {
        $sql = 'INSERT INTO transacciones (IdTransaccion, ResultadoTransaccion, Monto, FechaTransaccion) VALUES (?, ?, ?, ?)';
        $transaccion = $this->ejecutarConsulta($sql, [$IdTransaccion, $ResultadoTransaccion, $Monto, $FechaTransaccion]);
        if ($transaccion) {
            $this->response['status'] = 'OK';
            $this->response['message'] = 'Operacion exitosa';
            return $this->response;
        }
    }

    public function saveTransactionAfterPay($IdTransaccion, $user_uuid)
    {
        if (!$this->existenciaIdTransaccion($IdTransaccion)) {
            $sql = 'INSERT INTO transacciones (IdTransaccion, user_uuid) VALUES (?, ?)';
            $transaccion = $this->ejecutarConsulta($sql, [$IdTransaccion, $user_uuid]);
            if ($transaccion) {
                $this->response['status'] = 'OK';
                $this->response['message'] = 'Operacion exitosa';
                return $this->response;
            }
        } else {
            $sql = 'UPDATE SET transacciones user_uuid = ?';
            $transaccion = $this->ejecutarConsulta($sql, [$user_uuid]);
            if ($transaccion) {
                $this->response['status'] = 'OK';
                $this->response['message'] = 'Operacion exitosa';
                return $this->response;
            }
        }
    }
}
