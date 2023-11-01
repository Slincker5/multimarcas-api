<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/clases/Database.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Ramsey\Uuid\UuidFactory;


class Rotulo extends Database {

    private $response = [];

    public function __construct($barra = '', $descripcion = '', $precio = '', $f_inicio = '', $f_fin = '', $cantidad = '', $user_uuid = ''){
        $this->barra = $barra;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->f_inicio = $f_inicio;
        $this->f_fin = $f_fin;
        $this->cantidad = $cantidad;
        $this->user_uuid = $user_uuid;

    }
    public function listadoRotulos($user_uuid){
        $sql = 'SELECT * FROM rotulos WHERE user_uuid = ?  AND path_uuid IS NULL ORDER BY id DESC';
        $registrar = $this->ejecutarConsulta($sql, [$user_uuid]);
        $datos = $registrar->fetchAll(PDO::FETCH_ASSOC);
        return $datos;
    }
    
    public function listadoRotulosMini($user_uuid){
        $sql = 'SELECT * FROM rotulos_mini WHERE user_uuid = ?  AND path_uuid IS NULL ORDER BY id DESC';
        $registrar = $this->ejecutarConsulta($sql, [$user_uuid]);
        $datos = $registrar->fetchAll(PDO::FETCH_ASSOC);
        return $datos;
    }

    public function listaAfiches($user_uuid){
        $sql = 'SELECT * FROM rotulos WHERE user_uuid = ?  AND path_uuid IS NULL ORDER BY id DESC';
        $response = $this->ejecutarConsulta($sql, [$user_uuid]);
        $datos = $response->fetchAll(PDO::FETCH_ASSOC);
        return $datos;
    }

    public function crearRotulo(){
        
        date_default_timezone_set("America/El_Salvador");
        $this->response['status'] = 'error';

        if(empty($this->descripcion) || empty($this->precio) || empty($this->cantidad)){
            $this->response['massage'] = 'Debes completar todos los campos';
            return $this->response;
        }else if(!is_numeric($this->cantidad)){
            $this->response['message'] = 'La cantidad de cintillos debe ser en numeros';
            return $this->response;
          }else if($this->cantidad > 200){
            $this->response['message'] = 'El limite de rotulos por crear es de 200';
            return $this->response;
        }else{
            #CREAR UUID PARA CADA ROTULO
            $uuidFactory = new UuidFactory();
            $uuid = $uuidFactory->uuid4();
            $rotulo_uuid = $uuid->toString();
            
            for($i = 1;$i <= $this->cantidad;$i++){
                if($this->barra == ''){
                    $this->barra = ' ';
                }
                $sql = 'INSERT INTO  rotulos (barra, descripcion, precio, f_inicio, f_fin, cantidad, user_uuid, uuid) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
                $crear = $this->ejecutarConsulta($sql, [$this->barra, $this->descripcion, $this->precio, $this->f_inicio, $this->f_fin, $this->cantidad, $this->user_uuid, $rotulo_uuid]);
                if(!$crear){
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
        

        public function crearRotuloMini(){
        
            date_default_timezone_set("America/El_Salvador");
            $this->response['status'] = 'error';
    
            if(empty($this->descripcion) || empty($this->precio) || empty($this->cantidad)){
                $this->response['massage'] = 'Debes completar todos los campos';
                return $this->response;
            }else if(!is_numeric($this->cantidad)){
                $this->response['message'] = 'La cantidad de cintillos debe ser en numeros';
                return $this->response;
              }else if($this->cantidad > 90){
                $this->response['message'] = 'El limite de rotulos por crear es de 90';
                return $this->response;
            }else{
                #CREAR UUID PARA CADA ROTULO
                $uuidFactory = new UuidFactory();
                $uuid = $uuidFactory->uuid4();
                $rotulo_uuid = $uuid->toString();
                
                for($i = 1;$i <= $this->cantidad;$i++){
                    if($this->barra == ''){
                        $this->barra = ' ';
                    }
                    $sql = 'INSERT INTO  rotulos_mini (barra, descripcion, precio, f_inicio, f_fin, cantidad, user_uuid, uuid) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
                    $crear = $this->ejecutarConsulta($sql, [$this->barra, $this->descripcion, $this->precio, $this->f_inicio, $this->f_fin, $this->cantidad, $this->user_uuid, $rotulo_uuid]);
                    if(!$crear){
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

        public function guardarGenerados($path, $path_name, $path_uuid, $user_uuid){
            date_default_timezone_set("America/El_Salvador");
            $sql = 'INSERT INTO rotulos_generados (path, path_name, path_uuid, user_uuid) VALUES (?,?,?,?)';
            $this->ejecutarConsulta($sql, [$path, $path_name, $path_uuid, $user_uuid]);
          }
        
        public function asignarDocumento($path_uuid, $user_uuid){
            $sql = 'UPDATE rotulos SET path_uuid = ? WHERE user_uuid = ? AND path_uuid IS NULL';
            $this->ejecutarConsulta($sql, [$path_uuid, $user_uuid]);
          }
          public function asignarDocumentoMini($path_uuid, $user_uuid){
            $sql = 'UPDATE rotulos_mini SET path_uuid = ? WHERE user_uuid = ? AND path_uuid IS NULL';
            $this->ejecutarConsulta($sql, [$path_uuid, $user_uuid]);
          }

    }
