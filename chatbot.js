// js/chatbot.js
document.addEventListener("DOMContentLoaded", function () {
    const chatWindow = document.getElementById("pg-chat-window");
    const toggleBtn = document.getElementById("pg-chat-toggle");
    const closeBtn = document.getElementById("pg-chat-close");
    const messagesBox = document.getElementById("pg-chat-messages");
    const form = document.getElementById("pg-chat-form");
    const input = document.getElementById("pg-chat-input");

    if (!chatWindow || !toggleBtn || !closeBtn || !messagesBox || !form || !input) {
        return;
    }
    const micBtn = document.getElementById("pg-chat-mic");
    let recognizing = false;
    let recognition;

    if (micBtn && ("webkitSpeechRecognition" in window || "SpeechRecognition" in window)) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        recognition.lang = 'auto';
        recognition.interimResults = false;

        recognition.onstart = function () {
            recognizing = true;
            micBtn.disabled = true;
            micBtn.textContent = "…";
        };

        recognition.onend = function () {
            recognizing = false;
            micBtn.disabled = false;
            micBtn.textContent = "🎤";
        };

        recognition.onresult = function (event) {
            const transcript = event.results[0][0].transcript;
            input.value = transcript;
        };

        micBtn.addEventListener("click", function () {
            if (!recognizing) {
                recognition.start();
            }
        });
    } else if (micBtn) {
        micBtn.style.display = "none";
    }


    function appendMessage(text, sender) {
        const msgDiv = document.createElement("div");
        msgDiv.classList.add("pg-chat-message", sender);
        msgDiv.textContent = text;
        messagesBox.appendChild(msgDiv);
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }

    // Initial welcome message
    appendMessage("Hi! I am your PG Assistant 🤖. Ask me anything about PGs, rent, facilities, safety or booking.", "bot");

    toggleBtn.addEventListener("click", function () {
        const isVisible = chatWindow.style.display === "flex";
        chatWindow.style.display = isVisible ? "none" : "flex";
    });

    closeBtn.addEventListener("click", function () {
        chatWindow.style.display = "none";
    });

    form.addEventListener("submit", function (e) {
        e.preventDefault();
        const text = input.value.trim();
        if (text === "") return;

        appendMessage(text, "user");
        input.value = "";

        fetch("./chatbot.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
            },
            body: "message=" + encodeURIComponent(text)
        })
            .then(response => response.json())
            .then(data => {
                if (data && data.reply) {
                    appendMessage(data.reply, "bot");
                } else {
                    appendMessage("Sorry, I couldn't understand that right now.", "bot");
                }
            })
            .catch(() => {
                appendMessage("Network error. Please try again.", "bot");
            });
    });
});
