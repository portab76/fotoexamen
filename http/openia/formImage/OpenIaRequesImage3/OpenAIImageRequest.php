<?php
class OpenAIImageRequest {
    private $api_key;
    private $base64_image;

    // Constructor de la clase
    public function __construct($api_key, $base64_image) {
        $this->api_key = $api_key;
        $this->base64_image = $base64_image;
    }

    // Función para enviar la solicitud a la API de OpenAI
    public function send_request() {

        // URL de la API de OpenAI
        $url = "https://api.openai.com/v1/chat/completions";

        // Crear el payload (datos a enviar)
        $payload = [
            "model" => "gpt-4o",
            "messages" => [
                [
                    "role" => "user",
                    "content" => [
                        [
                            "type" => "text",
                            "text" => "Identifica los numero de preguntas , texto pregunta y respuesteas de la imagen adjunto y expresamente obten los resultados en este formato json: { \"numero\":int,\"pregunta\": String,\"respuestas\":String}, no repitas las preguntas."
                        ],
                        [
                            "type" => "image_url",
                            "image_url" => [
                                "url" => "data:image/jpeg;base64," . $this->base64_image
                            ]
                        ]
                    ]
                ]
            ],
            "n" => 1
        ];

        // Configurar los encabezados para la solicitud
        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->api_key
        ];

        // Inicializar la sesión cURL
        $ch = curl_init($url);

        // Configurar las opciones de cURL
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($ch);

        // Verificar si ocurrió un error
        if (curl_errno($ch)) {
            echo 'Error en la solicitud: ' . curl_error($ch);
        }

        // Cerrar la sesión cURL
        curl_close($ch);

        // Decodificar la respuesta JSON
        return json_decode($response, true);
    }
}
?>

