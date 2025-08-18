<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rev Garage AI Assistant</title>
    <style>
        /* Chatbot Toggle Button */
        .chatbot-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #303336ff, #005c99ff);
            border-radius: 50%;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(30, 136, 229, 0.4);
            z-index: 1001;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .chatbot-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(36, 52, 66, 0.6);
        }

        .chatbot-toggle svg {
            width: 24px;
            height: 24px;
            fill: white;
        }

        /* Chatbot Container */
        .chatbot-container {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 350px;
            max-width: calc(100vw - 40px);
            background: #000000ff;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            display: none;
            flex-direction: column;
            z-index: 1000;
            font-family: 'Arial', sans-serif;
        }

        .chatbot-container.active {
            display: flex;
        }

        /* Chat Header */
        .chat-header {
            background: linear-gradient(135deg, #4a5763ff, #050505ff);
            color: #fff;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            position: relative;
        }

        .chat-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        }

        /* Chat Body */
        .chat-body {
            height: 300px;
            padding: 15px;
            overflow-y: auto;
            font-size: 14px;
            background: linear-gradient(135deg, #383e44ff, #000000ff);
        }

        .chat-body::-webkit-scrollbar {
            width: 6px;
        }

        .chat-body::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .chat-body::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        /* Chat Messages */
        .chat-message {
            margin-bottom: 15px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .user-message {
            text-align: right;
        }

        .user-message .message-bubble {
            background: linear-gradient(135deg, #61829eff, #aecaedff);
            color: white;
            padding: 10px 15px;
            border-radius: 18px 18px 5px 18px;
            display: inline-block;
            max-width: 80%;
            word-wrap: break-word;
        }

        .bot-message {
            text-align: left;
        }

        .bot-message .message-bubble {
            background: white;
            color: #333;
            padding: 10px 15px;
            border-radius: 18px 18px 18px 5px;
            display: inline-block;
            max-width: 80%;
            word-wrap: break-word;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Typing Indicator */
        .typing-indicator {
            display: none;
            text-align: left;
            margin-bottom: 15px;
        }

        .typing-indicator .message-bubble {
            background: white;
            padding: 15px;
            border-radius: 18px 18px 18px 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .typing-dots {
            display: inline-block;
        }

        .typing-dots span {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #2f475cff;
            margin: 0 2px;
            animation: typing 1.4s infinite ease-in-out;
        }

        .typing-dots span:nth-child(1) {
            animation-delay: -0.32s;
        }

        .typing-dots span:nth-child(2) {
            animation-delay: -0.16s;
        }

        @keyframes typing {

            0%,
            80%,
            100% {
                transform: scale(0.8);
                opacity: 0.5;
            }

            40% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Chat Input */
        .chat-input {
            display: flex;
            border-top: 1px solid #e0e0e0;
            background: white;
        }

        .chat-input input {
            flex: 1;
            padding: 12px 15px;
            border: none;
            outline: none;
            font-size: 14px;
            background: transparent;
        }

        .chat-input input::placeholder {
            color: #999;
        }

        .chat-input button {
            background: linear-gradient(135deg, #21394eff, #1e2123ff);
            color: #fff;
            border: none;
            padding: 12px 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 50px;
        }

        .chat-input button:hover {
            background: linear-gradient(135deg, #363b41ff, #46556aff);
        }

        .chat-input button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* Quick Actions */
        .quick-actions {
            padding: 10px 15px;
            background: white;
            border-top: 1px solid #e0e0e0;
        }

        .quick-action-btn {
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 15px;
            padding: 6px 12px;
            margin: 2px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .quick-action-btn:hover {
            background: #12304aff;
            color: white;
            border-color: #252729ff;
        }

        /* Mobile Responsiveness */
        @media (max-width: 480px) {
            .chatbot-container {
                width: calc(100vw - 20px);
                right: 10px;
                bottom: 80px;
            }

            .chatbot-toggle {
                right: 10px;
            }
        }
    </style>
</head>

<body>
    <!-- Chatbot Toggle Button -->
    <button class="chatbot-toggle" onclick="toggleChatbot()" id="chatbot-toggle">
        <svg viewBox="0 0 24 24">
            <path d="M20,2H4A2,2 0 0,0 2,4V22L6,18H20A2,2 0 0,0 22,16V4A2,2 0 0,0 20,2M6,9V7H18V9H6M14,11V13H6V11H14M16,15V17H6V15H16Z" />
        </svg>
    </button>

    <!-- Chatbot Container -->
    <div class="chatbot-container" id="chatbot-container">
        <div class="chat-header">
            🏎️ Rev Garage Assistant
        </div>

        <div class="chat-body" id="chat-body">
            <div class="chat-message bot-message">
                <div class="message-bubble">
                    Welcome to Rev Garage! I'm your McLaren specialist. How can I help you today? 🚗
                </div>
            </div>
        </div>

        <div class="quick-actions">
            <button class="quick-action-btn" onclick="sendQuickMessage('Tell me about McLaren models')">McLaren Models</button>
            <button class="quick-action-btn" onclick="sendQuickMessage('What are your prices?')">Pricing</button>
            <button class="quick-action-btn" onclick="sendQuickMessage('Book a test drive')">Test Drive</button>
            <button class="quick-action-btn" onclick="sendQuickMessage('Contact information')">Contact</button>
        </div>

        <div class="typing-indicator" id="typing-indicator">
            <div class="message-bubble">
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>

        <div class="chat-input">
            <input type="text" id="user-input" placeholder="Ask about McLaren cars..." onkeypress="handleKeyPress(event)">
            <button onclick="sendMessage()" id="send-btn">Send</button>
        </div>
    </div>

    <script>
        const botResponses = {
            greetings: [
                "Hello! Welcome to Rev Garage, your premier McLaren destination! How can I assist you today?",
                "Hi there! I'm here to help you with all things McLaren. What would you like to know?",
                "Welcome to Rev Garage! Ready to explore the world of McLaren supercars?"
            ],
            models: {
                "sport series": "Our Sport Series includes the McLaren 570S, 540C, and 600LT. These are perfect entry points into the McLaren world, offering incredible performance and daily usability.",
                "super series": "The Super Series features the 720S, 765LT, and 675LT. These are our high-performance supercars with track-focused engineering and breathtaking speed.",
                "ultimate series": "Our Ultimate Series represents the pinnacle of McLaren engineering - the P1, Senna, and Speedtail. These are limited-production hypercars for the most discerning enthusiasts.",
                "570s": "The McLaren 570S is a fantastic entry into McLaren ownership. With 562hp from its twin-turbo V8, it offers supercar performance with everyday usability. Perfect for both road and track!",
                "720s": "The 720S is our flagship Super Series model with 710hp. It features our revolutionary MonoCell II carbon fiber chassis and active aerodynamics. Truly a masterpiece of engineering!",
                "p1": "The P1 is one of the 'Holy Trinity' of hypercars. With 916hp from its hybrid powertrain, only 375 were ever made. It's the ultimate expression of McLaren's Formula 1 technology.",
                "senna": "Named after Ayrton Senna, this track-focused hypercar produces 789hp and weighs just 1,198kg. It's designed for the ultimate track experience while remaining road legal.",
                "speedtail": "The Speedtail is our fastest car ever, capable of 250mph. It's a three-seat Hyper-GT that combines luxury with extreme performance. Only 106 were made."
            },
            services: [
                "We offer comprehensive McLaren services including sales, financing, maintenance, and genuine parts. Our certified technicians ensure your McLaren performs at its best.",
                "Our services include new and pre-owned McLaren sales, factory-trained maintenance, genuine McLaren parts, and exclusive customer experiences.",
                "We provide full McLaren ownership support: sales consultation, financing options, scheduled maintenance, warranty service, and track day experiences."
            ],
            pricing: [
                "McLaren pricing varies by model and specification. Our Sport Series starts around $200k, Super Series from $300k, and Ultimate Series are typically $1M+. Would you like specific pricing for a particular model?",
                "Pricing depends on the model and options you choose. I'd be happy to discuss specific models and arrange a consultation with our sales team for detailed pricing.",
                "Each McLaren is unique with various customization options. Let me connect you with our sales specialists who can provide detailed pricing based on your preferences."
            ],
            testDrive: [
                "I'd love to arrange a test drive for you! Which McLaren model interests you most? We can schedule a personalized driving experience at your convenience.",
                "Test drives are available for qualified customers. Please provide your contact information and preferred model, and our team will arrange an unforgettable driving experience.",
                "Nothing beats experiencing a McLaren firsthand! Let me help you schedule a test drive. Which model would you like to experience?"
            ],
            contact: [
                "You can reach Rev Garage at our showroom or call us directly. We're here Monday-Saturday to assist with all your McLaren needs. Would you like our specific contact details?",
                "Our team is ready to help! Visit our showroom, call us, or schedule an appointment online. We're passionate about helping you find your perfect McLaren.",
                "Contact us anytime! Our McLaren specialists are available to answer questions, arrange viewings, or schedule test drives. How would you prefer to connect?"
            ],
            default: [
                "That's a great question about McLaren! Let me help you with that. Could you be more specific about what you'd like to know?",
                "I'm here to help with all things McLaren and Rev Garage. Could you tell me more about what you're looking for?",
                "I'd be happy to assist you! Whether it's about our McLaren models, services, or anything else, just let me know how I can help.",
                "Thanks for your interest in Rev Garage! I'm here to help with McLaren information, pricing, test drives, or any other questions you might have."
            ]
        };

        let chatbotOpen = false;

        function toggleChatbot() {
            const container = document.getElementById('chatbot-container');
            const toggle = document.getElementById('chatbot-toggle');

            chatbotOpen = !chatbotOpen;

            if (chatbotOpen) {
                container.classList.add('active');
                toggle.innerHTML = '<svg viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg>';
            } else {
                container.classList.remove('active');
                toggle.innerHTML = '<svg viewBox="0 0 24 24"><path d="M20,2H4A2,2 0 0,0 2,4V22L6,18H20A2,2 0 0,0 22,16V4A2,2 0 0,0 20,2M6,9V7H18V9H6M14,11V13H6V11H14M16,15V17H6V15H16Z" /></svg>';
            }
        }

        function sendMessage() {
            const userInput = document.getElementById('user-input');
            const message = userInput.value.trim();
            if (message === '') return;

            addMessage(message, 'user-message');
            userInput.value = '';

            // Disable send button temporarily
            const sendBtn = document.getElementById('send-btn');
            sendBtn.disabled = true;


            showTypingIndicator();

            setTimeout(() => {
                hideTypingIndicator();
                const reply = generateResponse(message);
                addMessage(reply, 'bot-message');
                sendBtn.disabled = false;
            }, 1000 + Math.random() * 1000);
        }

        function sendQuickMessage(message) {
            addMessage(message, 'user-message');

            showTypingIndicator();
            setTimeout(() => {
                hideTypingIndicator();
                const reply = generateResponse(message);
                addMessage(reply, 'bot-message');
            }, 800);
        }

        function generateResponse(message) {
            const lowerMessage = message.toLowerCase();

            // Greeting detection
            if (lowerMessage.includes('hello') || lowerMessage.includes('hi') || lowerMessage.includes('hey')) {
                return getRandomResponse(botResponses.greetings);
            }

            // Model-specific responses
            for (const [key, response] of Object.entries(botResponses.models)) {
                if (lowerMessage.includes(key)) {
                    return response;
                }
            }

            // Service-related
            if (lowerMessage.includes('service') || lowerMessage.includes('maintenance') || lowerMessage.includes('repair')) {
                return getRandomResponse(botResponses.services);
            }

            // Pricing
            if (lowerMessage.includes('price') || lowerMessage.includes('cost') || lowerMessage.includes('expensive')) {
                return getRandomResponse(botResponses.pricing);
            }

            // Test drive
            if (lowerMessage.includes('test drive') || lowerMessage.includes('drive') || lowerMessage.includes('try')) {
                return getRandomResponse(botResponses.testDrive);
            }

            // Contact
            if (lowerMessage.includes('contact') || lowerMessage.includes('phone') || lowerMessage.includes('address')) {
                return getRandomResponse(botResponses.contact);
            }

            // Default response
            return getRandomResponse(botResponses.default);
        }

        function getRandomResponse(responses) {
            return responses[Math.floor(Math.random() * responses.length)];
        }

        function addMessage(text, className) {
            const chatBody = document.getElementById('chat-body');
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('chat-message', className);

            const bubbleDiv = document.createElement('div');
            bubbleDiv.classList.add('message-bubble');
            bubbleDiv.innerText = text;

            messageDiv.appendChild(bubbleDiv);
            chatBody.appendChild(messageDiv);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function showTypingIndicator() {
            const indicator = document.getElementById('typing-indicator');
            indicator.style.display = 'block';
            const chatBody = document.getElementById('chat-body');
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function hideTypingIndicator() {
            const indicator = document.getElementById('typing-indicator');
            indicator.style.display = 'none';
        }

        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        }

        // setTimeout(() => {
        //     if (!chatbotOpen) {

        //     }
        // }, 5000);
    </script>
</body>

</html>