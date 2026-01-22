<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user info
$username = $_SESSION['username'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-AI Chatbot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <span class="bot-emoji">🤖</span>
            Multi-AI Chatbot
        </div>
        <div class="header-right">
            <button class="header-btn" id="themeToggle" onclick="toggleTheme()">🌙</button>
            <button class="header-btn">👤 <?php echo htmlspecialchars($username); ?></button>
            <button class="header-btn" onclick="location.href='api/auth/logout.php'">🚪 Logout</button>
        </div>
    </div>

    <!-- Main Container -->
    <div class="main-container">

        <!-- Chat Container -->
        <div class="chat-container">
            <!-- Welcome Section -->
            <div class="welcome-section" id="welcomeSection">
                <div class="bot-icon">🤖</div>
                <h1 class="welcome-title">Welcome to Multi-AI Chatbot</h1>
                <p class="welcome-subtitle">Ask a question and get responses from multiple AI models</p>
                <div class="ai-badges">
                    <button class="ai-badge badge-openai">
                        🤖 ChatGpt
                    </button>
                    <button class="ai-badge badge-gemini">
                        ✨ Gemini AI
                    </button>
                    <button class="ai-badge badge-custom">
                        ⚡ Cohere AI
                    </button>
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="chat-messages" id="chatMessages">
                <!-- Messages will be added here dynamically -->
            </div>

            <!-- Input Section -->
            <div class="input-section">
                <div class="input-wrapper">
                    <textarea 
                        id="messageInput" 
                        class="input-box" 
                        placeholder="Type your message here..."
                        rows="1"
                    ></textarea>
                    <button class="send-btn" id="sendBtn" onclick="sendMessage()">
                        ✈️ Send
                    </button>
                </div>
            </div>
        </div>
    </div>

<script>
function loadRecentChats() {
    fetch("api/get_recent_chats.php")
        .then(res => res.json())
        .then(data => {
            const box = document.getElementById("recentChats");
            box.innerHTML = "";

            if (!data || data.length === 0) {
                box.innerHTML = "<p>Chat history will appear here</p>";
                return;
            }

            data.forEach(chat => {
                box.innerHTML += `
                    <div class="recent-chat-item"
                         onclick="openChat(${chat.id})"
                         style="cursor:pointer;">
                        ${chat.message}
                    </div>
                `;
            });
        })
        .catch(err => console.error("Recent chat error:", err));
}

// 🔥 CLICK HANDLER – chat open karega
function openChat(chatId) {
    // Abhi navigation disable rakhenge
    // kyunki proper chat-session system nahi hai
    console.log("Recent chat clicked:", chatId);
}

// global scope ke liye (safe)
window.openChat = openChat;

// page load pe sidebar fill ho
document.addEventListener("DOMContentLoaded", loadRecentChats);
</script>

    <script src="assets/js/script.js"></script>
</body>
</html>