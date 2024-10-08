<?php
// URL de la API
$url = 'https://texttospeech.googleapis.com/v1/text:synthesize';

// Datos de la petición
$data = [
    'input' => [
        'text' => 'Hola, ¿cómo estás?'
    ],
    'voice' => [
        'languageCode' => 'es-ES',
        'ssmlGender' => 'NEUTRAL'
    ],
    'audioConfig' => [
        'audioEncoding' => 'MP3'
    ]
];

// Autenticación: Reemplaza con tu token de Google Cloud
$accessToken = 'ya29.a0AcM612wx4p6MnHf9uAwq9wz8XSLsbmeV_8s00rmv4XGB87Q5ckJhnK-_kt-kgMwxvUT8qGdn4GR55RpcHQhHb1R6OyzSyiAAw0MCa5pRuLN57P_Uq3B4-HR2HBhzcbe4hHPAp6fKIdtQdwtmD8PH_2NwlzOfaNqdO9PODqDnROYaCgYKASASARESFQHGX2MiuDWY70tjzbeb5k_D-o1xow0178'; 
// Debes generar el token de acceso con Google Cloud SDK

// Inicializa la sesión cURL
$ch = curl_init($url);

// Configuración de la petición
curl_setopt($ch, CURLOPT_POST, true); // Solicitud POST
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken, // Autenticación Bearer
    'Content-Type: application/json; charset=utf-8' // Tipo de contenido
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Datos a enviar
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Para obtener la respuesta
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Deshabilita la verificación SSL (opcional)

// Ejecuta la petición
$response = curl_exec($ch);

// Cierra la sesión cURL
curl_close($ch);

// Guarda el archivo de audio
if ($response) {
    $responseData = json_decode($response, true);
    
    if (isset($responseData['audioContent'])) {
        $audioContent = base64_decode($responseData['audioContent']); // Decodifica el contenido base64
        file_put_contents('output.mp3', $audioContent); // Guarda el archivo MP3
        echo 'Audio guardado como output.mp3';
    } else {
        echo 'Error: ' . $response;
    }
} else {
    echo 'Error al ejecutar la solicitud';
}

?>
