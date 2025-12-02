<?php
// includes/chatbot_widget.php
?>
<style>
    .pg-chatbot-wrapper {
        position: fixed;
        right: 20px;
        bottom: 20px;
        z-index: 9999;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .pg-chat-toggle-btn {
        border: none;
        border-radius: 999px;
        padding: 10px 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        background: #0d6efd;
        color: #fff;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pg-chat-toggle-btn span {
        font-size: 14px;
    }

    .pg-chat-toggle-btn .pg-chat-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #4ade80;
    }

    .pg-chat-window {
        width: 320px;
        max-height: 420px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.25);
        display: none;
        flex-direction: column;
        overflow: hidden;
        margin-bottom: 10px;
    }

    .pg-chat-header {
        background: #0d6efd;
        color: #fff;
        padding: 12px 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
    }

    .pg-chat-header-title {
        font-weight: 600;
    }

    .pg-chat-header button {
        border: none;
        background: transparent;
        color: #fff;
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
    }

    .pg-chat-body {
        padding: 10px;
        background: #f8fafc;
        flex: 1;
        overflow-y: auto;
        font-size: 13px;
        display: flex;
        flex-direction: column;
    }

    .pg-chat-message {
        margin-bottom: 8px;
        max-width: 85%;
        padding: 6px 10px;
        border-radius: 12px;
        line-height: 1.4;
    }

    .pg-chat-message.bot {
        background: #e2edff;
        align-self: flex-start;
    }

    .pg-chat-message.user {
        background: #0d6efd;
        color: #fff;
        align-self: flex-end;
    }

    .pg-chat-footer {
        padding: 8px;
        border-top: 1px solid #e5e7eb;
        background: #fff;
    }

    .pg-chat-footer form {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .pg-chat-input {
        flex: 1;
        border-radius: 999px;
        border: 1px solid #d1d5db;
        padding: 6px 10px;
        font-size: 13px;
        outline: none;
    }

    .pg-chat-send-btn {
        border-radius: 999px;
        border: none;
        padding: 6px 12px;
        background: #0d6efd;
        color: #fff;
        font-size: 13px;
        cursor: pointer;
        font-weight: 500;
    }

    
    .pg-chat-mic-btn {
        border-radius: 999px;
        border: none;
        padding: 6px 8px;
        background: #e5e7eb;
        cursor: pointer;
        font-size: 14px;
    }
    .pg-chat-mic-btn span {
        pointer-events: none;
    }

    @media (max-width: 576px) {
        .pg-chat-window {
            width: 100%;
            right: 0;
            left: 0;
            max-height: 60vh;
        }
    }
</style>

<div class="pg-chatbot-wrapper">
    <div class="pg-chat-window" id="pg-chat-window">
        <div class="pg-chat-window-inner">
            <div class="pg-chat-header">
                <div>
                    <div class="pg-chat-header-title">PG Assistant</div>
                    <small style="font-size:11px; opacity:0.9;">Ask anything about PGs</small>
                </div>
                <button type="button" id="pg-chat-close">&times;</button>
            </div>
            <div class="pg-chat-body" id="pg-chat-messages">
                <!-- Messages will be appended here -->
            </div>
            <div class="pg-chat-footer">
                <form id="pg-chat-form">
                    <input type="text" id="pg-chat-input" class="pg-chat-input"
                           placeholder="Ask about rent, city, facilities..." required>
                    <button type="button" class="pg-chat-send-btn" id="pg-chat-mic" title="Speak"><span>🎤</span></button>
                    <button type="submit" class="pg-chat-send-btn">Send</button>
                </form>
            </div>
        </div>
    </div>

    <button class="pg-chat-toggle-btn" id="pg-chat-toggle">
        <span class="pg-chat-dot"></span>
        <span>Chat with PG Assistant</span>
    </button>
</div>

<script src="js/chatbot.js"></script>
