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
            #CREAR UUID PARA CADA POST
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

    public function newComment($message, $photo, $post_uuid)
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
            #CREAR UUID PARA CADA POST
            $uuidFactory = new UuidFactory();
            $uuid = $uuidFactory->uuid4();
            $comment_uuid = $uuid->toString();
            $sql = 'INSERT INTO comentarios (comment_uuid, post_uuid, user_uuid, username, post, photo) VALUES (?, ?, ?, ?, ?, ?)';
            $publicar = $this->ejecutarConsulta($sql, [$comment_uuid, $post_uuid, $this->user_uuid, $this->username, $message, $photo]);
            if ($publicar) {
                $this->response['status'] = 'OK';
                $this->response['message'] = 'comentario agregado';
                return $this->response;
            }
        }
    }

    public function listPost()
    {
        $sql = 'SELECT p.*, COUNT(l.id) AS num_likes, COUNT(c.id) AS num_comments
            FROM publicaciones p
            LEFT JOIN likes l ON p.post_uuid = l.post_uuid
            LEFT JOIN comentarios c ON p.post_uuid = c.post_uuid
            GROUP BY p.post_uuid
            ORDER BY p.fecha DESC';

        $list = $this->ejecutarConsulta($sql);
        $posts = $list->fetchAll(\PDO::FETCH_ASSOC);
        return $posts;
    }

    public function selectPost($post_uuid)
    {
        $sql = 'SELECT p.*, COUNT(l.id) AS num_likes, COUNT(c.id) AS num_comments
            FROM publicaciones p
            LEFT JOIN likes l ON p.post_uuid = l.post_uuid
            LEFT JOIN comentarios c ON p.post_uuid = c.post_uuid
            WHERE p.post_uuid = ?
            GROUP BY p.post_uuid
            ORDER BY p.fecha DESC';

        $list = $this->ejecutarConsulta($sql, [$post_uuid]);
        $posts = $list->fetchAll(\PDO::FETCH_ASSOC);
        return $posts;
    }

    public function removePost($post_uuid)
    {
        $admin_uuid = "2c62e966-63d8-4bfd-832e-89094ae47eec";
        if ($this->user_uuid === $admin_uuid) {
            $sql = 'DELETE FROM publicaciones WHERE post_uuid = ?';
            $remove = $this->ejecutarConsulta($sql, [$post_uuid]);
        } else {
            $sql = 'DELETE FROM publicaciones WHERE post_uuid = ? AND user_uuid = ?';
            $remove = $this->ejecutarConsulta($sql, [$post_uuid, $this->user_uuid]);
        }
        $this->response['status'] = 'OK';
        $this->response['message'] = 'Se elimino tu publicacion con exito.';
        return $this->response;
    }

    public function likePost($post_uuid)
    {
        $sqlBuscar = 'SELECT post_uuid, user_uuid FROM likes WHERE post_uuid = ? AND user_uuid = ?';
        $searchLike = $this->ejecutarConsulta($sqlBuscar, [$post_uuid, $this->user_uuid]);
        $verify = $searchLike->fetchAll(\PDO::FETCH_ASSOC);
        if (count($verify) === 0) {
            #CREAR UUID PARA CADA LIKE
            $uuidFactory = new UuidFactory();
            $uuid = $uuidFactory->uuid4();
            $like_uuid = $uuid->toString();
            $sql = 'INSERT INTO likes (like_uuid, post_uuid, user_uuid) VALUES (?, ?, ?)';
            $like = $this->ejecutarConsulta($sql, [$like_uuid, $post_uuid, $this->user_uuid]);
            $this->response['status'] = 'OK';
            $this->response['message'] = 'Te gusta esta publicacion.';
            return $this->response;
        }

    }
    public function listLikePost()
    {
        $sql = 'SELECT * FROM likes';
        $likes = $this->ejecutarConsulta($sql, []);
        $list = $likes->fetchAll(\PDO::FETCH_ASSOC);
        return $list;

    }
}
