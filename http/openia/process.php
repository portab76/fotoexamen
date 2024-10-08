<?php

// Verifica si se ha enviado un archivo y una pregunta
if (isset($_FILES['image']) && isset($_POST['question'])) {
    $image = $_FILES['image'];
    $question = $_POST['question'];

    // Ruta temporal donde se guarda la imagen subida
    $imagePath = $image['tmp_name'];

    // Nombre del archivo para referencia (opcional)
    $imageName = $image['name'];

    // Carga el archivo (opcional, si es necesario hacer algo con él)
    $imageData = file_get_contents($imagePath);

    // Define tu API Key de OpenAI
    $apiKey = 'ya29.a0AcM612wx4p6MnHf9uAwq9wz8XSLsbmeV_8s00rmv4XGB87Q5ckJhnK-_kt-kgMwxvUT8qGdn4GR55RpcHQhHb1R6OyzSyiAAw0MCa5pRuLN57P_Uq3B4-HR2HBhzcbe4hHPAp6fKIdtQdwtmD8PH_2NwlzOfaNqdO9PODqDnROYaCgYKASASARESFQHGX2MiuDWY70tjzbeb5k_D-o1xow0178';  
	// Reemplaza con tu API Key de OpenAI

    // La solicitud que se envía a OpenAI
    $postData = json_encode([
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'Actúa como un analizador de imágenes.'],
            ['role' => 'user', 'content' => "He subido una imagen llamada $imageName. Mi pregunta es: $question."]
        ]
    ]);

    // Inicia una sesión cURL
    $ch = curl_init();
    // Configura las opciones cURL
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    // Ejecuta la solicitud cURL y captura la respuesta
    $response = curl_exec($ch);

    // Cierra la sesión cURL
    curl_close($ch);

    // Decodifica la respuesta de la API de OpenAI
    $decodedResponse = json_decode($response, true);

    // Verifica si hay una respuesta válida
    if (isset($decodedResponse['choices'][0]['message']['content'])) {
        $answer = $decodedResponse['choices'][0]['message']['content'];
    } else {
        $answer = "No se pudo obtener una respuesta de la API.";
    }

    // Muestra la respuesta en la página
    echo "<h1>Pregunta:</h1>";
    echo "<p>$question</p>";
    echo "<h1>Respuesta:</h1>";
    echo "<p>$answer</p>";
} else {
    echo "Por favor sube una imagen y haz una pregunta.";
}
