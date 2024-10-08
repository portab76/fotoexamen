<?php

// Reemplaza con tus credenciales reales de Gemini
$geminiApiKey = "YOUR_GEMINI_API_KEY";
$geminiApiSecret = "YOUR_GEMINI_API_SECRET";

// Configuraciones de la API de Gemini (ajusta según la versión de la API que uses)
$geminiApiUrl = "https://api.gemini.com/v1/images/analyze"; // Revisa la URL correcta en la documentación de Gemini

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se subió un archivo
    if (isset($_FILES["image"])) {
        $file = $_FILES["image"];
		$question = $_POST["question"];
        // Verificar si la subida fue exitosa
        if ($file["error"] === UPLOAD_ERR_OK) {
            // Mover el archivo a una ubicación temporal
            $tempFile = tempnam(sys_get_temp_dir(), "gemini_");
            move_uploaded_file($file["tmp_name"], $tempFile);

            // Preparar los datos para enviar a la API
            $data = [
                'image' => base64_encode(file_get_contents($tempFile)), // Codifica la imagen en base64
				'question' => $question, // Agregar la pregunta al array de datos
                // Agrega otros parámetros de la solicitud según la documentación de Gemini
                // Ejemplo:  'model' => 'your_model_name',
            ];

            // Crear la firma de la solicitud (requiere la librería 'hash_hmac' en PHP 7.2+)
            $timestamp = time();
            $payload = json_encode($data);
            $nonce = $timestamp;  // Usa un nonce único para cada solicitud
            $message = $nonce . $geminiApiUrl . $payload;
            $signature = hash_hmac('sha256', $message, $geminiApiSecret);


            // Crear la cabecera de la solicitud
            $headers = [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload),
                'Gemini-API-Key: ' . $geminiApiKey,
                'Gemini-API-Nonce: ' . $nonce,
                'Gemini-API-Signature: ' . $signature,
                // Añade otras cabeceras necesarias según la documentación de la API
            ];


            // Enviar la solicitud a la API de Gemini
            $ch = curl_init($geminiApiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);


            // Procesar la respuesta
            if ($httpCode == 200) {
                $result = json_decode($response, true);
                // Procesar los resultados de la API de Gemini
                echo "<h2>Resultados de Gemini:</h2><pre>";
                print_r($result);
                echo "</pre>";
            } else {
                echo "Error al llamar a la API de Gemini. Código de estado: " . $httpCode . "<br>";
                echo "Respuesta: " . $response;
            }

            // Limpiar el archivo temporal
            unlink($tempFile);
        } else {
            echo "Error al subir el archivo: " . $file["error"];
        }
    } else {
        echo "No se ha subido ninguna imagen.";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Subir imagen a Gemini</title>
</head>
<body>
    <h1>Subir imagen a Gemini</h1>
    <form method="post" enctype="multipart/form-data">
        Selecciona una imagen: <input type="file" name="image"><br>
        Introduce tu pregunta: <input type="text" name="question" value="Identifica los numero de preguntas , texto pregunta y respuesteas de la imagen adjunto y expresamente obten los resultados en este formato json: { \"numero\":int,\"pregunta\": String,\"respuestas\":String}, no repitas las preguntas."><br>
        <input type="submit" value="Enviar">
    </form>
</body>
</html>


