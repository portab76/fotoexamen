<?php
class OpenAIImageRequest {
    private $api_key;
    private $image_path;

    // Constructor de la clase
    public function __construct($api_key, $image_path) {
        $this->api_key = $api_key;
        $this->image_path = $image_path;
    }

    // Función para codificar la imagen en Base64
    private function encode_image() {
        // Abrir la imagen en modo lectura binaria
        $image_data = file_get_contents($this->image_path);
        return base64_encode($image_data);
    }

    // Función para enviar la solicitud a la API de OpenAI
    public function send_request() {
        // Codificar la imagen en Base64
        $base64_image = $this->encode_image();

        // URL de la API de OpenAI
        $url = "https://api.openai.com/v1/chat/completions";

        // Crear el payload (datos a enviar)
        $payload = [
            "model" => "gpt-4-turbo-2024-04-09",
            "messages" => [
                [
                    "role" => "user",
                    "content" => [
                        [
                            "type" => "text",
                            "text" => "En base al temario oficial del examen para el certificado de Patrón de Embarcaciones de Recreo (PER), cual es la respuesta correcta de las preguntas que adjunto en la imagen. Expresamente responde solamente el numero de pregunta y la letra correspondiente a la respuesta correcta. Sin escribir ningún comentario más."
                        ],
                        [
                            "type" => "image_url",
                            "image_url" => [
                                "url" => "data:image/jpeg;base64," . $base64_image
                            ]
                        ]
                    ]
                ]
            ],
            "max_tokens" => 300
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

