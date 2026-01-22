<?php
require __DIR__ . '/../config/env.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$prompt = trim($_POST['prompt'] ?? '');
if ($prompt === '') {
    echo "Prompt empty";
    exit;
}

/*
 Cohere NEW Chat API (2025)
 Model: command-a-03-2025
*/

$payload = [
    "model" => "command-a-03-2025",
    "messages" => [
        [
            "role" => "user",
            "content" => [
                [
                    "type" => "text",
                    "text" => $prompt
                ]
            ]
        ]
    ]
];

$ch = curl_init("https://api.cohere.ai/v2/chat");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer " . COHERE_KEY
    ],
    CURLOPT_POSTFIELDS => json_encode($payload)
]);

$response = curl_exec($ch);

if ($response === false) {
    echo "CURL ERROR: " . curl_error($ch);
    exit;
}

curl_close($ch);

$data = json_decode($response, true);

// ✅ SUCCESS OUTPUT
if (isset($data['message']['content'][0]['text'])) {
    echo $data['message']['content'][0]['text'];
    exit;
}

// ❌ ERROR DEBUG
echo "<pre>";
print_r($data);
echo "</pre>";
