<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/clases/Database.php';

class Comentario extends Database {

    protected $response;

    public function __construct(){
          $this->response = [
                'status' => '',
                'message' => ''
                ];
    }

    public function createPost($user_uuid, $username, $photo = '', $message) {
      if(empty($message)){
        $this->response['status'] = 'error';
        $this->response['message'] = 'Tu publicación no puede estar vacía';
        return $this->response;
      }else if(strlen($message) > 1000){
        $this->response['status'] = 'error';
        $this->response['message'] = 'Tu publicación excede el límite de 1000 carácters';
        return $this->response;
      }else{
        $sql = 'INSERT INTO comentarioa (user_uuid, username, photo, message) VALUES (?, ?, ?, ?)';
        $publicar = $this->ejecutarConsulta($sql, [$user_uuid, $username, $photo, $message]);
        if($publicar){
          $this->response['status'] = 'OK';
          $this->response['message'] = 'Publicación exitosa';
          return $this->response;
        }
      }
    }
}