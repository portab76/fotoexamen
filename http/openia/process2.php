<?php
// OpenAI API Key
$api_key = 'ya29.a0AcM612wx4p6MnHf9uAwq9wz8XSLsbmeV_8s00rmv4XGB87Q5ckJhnK-_kt-kgMwxvUT8qGdn4GR55RpcHQhHb1R6OyzSyiAAw0MCa5pRuLN57P_Uq3B4-HR2HBhzcbe4hHPAp6fKIdtQdwtmD8PH_2NwlzOfaNqdO9PODqDnROYaCgYKASASARESFQHGX2MiuDWY70tjzbeb5k_D-o1xow0178';

// Function to encode the image in base64
function encode_image($image_path) {
    $image_data = file_get_contents($image_path);
    return base64_encode($image_data);
}

// Path to your image
$image_path = "C:\\temp\\pic.jpg";

// Getting the base64 string
$base64_image = encode_image($image_path);




// Headers for the request
$headers = [
    "Content-Type: application/json",
    "Authorization: Bearer $api_key"
];

// Payload for the request
$payload = [
    "model" => "gpt-4o-mini",
    "messages" => [
        [
            "role" => "user",
            "content" => [
                [
                    "type" => "text",
                    "text" => "What’s in this image?"
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

// Making the request
$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Get the response
$response = curl_exec($ch);
curl_close($ch);

// Print the response
echo $response;
?>
