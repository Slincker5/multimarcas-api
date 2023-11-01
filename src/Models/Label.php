<?php

namespace App\Models;

use App\Models\Database;
use Ramsey\Uuid\Uuid;

class Label extends Database
{
    private $response;
    protected $plantilla;

    public function __construct($barra = '', $descripcion = '', $cantidad = '', $precio = '', $username = '', $uuid = '', $user_uuid = '')
    {
        $this->plantilla = $_SERVER['DOCUMENT_ROOT'] . '/public/documentos/PLANTILLA2.xlsx';
        $this->barra = $barra;
        $this->descripcion = $descripcion;
        $this->cantidad = $cantidad;
        $this->precio = $precio;
        $this->username = $username;
        $this->uuid = $uuid;
        $this->user_uuid = $user_uuid;
    }

    public function getLabels($user_uuid)
    {
        $sql = 'SELECT * FROM codigos WHERE user_uuid = ?  AND path_uuid IS NULL ORDER BY id DESC';
        $response = $this->ejecutarConsulta($sql, [$user_uuid]);
        $labels = $response->fetchAll(\PDO::FETCH_ASSOC);
        return $labels;
    }

    public function addLabel()
    {
        if ($this->cantidad > 448) {
            $this->response['message'] = 'El maximo de cintillos que puedes generar es de 448.';
            return $this->response;
        } else if (empty($this->cantidad) || empty($this->precio)) {
            $this->response['message'] = 'Debes completar todos los campos';
            return $this->response;
        } else if (!is_numeric($this->cantidad)) {
            $this->response['message'] = 'La cantidad de cintillos debe ser en numeros';
            return $this->response;
        } else if (!is_numeric($this->precio)) {
            $this->response['message'] = 'El campo precio solo admite numeros';
            return $this->response;
        } else {
            for ($i = 1; $i <= $this->cantidad; $i++) {
                if ($this->barra == '') {
                    $this->barra = ' ';
                }
                $sql = 'INSERT INTO codigos (barra, descripcion, cantidad, precio, username, uuid, user_uuid) VALUES (?, ?, ?, ?, ?, ?, ?)';
                $crear = $this->ejecutarConsulta($sql, [$this->barra, $this->descripcion, $this->cantidad, $this->precio, $this->username, $this->uuid, $this->user_uuid]);
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

    public function guardarGenerados($path, $path_name, $path_uuid, $autor)
    {
        $sql = 'INSERT INTO generados (path, path_name, path_uuid, user_uuid) VALUES (?,?,?,?)';
        $this->ejecutarConsulta($sql, [$path, $path_name, $path_uuid, $autor]);
    }

    public function asignarDocumento($path_uuid, $user_uuid)
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
