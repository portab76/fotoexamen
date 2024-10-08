<?php
// Importar la clase OpenAIImageRequest
require 'OpenAIImageRequest.php';

// API Key de OpenAI (la debes reemplazar por tu propia clave)
$api_key = "sk-FanGSGBCBJfqh_qq884akRwoyeIRfHCiBSirbjFu5rT3BlbkFJkJxEOxsmbCmvNc0c8yf6c9WqPCrmWcITVo16d0PFAA";

// Ruta a la imagen que quieres codificar
$image_path = "C:/temp/pic.jpg";

// Crear una instancia de la clase OpenAIImageRequest
$openAIRequest = new OpenAIImageRequest($api_key, $image_path);

// Enviar la solicitud y obtener la respuesta
$response = $openAIRequest->send_request();

// Mostrar la respuesta
echo "<pre>";
print_r($response);
echo "</pre>";
?>
