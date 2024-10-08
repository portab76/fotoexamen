<?php
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "model" => "gpt-3.5-turbo",
    "messages" => [
        ["role" => "user", "content" => "Hello!"]
    ]
]));

$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer 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'
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

echo $result;
?>
