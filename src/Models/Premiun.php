<?php

namespace App\Models;

use App\Models\Database;
use Ramsey\Uuid\UuidFactory;

trait Cupones
{
    public function generarCupon($cupon_limite, $cupon)
    {
        $cupon = str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        $this->response['status'] = 'OK';
        $this->response['cupon'] = $cupon;
        return $this->response;
    }
}

class Premiun extends Database
{
    use Cupones;
    public $user_uuid;
    public $response;

    public function __constructor($user_uuid)
    {
        $this->user_uuid = $user_uuid;
    }

    private static function fechaFinSuscripcion()
    {
        $fecha_inicio = date('Y-m-d');
        $fecha_vencimiento = date('Y-m-d', strtotime($fecha_inicio . ' +1 month'));
        return $fecha_vencimiento;
    }

    private function validarCuponExistencia($cupon)
    {
        $sql = 'SELECT COUNT(*) FROM cupones WHERE cupon = ?';
        $getData = $this->ejecutarConsulta($sql, [$cupon]);
        $total = $getData->fetchColumn();
        return $total;
    }

    public function hacerPremiun()
    {
        $sql = 'UPDATE usuarios SET suscripcion = true, fin_suscripcion = ?';
        $guardarVip = $this->ejecutarConsulta($sql, [self::fechaFinSuscripcion()]);
        if (!$guardarVip) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'Hubo un error al realizar tu suscripcion.';
            return $this->response;
        } else {
            $this->response['status'] = 'OK';
            $this->response['message'] = '¡En hora buena!, ahora eres usuario premiun.' . $this->generarCupon();
            return $this->response;
        }
    }

    public function agregarCupon($cupon_limite, $cupon)
    {
        $admin_uuid = "2c62e966-63d8-4bfd-832e-89094ae47eec";
        if ($this->user_uuid == $admin_uuid) {
            if ($cupon_limite >= 41 || $cupon_limite <= 0) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'Debes cumplir con el rango de uso, >1 o <40';
                return $this->response;
            } else if (!filter_var($cupon_limite, FILTER_VALIDATE_INT)) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'El limite del cupon solo acepta numeros enteros.';
                return $this->response;
            } else if (!filter_var($cupon, FILTER_VALIDATE_INT)) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'El cupon solo debe contener numeros.';
                return $this->response;
            } else if (strlen($cupon) > 8 || strlen($cupon) < 8) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'El cupon solo debe contener 8 caracteres.';
                return $this->response;
            } else if ($this->validarCuponExistencia($cupon)) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'El cupon ya existe, crea otro diferente';
                return $this->response;
            } else {

                #CREAR UUID PARA CUPON
                $uuidFactory = new UuidFactory();
                $uuid = $uuidFactory->uuid4();
                $cupon_uuid = $uuid->toString();
                $sql = 'INSERT INTO cupones (cupon_uuid, cupon_limite, cupon) VALUES (?, ?, ?)';
                $guardarCupon = $this->ejecutarConsulta($sql, [$cupon_uuid, $cupon_limite, $cupon]);
                if (!$guardarCupon) {
                    $this->response['status'] = 'error';
                    $this->response['message'] = 'Hubo un error al crear tu cupon.';
                    return $this->response;
                } else {
                    $this->response['status'] = 'OK';
                    $this->response['message'] = 'Cupon generado con exito';
                    $this->response['cupon'] = $cupon;
                    return $this->response;
                }

            }

        } else {
            $this->response['status'] = 'error';
            $this->response['message'] = 'No estas autorizado para crear cupones.' . $this->user_uuid;
            return $this->response;
        }

    }
}
