<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/clases/Database.php';

class Producto extends Database {

    protected $response;

    public function __construct(){
          $this->response = [
                'status' => '',
                'msg' => ''
                ];
    }

    public function obtenerProductos(){
          $sql = 'SELECT * FROM codigos_global WHERE categoria <> "NIVEA" && categoria <> "LAB SUIZOS"';
          $res = $this->ejecutarConsulta($sql);
          $datos = $res->fetchAll(PDO::FETCH_ASSOC);
          return $datos;
    }

    public function detallesProducto($interno){
      $sql = 'SELECT * FROM codigos_global WHERE interno = ?';
      $res = $this->ejecutarConsulta($sql, [$interno]);
      $datos = $res->fetchAll(PDO::FETCH_ASSOC);
      return $datos;
    }
    
    public function editarProducto($interno, $items = ['barra' => '', 'descripcion' => '', 'factorEmpaque' => '', 'precio' => '']) {
          $sql = 'UPDATE codigos_global SET barra = ?, descripcion = ?, factorEmpaque = ?, precio = ? WHERE interno = ?';
          $res = $this->ejecutarConsulta($sql, [$items['barra'], $items['descripcion'], $items['factorEmpaque'], $items['precio'], $interno]);
          $this->response['status'] = 'OK';
          $this->response['msg'] = 'Se actualizo registro';
          return  $this->response;
    }

    public function eliminarProducto($interno){
          $sql = 'DELETE FROM codigos_global WHERE interno = ?';
          $res = $this->ejecutarConsulta($sql, [$interno]);
          $this->response['status'] = 'OK';
          $this->response['msg'] = 'Se ha eliminado el registro';
          return  $this->response;
    }
}