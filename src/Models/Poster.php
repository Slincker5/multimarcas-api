<?php
namespace App\Models;

use App\Models\Database;
use App\Models\Premiun;
use Ramsey\Uuid\UuidFactory;

class Poster extends Database
{

    private $response;
    private $barra;
    private $descripcion;
    private $precio;
    private $f_inicio;
    private $f_fin;
    private $cantidad;
    private $user_uuid;
    private $instanciaPremium;
    private $estadoPremium;

    public function __construct($barra = '', $descripcion = '', $precio = '', $f_inicio = '', $f_fin = '', $cantidad = '', $user_uuid = '')
    {
        $this->instanciaPremium = new Premiun($user_uuid);
        $this->estadoPremium = $this->instanciaPremium->validarSuscripcion();
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

    public function listPosterSmallDesc($user_uuid)
    {
        $sql = 'SELECT * FROM rotulos_mini_desc WHERE user_uuid = ?  AND path_uuid IS NULL ORDER BY id DESC';
        $registrar = $this->ejecutarConsulta($sql, [$user_uuid]);
        $datos = $registrar->fetchAll(\PDO::FETCH_ASSOC);
        return $datos;
    }

    public function listPosterLowPriceSmall($user_uuid)
    {
        $sql = 'SELECT * FROM rotulos_mini_baja WHERE user_uuid = ?  AND path_uuid IS NULL ORDER BY id DESC';
        $registrar = $this->ejecutarConsulta($sql, [$user_uuid]);
        $datos = $registrar->fetchAll(\PDO::FETCH_ASSOC);
        return $datos;
    }

    public function createPoster()
    {

        date_default_timezone_set("America/El_Salvador");
        if ($this->estadoPremium) {
            $this->response['status'] = 'error';
            $this->response['message'] = "Necesitas ser usuario premiun para esta accion";
            return $this->response;
        } else {

            if (empty($this->descripcion) || empty($this->precio) || empty($this->cantidad)) {
                $this->response['status'] = 'error';
                $this->response['massage'] = 'Debes completar todos los campos';
                return $this->response;
            } else if (!is_numeric($this->cantidad)) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'La cantidad de cintillos debe ser en numeros';
                return $this->response;
            } else if ($this->cantidad > 200) {
                $this->response['status'] = 'error';
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
    }

    public function createPosterSmall()
    {

        date_default_timezone_set("America/El_Salvador");
        if ($this->estadoPremium) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'Necesitas ser usuario premiun para esta accion';
            return $this->response;
        } else {

            if (empty($this->descripcion) || empty($this->precio) || empty($this->cantidad)) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'Debes completar todos los campos';
                return $this->response;
            } else if (!is_numeric($this->cantidad)) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'La cantidad de cintillos debe ser en numeros';
                return $this->response;
            } else if ($this->cantidad > 90) {
                $this->response['status'] = 'error';
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
    }

    public function createPosterSmallDesc($descuento)
    {

        date_default_timezone_set("America/El_Salvador");
        if ($this->estadoPremium) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'Necesitas ser usuario premiun para esta accion';
            return $this->response;
        } else {
            if(count($this->listPosterSmallDesc($this->user_uuid)) > 27){
                $this->response['status'] = 'error';
                $this->response['message'] = 'El documento ha llegado a 27 rotulos.';
                return $this->response;
            } else if (empty($this->descripcion) || empty($this->precio) || empty($this->cantidad)) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'Debes completar todos los campos';
                return $this->response;
            } else if (!is_numeric($this->cantidad)) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'La cantidad de rotulos debe ser en numeros';
                return $this->response;
            } else if ($this->cantidad > 27) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'El limite de rotulos por crear es de 27';
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
                    $sql = 'INSERT INTO  rotulos_mini_desc (descuento, descripcion, precio, f_fin, cantidad, user_uuid, uuid) VALUES (?, ?, ?, ?, ?, ?, ?)';
                    $crear = $this->ejecutarConsulta($sql, [$descuento, $this->descripcion, $this->precio, $this->f_fin, $this->cantidad, $this->user_uuid, $poster_uuid]);
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
    }

    public function createPosterLowPriceSmall()
    {

        date_default_timezone_set("America/El_Salvador");

        if ($this->estadoPremium) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'Necesitas ser usuario premiun para esta accion';
            return $this->response;
        } else {

            if (empty($this->descripcion) || empty($this->precio) || empty($this->cantidad)) {
                $this->response['status'] = 'error';
                $this->response['message'] = "Debes llenar todos los campos";
                return $this->response;
            } else if (!is_numeric($this->cantidad)) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'La cantidad de rotulos debe ser en numeros';
                return $this->response;
            } else if ($this->cantidad > 90) {
                $this->response['status'] = 'error';
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
                    $sql = 'INSERT INTO  rotulos_mini_baja (barra, descripcion, precio, cantidad, user_uuid, uuid) VALUES (?, ?, ?, ?, ?, ?)';
                    $crear = $this->ejecutarConsulta($sql, [$this->barra, $this->descripcion, $this->precio, $this->cantidad, $this->user_uuid, $poster_uuid]);
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
    }

    public function removePosterSmall($uuid, $user_uuid)
    {
        $sql = 'DELETE FROM rotulos_mini WHERE uuid = ? AND user_uuid = ?';
        $eliminar = $this->ejecutarConsulta($sql, [$uuid, $user_uuid]);
        if ($eliminar) {
            $this->response['status'] = 'OK';
            $this->response['message'] = 'Se elimino el rotulo correctamente';
            return $this->response;
        }
    }

    public function removePosterSmallDesc($uuid, $user_uuid)
    {
        $sql = 'DELETE FROM rotulos_mini_desc WHERE uuid = ? AND user_uuid = ?';
        $eliminar = $this->ejecutarConsulta($sql, [$uuid, $user_uuid]);
        if ($eliminar) {
            $this->response['status'] = 'OK';
            $this->response['message'] = 'Se elimino el rotulo correctamente';
            return $this->response;
        }
    }

    public function saveGenerated($path, $path_name, $path_uuid, $user_uuid, $comment, $code, $tipo)
    {
        date_default_timezone_set("America/El_Salvador");
        $regex = '/^[\p{L}\p{N}\s.,;:!?\'"áéíóúÁÉÍÓÚñÑ]+$/u';
        if (preg_match($regex, $comment)) {
            $sql = 'INSERT INTO rotulos_generados (path, path_name, path_uuid, user_uuid, comentario, code, tipo) VALUES (?, ?, ?, ?, ?, ?, ?)';
            $this->ejecutarConsulta($sql, [$path, $path_name, $path_uuid, $user_uuid, $comment, $code, $tipo]);
        } else {
            $sql = 'INSERT INTO rotulos_generados (path, path_name, path_uuid, user_uuid, comentario, code, tipo) VALUES (?, ?, ?, ?, ?, ?, ?)';
            $this->ejecutarConsulta($sql, [$path, $path_name, $path_uuid, $user_uuid, null, $code, $tipo]);
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

    public function assignDocumentSmallDesc($path_uuid, $user_uuid)
    {
        $sql = 'UPDATE rotulos_mini_desc SET path_uuid = ? WHERE user_uuid = ? AND path_uuid IS NULL';
        $this->ejecutarConsulta($sql, [$path_uuid, $user_uuid]);
    }

    public function assignDocumentLowPriceSmall($path_uuid, $user_uuid)
    {
        $sql = 'UPDATE rotulos_mini_baja SET path_uuid = ? WHERE user_uuid = ? AND path_uuid IS NULL';
        $this->ejecutarConsulta($sql, [$path_uuid, $user_uuid]);
    }
}
