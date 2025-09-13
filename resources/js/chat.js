import { generateAIResponse } from './aiChat';

// Chat component
window.chat = function() {
    return {
        showServiceModal: false,
        selectedService: null,
        showChatbot: false,
        isChatOpen: false,
        chatMessages: [],
        newMessage: '',
        isTyping: false,
        services: {
            computer: {
                title: 'Computer Services',
                description: 'Comprehensive computer repair and maintenance services for desktops and workstations.',
                features: [
                    'Hardware Diagnostics',
                    'Virus Removal',
                    'System Optimization',
                    'Software Installation',
                    'Data Backup & Recovery',
                    'Networking Setup',
                    'Reformat w/ MS Office',
                    'Deep Cleaning',
                ],
                pricing: 'Starting from 300',
                turnaround: '1-2 business days',
                warranty: '14 days service warranty',
                image: '/img/computer-repair.jpg'
            },
            mobile: {
                title: 'Mobile Services',
                description: 'Expert repair services for all major smartphone brands including Apple, Samsung, and more.',
                features: [
                    'Screen Replacement',
                    'Battery Replacement',
                    'Water Damage Repair (soon)',
                    'Camera Replacement',
                    'Charging Board Replacement',
                    'Bypass Services',
                    
                ],
                pricing: 'Starting from ₱300',
                turnaround: 'Same day service available',
                warranty: '14 days service warranty',
                image: '/img/smartphone-repair.jpg'
            },
            laptop: {
                title: 'Laptop Services',
                description: 'Comprehensive laptop repair services for all brands and models.',
                features: [
                    'Screen Replacement',
                    'Keyboard Replacement',
                    'HDD/SDD/Ram Upgrade/Repair',
                    'Reformat w/ MS Office',
                    'Virus Removal',
                    'Software Issues',
                    'Data Recovery',
                    'Network Setup',
                ],
                pricing: 'Starting from ₱280',
                turnaround: '1-3 business days',
                warranty: '14 days service warranty',
                image: '/img/laptop-repair.jpg'
            },
            printer: {
                title: 'Printer Services',
                description: 'Professional printer repair and maintenance for home and office printers.',
                features: [
                    'Printer Repair',
                    'Ink System Maintenance',
                    'Network Configuration',
                    'Driver Installation',
                    'Print Quality Optimization',
                    'Preventative Maintenance'
                ],
                pricing: 'Starting from ₱350',
                turnaround: '1-2 business days',
                warranty: '14 days service warranty',
                image: '/img/printer-repair.jpg'
            },
            website: {
                title: 'Website Development',
                description: 'Professional website design and development services to establish your online presence and grow your business.',
                features: [
                    'Responsive Web Design',
                    'Custom Web Applications',
                    'E-commerce Solutions',
                    'Content Management Systems',
                    'SEO Optimization',
                    'Website Maintenance',
                    'Web Hosting Solutions'
                ],
                pricing: 'Starting from ₱5,000',
                turnaround: '7-14 business days',
                warranty: '30 days support included',
                image: '/img/website-development.jpg'
            }
        },

        initChat() {
            // Hide chat by default to prevent flashing
            this.isChatOpen = false;
            
            // Initialize scroll listener
            window.addEventListener('scroll', () => {
                this.showChatbot = window.pageYOffset > 100;
            });
            
            // Check initial scroll position
            this.showChatbot = window.pageYOffset > 100;
            
            // Initialize chat with welcome message
            this.chatMessages = [{
                sender: 'bot',
                message: 'Hello! 👋 I\'m your AI assistant. How can I help you today?',
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
            }];
        },

        toggleChat() {
            this.isChatOpen = !this.isChatOpen;
            if (this.isChatOpen && this.chatMessages.length === 0) {
                this.chatMessages = [{
                    sender: 'bot',
                    message: 'Hello! 👋 I\'m your AI assistant. How can I help you today?',
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                }];
            }
            
            // Auto scroll chat to bottom when opened
            if (this.isChatOpen) {
                this.$nextTick(() => {
                    const chatContainer = document.querySelector('.chat-messages');
                    if (chatContainer) {
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    }
                });
            }
        },

        async sendMessage() {
            if (this.newMessage.trim() === '') return;
            
            // Add user message
            const userMessage = this.newMessage;
            this.chatMessages.push({
                sender: 'user',
                message: userMessage,
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
            });
            
            // Clear input
            this.newMessage = '';
            
            // Show typing indicator
            this.isTyping = true;
            
            // Auto scroll to bottom
            this.$nextTick(() => {
                const chatContainer = document.querySelector('.chat-messages');
                if (chatContainer) {
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                }
            });
            
            try {
                // Generate AI response
                const aiResponse = await generateAIResponse(userMessage);
                
                // Hide typing indicator
                this.isTyping = false;
                
                // Add AI response
                this.chatMessages.push({
                    sender: 'bot',
                    message: aiResponse,
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                });
                
                // Auto scroll after response
                this.$nextTick(() => {
                    const chatContainer = document.querySelector('.chat-messages');
                    if (chatContainer) {
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    }
                });
            } catch (error) {
                console.error('Chat Error:', error);
                this.isTyping = false;
                
                // Add error message
                this.chatMessages.push({
                    sender: 'bot',
                    message: 'I apologize, but I\'m having trouble processing your request. For immediate assistance, please click the "Chat on Messenger" button below.',
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                });
            }
        },

        openServiceModal(service) {
            this.selectedService = this.services[service];
            this.showServiceModal = true;
            document.body.style.overflow = 'hidden';
        },

        closeServiceModal() {
            this.showServiceModal = false;
            this.selectedService = null;
            document.body.style.overflow = 'auto';
        }
    };
}