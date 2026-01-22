<?php
//save_chat.php

session_start();
header('Content-Type: application/json');

require_once '../config/db.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$user_id = $_SESSION['user_id'];
$prompt = $_POST['prompt'] ?? ($_POST['message'] ?? '');
$openai_response = $_POST['openai_response'] ?? '';
$openrouter_response = $_POST['openrouter_response'] ?? '';
$cohere_response = $_POST['cohere_response'] ?? '';

if(empty($prompt)) {
    echo json_encode(['success' => false, 'message' => 'Prompt is required']);
    exit();
}

try {
    // Using MySQLi (your existing style)
    $stmt = $conn->prepare(
    "INSERT INTO chat_logs (user_id, message, role, created_at)
     VALUES (?, ?, 'user', NOW())"
    );

    $stmt->bind_param("is", $user_id, $prompt);

    
    if($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Chat saved successfully',
            'chat_id' => $conn->insert_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save chat']);
    }
    
    $stmt->close();
    
} catch(Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}

$conn->close();
?>