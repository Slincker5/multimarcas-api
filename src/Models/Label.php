<?php

namespace App\Models;

use App\Models\Database;
use Ramsey\Uuid\UuidFactory;

class Label extends Database
{
    private $response;
    private $barra;
    private $descripcion;
    private $cantidad;
    private $precio;
    private $username;
    private $user_uuid;


    public function __construct($barra = '', $descripcion = '', $cantidad = '', $precio = '', $username = '', $user_uuid = '')
    {
        $this->barra = $barra;
        $this->descripcion = $descripcion;
        $this->cantidad = $cantidad;
        $this->precio = $precio;
        $this->username = $username;
        $this->user_uuid = $user_uuid;
    }

    public function getLabels($user_uuid)
    {
        $sql = 'SELECT * FROM codigos WHERE user_uuid = ?  AND path_uuid IS NULL ORDER BY id DESC';
        $response = $this->ejecutarConsulta($sql, [$user_uuid]);
        $labels = $response->fetchAll(\PDO::FETCH_ASSOC);
        return $labels;
    }

    public function addLabel($vip)
    {
        date_default_timezone_set("America/El_Salvador");
        if ($vip === 0) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'Necesitas ser usuario premiun para esta accion';
            return $this->response;
        } else {
            if ($this->cantidad > 448) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'El maximo de cintillos que puedes generar es de 448.';
                return $this->response;
            } else if (empty($this->cantidad) || empty($this->precio)) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'Debes completar todos los campos';
                return $this->response;
            } else if (!is_numeric($this->cantidad)) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'La cantidad de cintillos debe ser en numeros';
                return $this->response;
            } else if (!is_numeric($this->precio)) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'El campo precio solo admite numeros';
                return $this->response;
            } else {

                #CREAR UUID PARA CADA ROTULO
                $uuidFactory = new UuidFactory();
                $uuid = $uuidFactory->uuid4();
                $label_uuid = $uuid->toString();

                for ($i = 1; $i <= $this->cantidad; $i++) {

                    $this->barra = $this->barra === '' ? ' ' : $this->barra;

                    $sql = 'INSERT INTO codigos (barra, descripcion, cantidad, precio, username, uuid, user_uuid) VALUES (?, ?, ?, ?, ?, ?, ?)';
                    $crear = $this->ejecutarConsulta($sql, [$this->barra, $this->descripcion, $this->cantidad, $this->precio, $this->username, $label_uuid, $this->user_uuid]);
                    if (!$crear) {
                        $this->response['status'] = 'error';
                        $this->response['message'] = 'Hubo un error al crear el cintillo.';
                        return $this->response;
                    }
                }
                $this->response['status'] = 'OK';
                $this->response['message'] = 'Se han añadido ' . $this->cantidad . ' cintillos.';
                return $this->response;
            }
        }

    }

    public function editLabel($infoCintillo, $user_uuid)
    {
        if (empty($infoCintillo['uuid'])) {
            $this->response['status'] = 'OK';
            $this->response['message'] = 'No tienes permitida esta accion';
            return $this->response;
        } else {
            $sql = 'UPDATE codigos SET barra = ?, descripcion = ?, precio = ? WHERE uuid = ? AND user_uuid = ?';
            $this->ejecutarConsulta($sql, [$infoCintillo['barra'], $infoCintillo['descripcion'], $infoCintillo['precio'], $infoCintillo['uuid'], $user_uuid]);
            $this->response['status'] = 'OK';
            $this->response["message"] = 'Se actualizo la informacion del cintillo correctamente';
            return $this->response;
        }
    }

    public function detailsLabels($user_uuid, $uuid)
    {
        $sql = 'SELECT * FROM codigos WHERE user_uuid = ? AND uuid = ?';
        $res = $this->ejecutarConsulta($sql, [$user_uuid, $uuid]);
        $datos = $res->fetchAll(\PDO::FETCH_ASSOC);
        return $datos;
    }

    public function saveGenerated($path, $path_name, $path_uuid, $user_uuid, $comment, $code)
    {
        $regex = '/^[\p{L}\p{N}\s.,;:!?\'"áéíóúÁÉÍÓÚñÑ]+$/u';
        if (preg_match($regex, $comment)) {
            $sql = 'INSERT INTO generados (path, path_name, path_uuid, user_uuid, comentario, code) VALUES (?, ?, ?, ?, ?, ?)';
            $this->ejecutarConsulta($sql, [$path, $path_name, $path_uuid, $user_uuid, $comment, $code]);
        } else {
            $sql = 'INSERT INTO generados (path, path_name, path_uuid, user_uuid, comentario, code) VALUES (?, ?, ?, ?, ?, ?)';
            $this->ejecutarConsulta($sql, [$path, $path_name, $path_uuid, $user_uuid, null, $code]);
        }

    }

    public function assignDocument($path_uuid, $user_uuid)
    {
        $sql = 'UPDATE codigos SET path_uuid = ? WHERE user_uuid = ? AND path_uuid IS NULL';
        $this->ejecutarConsulta($sql, [$path_uuid, $user_uuid]);
    }

    public function eliminar($uuid, $user_uuid)
    {
        $sql = 'DELETE FROM codigos WHERE uuid = ? AND user_uuid = ?';
        $eliminar = $this->ejecutarConsulta($sql, [$uuid, $user_uuid]);
        if ($eliminar) {
            $this->response['status'] = 'OK';
            $this->response['message'] = 'Se elimino el cintillo correctamente';
            return $this->response;
        }
    }
}
