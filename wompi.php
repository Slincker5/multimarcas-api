<?php

function obtenerToken() {
    // URL para obtener el token
    $url = "https://id.wompi.sv/connect/token";
    
    // Datos del formulario
    $data = [
        'audience' => 'wompi_api',
        'grant_type' => 'client_credentials',
        'client_id' => 'b1afc4a4-b0cf-4ff8-a9c7-4c58947ecdca',
        'client_secret' => '629b9c12-31b8-4772-a0fe-9f0c10641b7e'
    ];
    
    // Configuración de cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    
    // Ejecutar la petición y obtener la respuesta
    $response = curl_exec($ch);
    
    // Verificar si hubo un error en la petición
    if ($response === false) {
        die('Error en la petición: ' . curl_error($ch));
    }
    
    // Cerrar la sesión de cURL
    curl_close($ch);
    
    // Decodificar la respuesta JSON
    $respuestaDecodificada = json_decode($response, true);
    
    return $respuestaDecodificada['access_token'];
}

// Obtener el token de acceso
$token = obtenerToken();
echo "Token de acceso: " . $token;

?>
