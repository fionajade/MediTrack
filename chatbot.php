<style>
/* --- CHATBOT STYLES --- */
    :root {
        --chat-primary: #002147;
        --chat-bg: #ffffff;
        --chat-width: 380px;
    }

    /* Floating Button */
    .chatbot-toggler {
        position: fixed;
        bottom: 30px;
        right: 30px;
        outline: none;
        border: none;
        height: 60px;
        width: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: var(--chat-primary);
        color: white;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        z-index: 9999;
    }

    .chatbot-toggler:hover {
        transform: scale(1.1);
    }

    /* Chat Sidebar */
    .chatbot-sidebar {
        position: fixed;
        right: -450px; /* Hidden by default */
        top: 0;
        height: 100%;
        width: var(--chat-width);
        background: var(--chat-bg);
        box-shadow: -5px 0 20px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        transition: right 0.4s ease;
        z-index: 9998;
        font-family: 'SF Pro Display', sans-serif;
    }

    /* Class to slide it in */
    .show-chatbot .chatbot-sidebar {
        right: 0;
    }

    /* Header */
    .chat-header {
        background: var(--chat-primary);
        color: white;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .chat-header h2 {
        font-size: 1.2rem;
        margin: 0;
        font-weight: 600;
    }

    .close-btn {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        opacity: 0.8;
    }
    
    .close-btn:hover { opacity: 1; }

    /* Chat Body */
    .chat-box {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background: #f9f9f9;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .chat-message {
        display: flex;
        flex-direction: column;
        max-width: 80%;
    }

    .chat-message.bot {
        align-self: flex-start;
    }

    .chat-message.user {
        align-self: flex-end;
        align-items: flex-end;
    }

    .message-content {
        padding: 10px 15px;
        border-radius: 15px;
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .bot .message-content {
        background: #e9ecef;
        color: #333;
        border-bottom-left-radius: 2px;
    }

    .user .message-content {
        background: var(--chat-primary);
        color: white;
        border-bottom-right-radius: 2px;
    }

    /* Chat Input Area */
    .chat-input {
        padding: 15px;
        border-top: 1px solid #ddd;
        display: flex;
        gap: 10px;
        background: white;
    }

    .chat-input textarea {
        width: 100%;
        border: 1px solid #ccc;
        border-radius: 20px;
        padding: 10px 15px;
        resize: none;
        height: 45px;
        font-family: inherit;
        outline: none;
        font-size: 0.9rem;
    }

    .send-btn {
        background: var(--chat-primary);
        color: white;
        border: none;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Mobile Responsiveness */
    @media (max-width: 480px) {
        .chatbot-sidebar { width: 100%; right: -100%; }
        .show-chatbot .chatbot-sidebar { right: 0; }
    }
</style>
    
    <!-- 1. Floating Trigger Button -->
    <button class="chatbot-toggler" onclick="toggleChat()">
        <!-- SVG Icon (Message Bubble) -->
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
    </button>

    <!-- 2. The Sidebar -->
    <div class="chatbot-sidebar">
        <div class="chat-header">
            <div style="display:flex; align-items:center; gap:10px;">
                <!-- Logo inside chat -->
                <div style="width:30px; height:30px; background:white; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                    <span style="color:#002147; font-weight:bold;">M</span>
                </div>
                <h2>MediTrack AI</h2>
            </div>
            <button class="close-btn" onclick="toggleChat()">&times;</button>
        </div>

        <div class="chat-box">
            <!-- Default Welcome Message -->
            <div class="chat-message bot">
                <div class="message-content">
                    Hello! I'm your MediTrack Assistant. How can I help you find medicines today?
                </div>
            </div>
        </div>

        <div class="chat-input">
            <textarea placeholder="Type a message..." required></textarea>
            <button class="send-btn">
                <!-- SVG Send Icon -->
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
            </button>
        </div>
    </div>

    <!-- 3. Javascript for functionality -->
    <script>
        const body = document.body;
        
        function toggleChat() {
            body.classList.toggle("show-chatbot");
        }

        // Optional: Simple auto-reply logic for demo
        const sendBtn = document.querySelector(".send-btn");
        const chatInput = document.querySelector(".chat-input textarea");
        const chatBox = document.querySelector(".chat-box");

        const handleChat = () => {
            const userMessage = chatInput.value.trim();
            if (!userMessage) return;

            // 1. Append User Message
            chatBox.innerHTML += `
                <div class="chat-message user">
                    <div class="message-content">${userMessage}</div>
                </div>`;
            
            chatInput.value = "";
            chatBox.scrollTop = chatBox.scrollHeight;

            // 2. Simulate Bot Typing/Reply
            setTimeout(() => {
                chatBox.innerHTML += `
                    <div class="chat-message bot">
                        <div class="message-content">Thank you for your message! This is a demo, but soon I'll be able to help you find stock for "${userMessage}".</div>
                    </div>`;
                chatBox.scrollTop = chatBox.scrollHeight;
            }, 600);
        }

        sendBtn.addEventListener("click", handleChat);
        chatInput.addEventListener("keydown", (e) => {
            if(e.key === "Enter" && !e.shiftKey) {
                e.preventDefault();
                handleChat();
            }
        });
    </script>
