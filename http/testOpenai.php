<?php
$apiKey = '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';
$apiUrl = 'https://api.openai.com/v1/chat/completions';

function generateNames() {
    global $apiKey, $apiUrl;

    $params = [
        'prompt' => 'Generate sample names for my black pet dog',
        'model' => 'gpt-3.5-turbo-0301',
        'temperature' => 0.6,
    ];

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ];

    $options = [
        'http' => [
            'header'  => implode("\r\n", $headers),
            'method'  => 'POST',
            'content' => json_encode($params),
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($apiUrl, false, $context);

    if ($result === FALSE) {
        echo "Error occurred";
        return [];
    }

    $response = json_decode($result, true);
    $names = array_map('trim', array_column($response['choices'], 'text'));

    return $names;
}

$names = generateNames();
echo 'Generated names: ' . implode(', ', $names);

?>
