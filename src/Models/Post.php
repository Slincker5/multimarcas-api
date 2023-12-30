<?php
namespace App\Models;

use App\Models\Database;
use Ramsey\Uuid\UuidFactory;

class Post extends Database
{

    protected $response = [];
    protected $user_uuid;
    protected $username;

    public function __construct($user_uuid, $username)
    {
        $this->user_uuid = $user_uuid;
        $this->username = $username;
    }

    public function newPost($message)
    {
        $message = trim(strip_tags($message));
        if (empty($message) || strlen($message) <= 0) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'Tu publicación no puede estar vacía';
            return $this->response;
        } else if (strlen($message) > 2000) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'Tu publicación excede el límite de 2000 caracteres.';
            return $this->response;
        } else {
            #CREAR UUID PARA CADA ROTULO
            $uuidFactory = new UuidFactory();
            $uuid = $uuidFactory->uuid4();
            $post_uuid = $uuid->toString();
            $sql = 'INSERT INTO publicaciones (post_uuid, user_uuid, username, message) VALUES (?, ?, ?, ?)';
            $publicar = $this->ejecutarConsulta($sql, [$post_uuid, $this->user_uuid, $this->username, $message]);
            if ($publicar) {
                $this->response['status'] = 'OK';
                $this->response['message'] = 'Publicación exitosa';
                return $this->response;
            }
        }
    }
}
