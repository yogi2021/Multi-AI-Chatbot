<?php
require __DIR__ . '/../config/env.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$prompt = $_POST['prompt'] ?? '';

if (!$prompt) {
    echo "No prompt provided";
    exit;
}

$payload = [
    "model" => "mistralai/mixtral-8x7b-instruct", //  FIXED MODEL
    "messages" => [
        [
            "role" => "user",
            "content" => $prompt
        ]
    ]
];

$ch = curl_init("https://openrouter.ai/api/v1/chat/completions");

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . OPENROUTER_KEY,
        "Content-Type: application/json",
        "HTTP-Referer: http://localhost",     // REQUIRED BY OPENROUTER
        "X-Title: Unified AI Hub"              
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

/* ✅ SAFELY EXTRACT AI RESPONSE */
if (isset($data['choices'][0]['message']['content'])) {
    echo $data['choices'][0]['message']['content'];
} elseif (isset($data['error']['message'])) {
    echo "OpenRouter Error: " . $data['error']['message'];
} else {
    echo "Unexpected OpenRouter response";
}
