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
    private $user_uuid;
    private $response;
    private $admin_uuid = '2c62e966-63d8-4bfd-832e-89094ae47eec';

    public function __construct($user_uuid)
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

    private function hacerPremiun()
    {
        $sql = 'UPDATE usuarios SET suscripcion = 1, fin_suscripcion = ? WHERE user_uuid = ?';
        $guardarVip = $this->ejecutarConsulta($sql, [self::fechaFinSuscripcion(), $this->user_uuid]);
        if (!$guardarVip) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'Hubo un error al realizar tu suscripcion.';
            return $this->response;
        } else {
            $this->response['status'] = 'OK';
            $this->response['message'] = '¡En hora buena!, ahora eres usuario premiun.';
            return $this->response;
        }
    }

    public function agregarCupon($cupon_limite, $cupon)
    {

        if ($this->user_uuid === $this->admin_uuid) {
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
            $this->response['message'] = 'No estas autorizado para crear cupones.';
            return $this->response;
        }

    }

    public function canjearCupon($cupon)
    {

        $datosCupon = $this->datosCupon($cupon);
        $nCanjeos = $this->totalCanjeos($datosCupon[0]['cupon_uuid']);
        if ($nCanjeos[0]['cantidad'] < $datosCupon[0]['cupon_limite']) {
            $verificar = $this->validarUnaVez($datosCupon[0]['cupon_uuid']);

            if ($verificar[0]['cantidad'] > 1) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'Este cupon ya no es valido para tu cuenta.';
                return $this->response;
            } else {
                $sql = 'INSERT INTO canjeados (cupon_uuid, user_uuid) VALUES (?, ?)';
                $canjear = $this->ejecutarConsulta($sql, [$datosCupon[0]['cupon_uuid'], $this->user_uuid]);
                if($canjear){
                    return $this->hacerPremiun();
                }
            }
        } else {
            $this->response['status'] = 'error';
            $this->response['message'] = 'El cupon ha excedido su limite.';
            return $this->response;
        }
    }

    private function datosCupon($cupon)
    {
        if (!filter_var($cupon, FILTER_VALIDATE_INT)) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'El cupon solo debe contener numeros.';
            return $this->response;
        } else if (strlen($cupon) > 8 || strlen($cupon) < 8) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'El cupon solo debe contener 8 caracteres.';
            return $this->response;
        } else {
            $sql = 'SELECT cupon_uuid, cupon_limite, cupon FROM cupones WHERE cupon = ?';
            $datos = $this->ejecutarConsulta($sql, [$cupon]);
            $listar = $datos->fetchAll(\PDO::FETCH_ASSOC);
            return $listar;
        }
    }

    private function totalCanjeos($cupon_uuid)
    {
        $sql = 'SELECT COUNT(*) AS cantidad FROM canjeados WHERE cupon_uuid = ?';
        $datos = $this->ejecutarConsulta($sql, [$cupon_uuid]);
        $listar = $datos->fetchAll(\PDO::FETCH_ASSOC);
        return $listar;
    }

    private function validarUnaVez($cupon_uuid)
    {
        $sql = 'SELECT COUNT(*) AS cantidad FROM canjeados WHERE cupon_uuid = ? AND user_uuid = ?';
        $datos = $this->ejecutarConsulta($sql, [$cupon_uuid, $this->user_uuid]);
        $listar = $datos->fetchAll(\PDO::FETCH_ASSOC);
        return $listar;
    }
}
