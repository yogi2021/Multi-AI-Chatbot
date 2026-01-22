<?php
require __DIR__ . '/../config/env.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Frontend se prompt aata hai
$prompt = $_POST['prompt'] ?? '';

if ($prompt === '') {
    echo "No prompt received";
    exit;
}

// OpenAI Responses API payload (LATEST & CORRECT)
$payload = [
    "model" => "gpt-4.1-mini",
    "input" => [
        [
            "role" => "user",
            "content" => [
                [
                    "type" => "input_text",
                    "text" => $prompt
                ]
            ]
        ]
    ]
];

// CURL request
$ch = curl_init("https://api.openai.com/v1/responses");

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer " . OPENAI_KEY
    ],
    CURLOPT_POSTFIELDS => json_encode($payload)
]);

$response = curl_exec($ch);

if ($response === false) {
    die("CURL ERROR: " . curl_error($ch));
}

curl_close($ch);

// Decode response
$data = json_decode($response, true);

// Output text safely
if (isset($data['output'][0]['content'][0]['text'])) {
    echo $data['output'][0]['content'][0]['text'];
} else {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}
