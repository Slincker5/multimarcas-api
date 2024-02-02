<?php

namespace App\Models;

use App\Models\Database;

class Transaccion extends Database
{
    private $secret = '7519b85d-ccaa-42c5-8e2f-0390c23e5d22';
    private $response;

    private function fechaFinSuscripcion()
    {
        $fecha_inicio = date('Y-m-d');
        $fecha_vencimiento = date('Y-m-d', strtotime($fecha_inicio . ' +1 month'));
        return $fecha_vencimiento;
    }

    private function existenciaIdTransaccion($IdTransaccion)
    {
        $sql = 'SELECT COUNT(*) FROM transacciones WHERE IdTransaccion = ?';
        $getIdTransaccion = $this->ejecutarConsulta($sql, [$IdTransaccion]);
        $result = $getIdTransaccion->fetchColumn();
        return $result;
    }

    public function saveTransaction($IdTransaccion, $ResultadoTransaccion, $Monto, $FechaTransaccion, $header_wompi, $wompiHashHeader)
    {
        $calculatedHash = hash_hmac('sha256', $header_wompi, $this->secret);
        if ($calculatedHash === $wompiHashHeader) {
            if (!$this->existenciaIdTransaccion($IdTransaccion)) {
                $sql = 'INSERT INTO transacciones (IdTransaccion, ResultadoTransaccion, Monto, FechaTransaccion) VALUES (?, ?, ?, ?)';
                $transaccion = $this->ejecutarConsulta($sql, [$IdTransaccion, $ResultadoTransaccion, $Monto, $FechaTransaccion]);
                if ($transaccion) {
                    $this->response['status'] = 'OK';
                    $this->response['message'] = 'Operacion exitosa';
                    return $this->response;
                }
            } else {
                $sql = 'UPDATE transacciones SET ResultadoTransaccion = ?, Monto = ?, FechaTransaccion = ?';
                $transaccion = $this->ejecutarConsulta($sql, [$ResultadoTransaccion, $Monto, $FechaTransaccion]);
                if ($transaccion) {
                    $this->response['status'] = 'OK';
                    $this->response['message'] = 'Operacion exitosa';
                    return $this->response;
                }
            }
        } else {
            $this->response['status'] = 'OK';
            $this->response['message'] = 'Tu transaccion es fraudulenta.';
            return $this->response;
        }

    }

    public function saveTransactionAfterPay($IdTransaccion, $user_uuid)
    {
        if (!$this->existenciaIdTransaccion($IdTransaccion)) {
            $sql = 'INSERT INTO transacciones (IdTransaccion, user_uuid) VALUES (?, ?)';
            $transaccion = $this->ejecutarConsulta($sql, [$IdTransaccion, $user_uuid]);
            if ($transaccion) {
                if ($transaccion) {
                    $hacerPremiun = 'UPDATE usuarios SET suscripcion = true, fin_suscripcion = ? WHERE user_uuid = ?';
                    $fecha = $this->fechaFinSuscripcion();
                    $premiun = $this->ejecutarConsulta($hacerPremiun, [$fecha, $user_uuid]);
                    if ($premiun) {
                        $this->response['status'] = 'OK';
                        $this->response['message'] = '¡En hora buena!, ahora eres usuario premiun.';
                        return $this->response;
                    }
                }
            }
        } else {
            $sql = 'UPDATE transacciones SET user_uuid = ? WHERE IdTransaccion = ?';
            $transaccion = $this->ejecutarConsulta($sql, [$user_uuid, $IdTransaccion]);
            if ($transaccion) {
                $hacerPremiun = 'UPDATE usuarios SET suscripcion = true, fin_suscripcion = ? WHERE user_uuid = ?';
                $fecha = $this->fechaFinSuscripcion();
                $premiun = $this->ejecutarConsulta($hacerPremiun, [$fecha, $user_uuid]);
                if ($premiun) {
                    $this->response['status'] = 'OK';
                    $this->response['message'] = '¡En hora buena!, ahora eres usuario premiun.';
                    return $this->response;
                }
            }
        }
    }
}
