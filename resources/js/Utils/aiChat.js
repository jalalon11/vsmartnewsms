// AI Chat Utility for VSMART Hardware Repair System

// Predefined responses for common queries
const responses = {
    greetings: [
        "Hello! Welcome to VSMART. How can I assist you today?",
        "Hi there! I'm here to help with your hardware repair needs.",
        "Welcome to VSMART! What can I help you with?"
    ],
    services: {
        computer: "We offer comprehensive computer repair services including hardware diagnostics, software troubleshooting, virus removal, and performance optimization. Our computer repairs start from $50 with a 90-day warranty.",
        laptop: "Our laptop services include screen replacement, keyboard repair, battery replacement, RAM/SSD upgrades, and cooling system maintenance. Laptop repairs start from $60 with 1-4 business days turnaround.",
        mobile: "We provide expert mobile phone repair services including screen replacement, battery replacement, water damage repair, and charging port fixes. Mobile repairs start from $30 with same-day to 2-day turnaround.",
        printer: "Our printer services cover print quality issues, paper jam fixes, ink system repair, network setup, and driver installation. Printer repairs start from $40 with 1-2 business days turnaround.",
        website: "We develop custom websites and digital solutions including responsive design, e-commerce, SEO optimization, and ongoing maintenance. Website projects start from $500 with 1-4 weeks delivery."
    },
    pricing: {
        general: "Our pricing varies by service type:\n• Computer repairs: Starting from $50\n• Laptop repairs: Starting from $60\n• Mobile repairs: Starting from $30\n• Printer repairs: Starting from $40\n• Website development: Starting from $500\n\nAll repairs include diagnostic and come with warranty!",
        diagnostic: "We offer free diagnostic for most devices. The diagnostic fee is waived if you proceed with the repair."
    },
    warranty: {
        general: "We provide warranty on all our repair services:\n• Computer & Laptop repairs: 90 days\n• Mobile repairs: 60 days\n• Printer repairs: 60 days\n• Website development: 1 year support"
    },
    location: "We're located in the heart of the city with easy access. You can visit us or we offer pickup/delivery services for your convenience.",
    hours: "We're open Monday to Saturday, 9 AM to 6 PM. We're closed on Sundays but available for emergency repairs.",
    contact: "You can reach us through:\n• Phone: Call us directly\n• WhatsApp: Quick messaging\n• Facebook Messenger: Continue this chat\n• Visit our store: Walk-in service\n• Online booking: Schedule your repair",
    emergency: "For emergency repairs, we offer 24/7 support with additional charges. Contact us immediately for urgent hardware issues.",
    data: "We understand your data is precious. We offer secure data backup and recovery services. Your privacy and data security are our top priorities.",
    pickup: "Yes! We offer free pickup and delivery services within the city limits for repairs over $100. For smaller repairs, pickup service is available for a small fee.",
    turnaround: "Our typical turnaround times:\n• Mobile repairs: Same day - 2 days\n• Computer repairs: 1-3 business days\n• Laptop repairs: 1-4 business days\n• Printer repairs: 1-2 business days\n• Website projects: 1-4 weeks",
    payment: "We accept multiple payment methods including cash, credit/debit cards, bank transfers, and digital wallets. Payment is due upon completion of service."
}

// Keywords for intent recognition
const keywords = {
    greetings: ['hello', 'hi', 'hey', 'good morning', 'good afternoon', 'good evening'],
    services: {
        computer: ['computer', 'desktop', 'pc', 'tower'],
        laptop: ['laptop', 'notebook', 'macbook'],
        mobile: ['phone', 'mobile', 'smartphone', 'iphone', 'android', 'tablet'],
        printer: ['printer', 'printing', 'ink', 'toner'],
        website: ['website', 'web', 'development', 'design', 'online']
    },
    pricing: ['price', 'cost', 'how much', 'pricing', 'rate', 'fee', 'charge'],
    warranty: ['warranty', 'guarantee', 'coverage'],
    location: ['where', 'location', 'address', 'find you'],
    hours: ['hours', 'open', 'time', 'when'],
    contact: ['contact', 'reach', 'call', 'phone', 'email'],
    emergency: ['emergency', 'urgent', 'asap', '24/7'],
    data: ['data', 'files', 'backup', 'recovery', 'lost'],
    pickup: ['pickup', 'delivery', 'collect', 'bring'],
    turnaround: ['how long', 'time', 'duration', 'when ready'],
    payment: ['payment', 'pay', 'accept', 'method']
}

// Function to detect intent from user message
function detectIntent(message) {
    const lowerMessage = message.toLowerCase()
    
    // Check for greetings first
    if (keywords.greetings.some(keyword => lowerMessage.includes(keyword))) {
        return 'greetings'
    }
    
    // Check for specific services
    for (const [service, serviceKeywords] of Object.entries(keywords.services)) {
        if (serviceKeywords.some(keyword => lowerMessage.includes(keyword))) {
            return `service_${service}`
        }
    }
    
    // Check for other intents
    for (const [intent, intentKeywords] of Object.entries(keywords)) {
        if (intent !== 'greetings' && intent !== 'services') {
            if (intentKeywords.some(keyword => lowerMessage.includes(keyword))) {
                return intent
            }
        }
    }
    
    return 'unknown'
}

// Function to generate appropriate response
export async function generateAIResponse(userMessage) {
    return new Promise((resolve) => {
        // Simulate AI thinking time
        setTimeout(() => {
            const intent = detectIntent(userMessage)
            let response
            
            switch (intent) {
                case 'greetings':
                    response = responses.greetings[Math.floor(Math.random() * responses.greetings.length)]
                    break
                    
                case 'service_computer':
                    response = responses.services.computer
                    break
                    
                case 'service_laptop':
                    response = responses.services.laptop
                    break
                    
                case 'service_mobile':
                    response = responses.services.mobile
                    break
                    
                case 'service_printer':
                    response = responses.services.printer
                    break
                    
                case 'service_website':
                    response = responses.services.website
                    break
                    
                case 'pricing':
                    response = responses.pricing.general
                    break
                    
                case 'warranty':
                    response = responses.warranty.general
                    break
                    
                case 'location':
                    response = responses.location
                    break
                    
                case 'hours':
                    response = responses.hours
                    break
                    
                case 'contact':
                    response = responses.contact
                    break
                    
                case 'emergency':
                    response = responses.emergency
                    break
                    
                case 'data':
                    response = responses.data
                    break
                    
                case 'pickup':
                    response = responses.pickup
                    break
                    
                case 'turnaround':
                    response = responses.turnaround
                    break
                    
                case 'payment':
                    response = responses.payment
                    break
                    
                default:
                    response = "I understand you're looking for help with hardware repair services. Could you please be more specific about what you need? I can help with:\n\n• Computer & laptop repairs\n• Mobile phone repairs\n• Printer services\n• Website development\n• Pricing information\n• Service locations & hours\n\nWhat would you like to know more about?"
            }
            
            resolve(response)
        }, 1000 + Math.random() * 2000) // Random delay between 1-3 seconds
    })
}

// Function to get service information
export function getServiceInfo(serviceType) {
    return responses.services[serviceType] || "Service information not available."
}

// Function to check if message contains specific keywords
export function containsKeywords(message, keywordList) {
    const lowerMessage = message.toLowerCase()
    return keywordList.some(keyword => lowerMessage.includes(keyword.toLowerCase()))
}

// Export for external use
export { responses, keywords, detectIntent }