/**
 * AI Chatbot Widget JavaScript
 * Floating chatbot for patient interaction
 */

class AIChatbotWidget {
    constructor() {
        this.isOpen = false;
        this.messages = [];
        this.init();
    }
    
    init() {
        // Create widget HTML
        this.createWidget();
        this.attachEventListeners();
        this.loadWelcomeMessage();
    }
    
    createWidget() {
        const widgetHTML = `
            <div class="ai-chatbot-widget">
                <button class="chatbot-toggle-btn" id="chatbotToggle">
                    🤖
                </button>
                
                <div class="chatbot-window" id="chatbotWindow">
                    <div class="chatbot-window-header">
                        <h4>🤖 Dr. Cares AI</h4>
                        <button class="chatbot-close-btn" id="chatbotClose">×</button>
                    </div>
                    
                    <div class="chatbot-quick-replies">
                        <button class="chatbot-quick-reply" data-message="Book appointment">📅 Book Appointment</button>
                        <button class="chatbot-quick-reply" data-message="Check symptoms">🩺 Check Symptoms</button>
                        <button class="chatbot-quick-reply" data-message="Health tips">💪 Health Tips</button>
                    </div>
                    
                    <div class="chatbot-messages" id="chatbotMessages">
                        <!-- Messages will be added here -->
                    </div>
                    
                    <div class="chatbot-input-area">
                        <form class="chatbot-input-form" id="chatbotForm">
                            <input type="text" id="chatbotInput" placeholder="Type your message..." autocomplete="off">
                            <button type="submit" class="chatbot-send-btn">Send</button>
                        </form>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', widgetHTML);
    }
    
    attachEventListeners() {
        const toggleBtn = document.getElementById('chatbotToggle');
        const closeBtn = document.getElementById('chatbotClose');
        const chatForm = document.getElementById('chatbotForm');
        const quickReplies = document.querySelectorAll('.chatbot-quick-reply');
        
        toggleBtn.addEventListener('click', () => this.toggleWidget());
        closeBtn.addEventListener('click', () => this.closeWidget());
        chatForm.addEventListener('submit', (e) => this.handleSubmit(e));
        
        quickReplies.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const message = e.target.getAttribute('data-message');
                this.sendMessage(message);
            });
        });
    }
    
    toggleWidget() {
        this.isOpen = !this.isOpen;
        const window = document.getElementById('chatbotWindow');
        
        if (this.isOpen) {
            window.classList.add('active');
            document.getElementById('chatbotInput').focus();
        } else {
            window.classList.remove('active');
        }
    }
    
    closeWidget() {
        this.isOpen = false;
        document.getElementById('chatbotWindow').classList.remove('active');
    }
    
    loadWelcomeMessage() {
        this.addBotMessage("Hello! I'm Dr. Cares AI, your virtual health assistant. How can I help you today?");
    }
    
    handleSubmit(e) {
        e.preventDefault();
        const input = document.getElementById('chatbotInput');
        const message = input.value.trim();
        
        if (message) {
            this.sendMessage(message);
            input.value = '';
        }
    }
    
    sendMessage(message) {
        // Add user message
        this.addUserMessage(message);
        
        // Show typing indicator
        this.showTyping();
        
        // Simulate AI response (in production, this would call the backend API)
        setTimeout(() => {
            this.hideTyping();
            const response = this.getAIResponse(message);
            this.addBotMessage(response);
        }, 1000);
    }
    
    getAIResponse(message) {
        const messageLower = message.toLowerCase();
        
        if (messageLower.includes('appointment') || messageLower.includes('book')) {
            return "I can help you book an appointment! You can visit our <a href='appointment.php'>Appointment Booking</a> page or tell me your preferred date and department.";
        } else if (messageLower.includes('symptom') || messageLower.includes('sick') || messageLower.includes('pain')) {
            return "I can help analyze your symptoms. Please describe what you're experiencing in detail, or visit our <a href='ai_dr_cares.php'>Dr. Cares AI</a> page for a comprehensive analysis.";
        } else if (messageLower.includes('medication') || messageLower.includes('medicine')) {
            return "For medication information and AI-powered recommendations, please visit our <a href='ai_dr_cares.php'>Dr. Cares AI</a> module.";
        } else if (messageLower.includes('health tips') || messageLower.includes('advice')) {
            return "Here are some quick health tips: Stay hydrated, exercise regularly, eat a balanced diet, and get 7-9 hours of sleep. For personalized advice, check out our <a href='ai_health_trends.php'>Health Trends</a> page!";
        } else if (messageLower.includes('hello') || messageLower.includes('hi')) {
            return "Hello! How can I assist you with your health needs today?";
        } else {
            return "I'm here to help! You can ask me about appointments, symptoms, medications, or general health advice. What would you like to know?";
        }
    }
    
    addUserMessage(message) {
        const messagesContainer = document.getElementById('chatbotMessages');
        const messageHTML = `
            <div class="chatbot-message user">
                <div class="chatbot-message-content">${this.escapeHtml(message)}</div>
            </div>
        `;
        messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
        this.scrollToBottom();
    }
    
    addBotMessage(message) {
        const messagesContainer = document.getElementById('chatbotMessages');
        const messageHTML = `
            <div class="chatbot-message bot">
                <div class="chatbot-message-content">${message}</div>
            </div>
        `;
        messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
        this.scrollToBottom();
    }
    
    showTyping() {
        const messagesContainer = document.getElementById('chatbotMessages');
        const typingHTML = `
            <div class="chatbot-message bot" id="typingIndicator">
                <div class="chatbot-typing active">
                    <span></span><span></span><span></span>
                </div>
            </div>
        `;
        messagesContainer.insertAdjacentHTML('beforeend', typingHTML);
        this.scrollToBottom();
    }
    
    hideTyping() {
        const typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }
    
    scrollToBottom() {
        const messagesContainer = document.getElementById('chatbotMessages');
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize chatbot when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Note: This widget can be used by all users for general queries
    // For personalized features, users should be logged in
    // The backend (ai_chatbot.php) enforces login requirements for full access
    
    // Initialize chatbot widget for all users
    new AIChatbotWidget();
});
