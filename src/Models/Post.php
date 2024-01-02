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

    public function newPost($message, $photo)
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
            $sql = 'INSERT INTO publicaciones (post_uuid, user_uuid, username, post, photo) VALUES (?, ?, ?, ?, ?)';
            $publicar = $this->ejecutarConsulta($sql, [$post_uuid, $this->user_uuid, $this->username, $message, $photo]);
            if ($publicar) {
                $this->response['status'] = 'OK';
                $this->response['message'] = 'Publicación exitosa';
                return $this->response;
            }
        }
    }

    public function listPost()
    {
        $sql = 'SELECT * FROM publicaciones ORDER BY fecha DESC';
        $list = $this->ejecutarConsulta($sql);
        $posts = $list->fetchAll(\PDO::FETCH_ASSOC);
        return $posts;

    }

    public function removePost($post_uuid)
    {
        $admin_uuid = "2c62e966-63d8-4bfd-832e-89094ae47eec";
        if ($this->user_uuid === $admin_uuid) {
            $sql = 'DELETE FROM publicaciones WHERE post_uuid';
            $remove = $this->ejecutarConsulta($sql, [$post_uuid]);
        } else {
            $sql = 'DELETE FROM publicaciones WHERE post_uuid = ? AND user_uuid = ?';
            $remove = $this->ejecutarConsulta($sql, [$post_uuid, $this->user_uuid]);
        }
    }
}
