<?php
namespace App\Models;

use App\Models\Database;
use Ramsey\Uuid\UuidFactory;

class Poster extends Database
{

    private $response = [];

    public function __construct($barra = '', $descripcion = '', $precio = '', $f_inicio = '', $f_fin = '', $cantidad = '', $user_uuid = '')
    {
        $this->barra = $barra;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->f_inicio = $f_inicio;
        $this->f_fin = $f_fin;
        $this->cantidad = $cantidad;
        $this->user_uuid = $user_uuid;

    }
    public function listPoster($user_uuid)
    {
        $sql = 'SELECT * FROM rotulos WHERE user_uuid = ?  AND path_uuid IS NULL ORDER BY id DESC';
        $registrar = $this->ejecutarConsulta($sql, [$user_uuid]);
        $datos = $registrar->fetchAll(\PDO::FETCH_ASSOC);
        return $datos;
    }

    public function listPosterSmall($user_uuid)
    {
        $sql = 'SELECT * FROM rotulos_mini WHERE user_uuid = ?  AND path_uuid IS NULL ORDER BY id DESC';
        $registrar = $this->ejecutarConsulta($sql, [$user_uuid]);
        $datos = $registrar->fetchAll(\PDO::FETCH_ASSOC);
        return $datos;
    }

    public function createPoster()
    {

        date_default_timezone_set("America/El_Salvador");
        $this->response['status'] = 'error';

        if (empty($this->descripcion) || empty($this->precio) || empty($this->cantidad)) {
            $this->response['massage'] = 'Debes completar todos los campos';
            return $this->response;
        } else if (!is_numeric($this->cantidad)) {
            $this->response['message'] = 'La cantidad de cintillos debe ser en numeros';
            return $this->response;
        } else if ($this->cantidad > 200) {
            $this->response['message'] = 'El limite de rotulos por crear es de 200';
            return $this->response;
        } else {
            #CREAR UUID PARA CADA ROTULO
            $uuidFactory = new UuidFactory();
            $uuid = $uuidFactory->uuid4();
            $poster_uuid = $uuid->toString();

            for ($i = 1; $i <= $this->cantidad; $i++) {
                if ($this->barra == '') {
                    $this->barra = ' ';
                }
                $sql = 'INSERT INTO  rotulos (barra, descripcion, precio, f_inicio, f_fin, cantidad, user_uuid, uuid) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
                $crear = $this->ejecutarConsulta($sql, [$this->barra, $this->descripcion, $this->precio, $this->f_inicio, $this->f_fin, $this->cantidad, $this->user_uuid, $poster_uuid]);
                if (!$crear) {
                    $this->response['status'] = 'error';
                    $this->response['message'] = 'Hubo un error al crear el rótulo.';
                    return $this->response;
                }
            }
            $this->response['status'] = 'OK';
            $this->response['message'] = 'Se han añadido ' . $this->cantidad . ' rotulos.';
            return $this->response;
        }
    }

    public function createPosterSmall()
    {

        date_default_timezone_set("America/El_Salvador");
        $this->response['status'] = 'error';

        if (empty($this->descripcion) || empty($this->precio) || empty($this->cantidad)) {
            $this->response['message'] = 'Debes completar todos los campos';
            return $this->response;
        } else if (!is_numeric($this->cantidad)) {
            $this->response['message'] = 'La cantidad de cintillos debe ser en numeros';
            return $this->response;
        } else if ($this->cantidad > 90) {
            $this->response['message'] = 'El limite de rotulos por crear es de 90';
            return $this->response;
        } else {
            #CREAR UUID PARA CADA ROTULO
            $uuidFactory = new UuidFactory();
            $uuid = $uuidFactory->uuid4();
            $poster_uuid = $uuid->toString();

            for ($i = 1; $i <= $this->cantidad; $i++) {
                if ($this->barra == '') {
                    $this->barra = ' ';
                }
                $sql = 'INSERT INTO  rotulos_mini (barra, descripcion, precio, f_inicio, f_fin, cantidad, user_uuid, uuid) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
                $crear = $this->ejecutarConsulta($sql, [$this->barra, $this->descripcion, $this->precio, $this->f_inicio, $this->f_fin, $this->cantidad, $this->user_uuid, $poster_uuid]);
                if (!$crear) {
                    $this->response['status'] = 'error';
                    $this->response['message'] = 'Hubo un error al crear el rótulo.';
                    return $this->response;
                }
            }
            $this->response['status'] = 'OK';
            $this->response['message'] = 'Se han añadido ' . $this->cantidad . ' rotulos.';
            return $this->response;
        }
    }

    public function saveGenerated($path, $path_name, $path_uuid, $user_uuid, $comment, $code, $tipo)
    {
        date_default_timezone_set("America/El_Salvador");
        $regex = '/^[\p{L}\p{N}\s.,;:!?\'"áéíóúÁÉÍÓÚñÑ]+$/u';
        if(preg_match($regex, $comment)){
            $sql = 'INSERT INTO rotulos_generados (path, path_name, path_uuid, user_uuid, comentario, code, tipo) VALUES (?, ?, ?, ?, ?, ?, ?)';
            $this->ejecutarConsulta($sql, [$path, $path_name, $path_uuid, $user_uuid, $comment, $code, $tipo]);
        }else{
            $sql = 'INSERT INTO rotulos_generados (path, path_name, path_uuid, user_uuid, comentario, code, tipo) VALUES (?, ?, ?, ?, ?, ?, ?)';
            $this->ejecutarConsulta($sql, [$path, $path_name, $path_uuid, $user_uuid, NULL, $code, $tipo]);
        }
    }

    public function assignDocument($path_uuid, $user_uuid)
    {
        $sql = 'UPDATE rotulos SET path_uuid = ? WHERE user_uuid = ? AND path_uuid IS NULL';
        $this->ejecutarConsulta($sql, [$path_uuid, $user_uuid]);
    }

    public function assignDocumentSmall($path_uuid, $user_uuid)
    {
        $sql = 'UPDATE rotulos_mini SET path_uuid = ? WHERE user_uuid = ? AND path_uuid IS NULL';
        $this->ejecutarConsulta($sql, [$path_uuid, $user_uuid]);
    }

}
