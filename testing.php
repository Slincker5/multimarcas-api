<?php
// Verifica que la solicitud sea un POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene el contenido del cuerpo de la solicitud
    $payload = file_get_contents('php://input');

    // Decodifica el JSON
    $data = json_decode($payload, true);

    // Puedes realizar acciones con los datos recibidos, por ejemplo, almacenarlos en una base de datos
    // o enviar notificaciones por correo electrónico

    // Aquí puedes agregar tu lógica de manejo de datos
    // ...

    // Responde a Wompi para indicar que la notificación fue recibida correctamente
    http_response_code(200);
    echo "OK";
} else {
    // Si la solicitud no es un POST, responde con un código de error
    http_response_code(405);
    echo "Method Not Allowed";
}
