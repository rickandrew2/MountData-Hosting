document.addEventListener('DOMContentLoaded', function() {
    const chatButton = document.getElementById('chatButton');
    const chatContainer = document.getElementById('chatContainer');
    const closeChat = document.getElementById('closeChat');
    const userInput = document.getElementById('userInput');
    const sendMessage = document.getElementById('sendMessage');
    const chatMessages = document.getElementById('chatMessages');

    // Toggle chat window
    chatButton.addEventListener('click', () => {
        chatContainer.style.display = chatContainer.style.display === 'none' ? 'block' : 'none';
    });

    closeChat.addEventListener('click', () => {
        chatContainer.style.display = 'none';
    });

    // Send message function
    function sendUserMessage() {
        const message = userInput.value.trim();
        if (message) {
            // Add user message to chat
            appendMessage('user', message);
            userInput.value = '';

            // Get bot response
            getBotResponse(message);
        }
    }

    // Send message on button click or Enter key
    sendMessage.addEventListener('click', sendUserMessage);
    userInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendUserMessage();
        }
    });

    // Append message to chat
    function appendMessage(sender, message) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message', sender);
        
        // Check if the message is from the bot and contains HTML
        if (sender === 'bot') {
            messageDiv.innerHTML = message; // Use innerHTML for bot messages
        } else {
            messageDiv.textContent = message; // Use textContent for user messages
        }
        
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Add FAQ button functionality
    const faqButtons = document.querySelectorAll('.faq-button');
    faqButtons.forEach(button => {
        button.addEventListener('click', () => {
            const question = button.getAttribute('data-question').toLowerCase();
            appendMessage('user', button.getAttribute('data-question'));
            getBotResponse(question);
            
            // Hide FAQ suggestions after clicking
            const faqSuggestions = document.querySelector('.faq-suggestions');
            if (faqSuggestions) {
                faqSuggestions.style.display = 'none';
            }
        });
    });

    // Add function to show typing indicator
    function showTypingIndicator() {
        const typingIndicator = document.querySelector('.typing-indicator');
        if (typingIndicator) {
            typingIndicator.style.display = 'flex';
        }
    }

    // Add function to hide typing indicator
    function hideTypingIndicator() {
        const typingIndicator = document.querySelector('.typing-indicator');
        if (typingIndicator) {
            typingIndicator.style.display = 'none';
        }
    }

    // Modify getBotResponse to include typing indicator
    function getBotResponse(userMessage) {
        showTypingIndicator();
        
        fetch('/systemfeatures/chatbot/chatbot_response.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message: userMessage })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            hideTypingIndicator();
            appendMessage('bot', data.response);
            
            // Show FAQ suggestions again for certain responses
            if (data.showFAQ) {
                const faqSuggestions = document.querySelector('.faq-suggestions');
                if (faqSuggestions) {
                    faqSuggestions.style.display = 'flex';
                }
            }
        })
        .catch(error => {
            hideTypingIndicator();
            console.error('Error:', error);
            appendMessage('bot', 'Sorry, I encountered an error. Please try again.');
        });
    }
}); 