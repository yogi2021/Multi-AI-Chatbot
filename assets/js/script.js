const messageInput = document.getElementById('messageInput');
const sendBtn = document.getElementById('sendBtn');
const chatMessages = document.getElementById('chatMessages');
const welcomeSection = document.getElementById('welcomeSection');
const themeToggle = document.getElementById('themeToggle');

// Load saved theme
const savedTheme = localStorage.getItem('theme') || 'light';
if (savedTheme === 'dark') {
    document.body.classList.add('dark-mode');
    themeToggle.textContent = '☀️';
}

// Toggle theme function
function toggleTheme() {
    document.body.classList.toggle('dark-mode');
    const isDark = document.body.classList.contains('dark-mode');
    themeToggle.textContent = isDark ? '☀️' : '🌙';
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
}

// Auto-resize textarea
messageInput.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 150) + 'px';
});

// Send on Enter (but allow Shift+Enter for new line)
messageInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

function sendMessage() {
    const prompt = messageInput.value.trim();
    if (!prompt) {
        alert("Please enter a message");
        return;
    }

    // Hide welcome, show chat
    welcomeSection.style.display = 'none';
    chatMessages.classList.add('active');

    // Add user message to UI
    addUserMessage(prompt);

    // Clear input
    messageInput.value = '';
    messageInput.style.height = 'auto';

    // Disable send button
    sendBtn.disabled = true;

    // Create loading responses
    const messageId = Date.now();
    addAIResponses(messageId);

    // Call APIs (OpenAI / Gemini / Cohere)
    callAPIs(prompt, messageId);
}

function addUserMessage(text) {
    const messageGroup = document.createElement('div');
    messageGroup.className = 'message-group';
    messageGroup.innerHTML = `
        <div class="user-message">
            <div class="user-message-label">You</div>
            <div class="user-message-text">${escapeHtml(text)}</div>
        </div>
    `;
    chatMessages.appendChild(messageGroup);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function addAIResponses(messageId) {
    const responsesDiv = document.createElement('div');
    responsesDiv.className = 'ai-responses';
    responsesDiv.id = 'responses-' + messageId;
    responsesDiv.innerHTML = `
        <div class="ai-response-card">
            <div class="ai-response-header header-openai">
                🤖 OpenAI
            </div>
            <div class="ai-response-content loading-text" id="openai-${messageId}">
                Loading response...
            </div>
        </div>
        <div class="ai-response-card">
            <div class="ai-response-header header-gemini">
                ✨ Gemini
            </div>
            <div class="ai-response-content loading-text" id="openrouter-${messageId}">
                Loading response...
            </div>
        </div>
        <div class="ai-response-card">
            <div class="ai-response-header header-cohere">
                ⚡ Cohere
            </div>
            <div class="ai-response-content loading-text" id="cohere-${messageId}">
                Loading response...
            </div>
        </div>
    `;
    
    const lastMessageGroup = chatMessages.lastElementChild;
    lastMessageGroup.appendChild(responsesDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

let completedCount = 0;

function callAPIs(prompt, messageId) {
    completedCount = 0;

    callAPI('api/openai.php', 'openai', prompt, messageId);
    callAPI('api/openrouter.php', 'openrouter', prompt, messageId);
    callAPI('api/cohere.php', 'cohere', prompt, messageId);
}

function callAPI(url, provider, prompt, messageId) {
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'prompt=' + encodeURIComponent(prompt)
    })
    .then(res => res.text())
    .then(data => {
        const element = document.getElementById(`${provider}-${messageId}`);
        element.className = 'ai-response-content';
        element.textContent = data;
        chatMessages.scrollTop = chatMessages.scrollHeight;
    })
    .catch(error => {
        const element = document.getElementById(`${provider}-${messageId}`);
        element.className = 'ai-response-content error-text';
        element.textContent = 'API Error ❌';
        console.error('Error:', error);
    })
    .finally(() => {
        completedCount++;
        if (completedCount === 3) {
            sendBtn.disabled = false;
        }
    });
}

// Save chat to database
function saveToDatabase(prompt, provider, response, messageId) {
    // Only save once when all responses are received
    if (completedCount === 3) {
        const formData = new FormData();
        formData.append('prompt', prompt);
        formData.append('openai_response', document.getElementById(`openai-${messageId}`).textContent);
        formData.append('openrouter_response', document.getElementById(`openrouter-${messageId}`).textContent);
        formData.append('cohere_response', document.getElementById(`cohere-${messageId}`).textContent);
        
        fetch('api/save_chat.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(!data.success) {
                console.error('Failed to save chat:', data.message);
            }
        })
        .catch(error => {
            console.error('Error saving chat:', error);
        });
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}


