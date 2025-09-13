<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>VSMART TUNE UP</title>
        <meta name="description" content="Professional repair services for smartphones, laptops, tablets, and more. Fast, reliable, and backed by our satisfaction guarantee.">
        
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('img/LogoClear.png') }}">
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/') }}">
        <meta property="og:title" content="VSMART TUNE UP - Professional IT Solutions & Services">
        <meta property="og:description" content="Expert device repair and IT services with fast turnaround time and quality guarantee.">
        <meta property="og:image" content="{{ asset('img/og-image.jpg', true) ?? 'https://placehold.co/1200x630/red/white?text=VSMART+SYSTEM' }}">
        
        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ url('/') }}">
        <meta property="twitter:title" content="VSMART TUNE UP - Professional IT Solutions & Services">
        <meta property="twitter:description" content="Expert device repair and IT services with fast turnaround time and quality guarantee.">
        <meta property="twitter:image" content="{{ asset('img/og-image.jpg', true) ?? 'https://placehold.co/1200x630/red/white?text=VSMART+SYSTEM' }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/scrollAnimations.js', 'resources/js/webglBackground.js'])

        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- Fix for flashing elements before Alpine loads -->
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
<body class="antialiased bg-black" 
      x-data="chat()" 
      x-init="initChat()">
    <!-- Navigation -->
    @include('components.navigation')

    <!-- Main Content -->
    <main>
        <!-- Hero Banner -->
        @include('components.hero-banner')

        <!-- Services Carousel -->
        @include('components.services-carousel')

        <!-- Testimonials -->
        @include('components.testimonials')

        <!-- Contact Section -->
        <section id="contact" class="py-20 bg-black border-t border-red-800">
            <div class="container mx-auto px-6">
                <div class="max-w-5xl mx-auto">
                    <!-- Section Header -->
                    <div class="text-center mb-12">
                        <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4 scroll-fade-up">Ready to Experience Professional Device Service?</h2>
                        <p class="text-lg text-gray-400 scroll-fade-up delay-200">Follow our Facebook page to get expert IT solutions & services</p>
                    </div>
                    
                    <!-- Social Proof Section -->
                    <div class="bg-gradient-to-br from-gray-900 to-black rounded-2xl p-8 mb-12 border border-red-800 shadow-2xl scroll-fade-up delay-400">
                        <div class="grid md:grid-cols-2 gap-8 items-center">
                            <!-- Left Column: Facebook Preview -->
                            <div class="space-y-6 scroll-fade-right delay-600">
                                <div class="bg-black rounded-xl p-6 border border-red-700/30 backdrop-blur-sm shadow-lg transform hover:scale-[1.02] transition-all duration-300">
                                    <div class="flex items-center mb-6">
                                        <div class="relative">
                                            <img src="https://graph.facebook.com/vinzTSV/picture?type=large" alt="VSMART TUNE UP" 
                                                 class="w-20 h-20 rounded-xl border-2 border-red-600 shadow-lg">
                                            <div class="absolute -bottom-2 -right-2 bg-[#1877F2] rounded-full p-2">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-6">
                                            <h3 class="text-2xl font-bold text-white">VSMART TUNE UP</h3>
                                            <p class="text-red-500 font-medium">Reliable Service, Lasting Results</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Stats -->
                                    <div class="grid grid-cols-2 gap-4 mb-6">
                                        <div class="bg-gray-900/50 rounded-lg p-4 text-center">
                                            <div class="text-2xl font-bold text-red-500">190+</div>
                                            <div class="text-sm text-gray-400">Happy Customers</div>
                                        </div>
                                        <div class="bg-gray-900/50 rounded-lg p-4 text-center">
                                            <div class="text-2xl font-bold text-red-500">4.9★</div>
                                            <div class="text-sm text-gray-400">Average Rating</div>
                                        </div>
                                    </div>
                                    
                                    <p class="text-gray-300 text-sm italic">
                                        "Professional service with quality and excellent customer care. Our team of experts is ready to help you with all your device service needs!"
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Right Column: Call-to-Action -->
                            <div class="space-y-6 scroll-fade-left delay-600">
                                <div class="space-y-4">
                                    <h3 class="text-2xl font-bold text-white">Get in Touch</h3>
                                    <p class="text-gray-400">Need service or have questions? Contact us today for fast and reliable service.</p>
                                    
                                    <div class="space-y-3">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-600/20 flex items-center justify-center mt-1">
                                                <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-lg font-medium text-white">Call Us</div>
                                                <div class="text-gray-400">(+63) 995 627 7648</div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-600/20 flex items-center justify-center mt-1">
                                                <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-lg font-medium text-white">Email Us</div>
                                                <div class="text-gray-400">vsmarttuneup@gmail.com</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-8">
                                        <div class="flex justify-center space-x-4 scroll-fade-up">
                                            <a href="https://m.me/vinzTSV" target="_blank" rel="noopener noreferrer" 
                                               class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors">
                                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C6.486 2 2 6.262 2 11.5c0 2.545 1.088 4.988 3 6.772v4.228l4.135-2.525c.912.256 1.876.397 2.865.397 5.514 0 10-4.262 10-9.5S17.514 2 12 2zm1.593 12.226l-2.593-2.766-5.066 2.766 5.566-5.934 2.657 2.766 5.003-2.766-5.567 5.934z"/>
                                                </svg>
                                                Message Us
                                            </a>
                                            <a href="https://www.facebook.com/vinzTSV" target="_blank" rel="noopener noreferrer"
                                            class="inline-flex items-center px-6 py-3 border border-blue-600 text-blue-600 font-semibold rounded-lg hover:bg-blue-600 hover:text-white transition-colors">
                                                Follow Us
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    @include('components.footer')

    <!-- Floating Chat Widget -->
    <div x-cloak
         x-show="true"
         class="fixed bottom-4 sm:bottom-8 right-4 sm:right-8 z-[9999] flex flex-col items-end space-y-4">
        
        <!-- Chat Box -->
        <div x-cloak
             x-show="isChatOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-4"
             class="bg-gray-900 rounded-2xl shadow-2xl w-full sm:w-[360px] md:w-96 max-h-[90vh] sm:max-h-[85vh] flex flex-col border border-red-800">
            
            <!-- Chat Header -->
            <div class="p-3 sm:p-4 border-b border-red-800 flex items-center justify-between bg-black rounded-t-2xl shrink-0">
                <div class="flex items-center">
                    <img src="https://graph.facebook.com/vinzTSV/picture?type=square" 
                         alt="VSMART TUNE UP" 
                         class="w-8 h-8 sm:w-10 sm:h-10 rounded-full border-2 border-red-600">
                    <div class="ml-2 sm:ml-3">
                        <h3 class="text-white font-semibold text-sm sm:text-base">VSMART TUNE UP</h3>
                        <p class="text-green-500 text-xs sm:text-sm flex items-center">
                            <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-green-500 rounded-full mr-1.5 sm:mr-2"></span>
                            Online
                        </p>
                    </div>
                </div>
                <button @click="toggleChat" class="text-gray-400 hover:text-white transition-colors p-1">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Chat Messages -->
            <div class="flex-1 overflow-y-auto p-3 sm:p-4 space-y-3 sm:space-y-4 chat-messages min-h-[200px] sm:min-h-[300px]">
                <template x-for="(message, index) in chatMessages" :key="index">
                    <div :class="message.sender === 'user' ? 'flex justify-end' : 'flex justify-start'">
                        <div :class="message.sender === 'user' ? 'bg-red-600 text-white' : 'bg-gray-800 text-gray-200'"
                             class="max-w-[85%] sm:max-w-[80%] rounded-2xl px-3 py-2 sm:px-4 sm:py-2 shadow break-words">
                            <p x-text="message.message" class="text-sm whitespace-pre-wrap break-words"></p>
                            <span x-text="message.time" class="text-xs opacity-75 mt-1 block"></span>
                        </div>
                    </div>
                </template>
                
                <!-- Typing Indicator -->
                <div x-show="isTyping" class="flex justify-start">
                    <div class="bg-gray-800 text-gray-200 rounded-2xl px-3 py-2 sm:px-4 sm:py-2 shadow">
                        <div class="flex space-x-1 sm:space-x-2">
                            <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-gray-400 rounded-full animate-bounce"></div>
                            <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Chat Input -->
            <div class="p-3 sm:p-4 border-t border-red-800 bg-black shrink-0">
                <form @submit.prevent="sendMessage" class="flex space-x-2">
                    <input type="text" 
                           x-model="newMessage"
                           @keydown.enter.prevent="sendMessage"
                           placeholder="Type your message..."
                           :disabled="isTyping"
                           class="flex-1 bg-gray-900 text-white text-sm rounded-xl px-3 py-2 sm:px-4 sm:py-2 focus:outline-none focus:ring-2 focus:ring-red-500 border border-red-800 disabled:opacity-50 disabled:cursor-not-allowed">
                    <button type="submit"
                            :disabled="isTyping"
                            class="bg-red-600 text-white rounded-xl p-2 sm:px-4 sm:py-2 hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </form>
                <div class="mt-3 sm:mt-4 text-center">
                    <a href="https://m.me/vinzTSV" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="inline-flex items-center justify-center px-3 py-1.5 sm:px-4 sm:py-2 bg-[#1877F2] hover:bg-[#166fe5] text-white text-xs sm:text-sm font-medium rounded-xl transition-colors">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1.5 sm:mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.486 2 2 6.262 2 11.5c0 2.545 1.088 4.988 3 6.772v4.228l4.135-2.525c.912.256 1.876.397 2.865.397 5.514 0 10-4.262 10-9.5S17.514 2 12 2zm1.593 12.226l-2.593-2.766-5.066 2.766 5.566-5.934 2.657 2.766 5.003-2.766-5.567 5.934z"/>
                        </svg>
                        Chat on Messenger
                    </a>
                </div>
            </div>
        </div>

        <!-- Chat Button -->
        <button @click="toggleChat"
                x-cloak
                class="group flex items-center justify-center w-12 h-12 sm:w-16 sm:h-16 bg-red-600 rounded-full shadow-xl hover:bg-red-700 transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            
            <svg x-show="!isChatOpen" class="w-6 h-6 sm:w-8 sm:h-8 text-white transform group-hover:scale-110 transition-transform" 
                 fill="currentColor" 
                 viewBox="0 0 24 24">
                <path d="M12 2C6.486 2 2 6.262 2 11.5c0 2.545 1.088 4.988 3 6.772v4.228l4.135-2.525c.912.256 1.876.397 2.865.397 5.514 0 10-4.262 10-9.5S17.514 2 12 2zm1.593 12.226l-2.593-2.766-5.066 2.766 5.566-5.934 2.657 2.766 5.003-2.766-5.567 5.934z"/>
            </svg>
            <svg x-show="isChatOpen" class="w-6 h-6 sm:w-8 sm:h-8 text-white transform group-hover:scale-110 transition-transform" 
                 fill="none" 
                 stroke="currentColor" 
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            
            <!-- Tooltip -->
            <div x-show="!isChatOpen" 
                 x-cloak
                 class="absolute -top-12 sm:-top-16 right-0 pointer-events-none">
                <div class="bg-black text-white text-xs sm:text-sm py-1.5 sm:py-2 px-3 sm:px-4 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                    Chat with us
                    <div class="absolute bottom-0 right-4 sm:right-6 transform translate-y-1/2 rotate-45 w-2 h-2 bg-black"></div>
                </div>
            </div>
        </button>

        <!-- Pulse Effect -->
        <div x-show="!isChatOpen" x-cloak class="absolute inset-0 z-[-1]">
            <div class="absolute inset-0 animate-ping bg-red-600 rounded-full opacity-20"></div>
        </div>
    </div>

    <!-- Service Details Modal -->
    <div x-cloak
         x-show="showServiceModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-4"
         class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-black opacity-75"></div>
            </div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-gray-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full max-w-[95%] mx-auto"
                 @click.away="closeServiceModal">
                <div class="relative">
                    <!-- Close button -->
                    <button @click="closeServiceModal" class="absolute top-2 right-2 sm:top-4 sm:right-4 text-gray-400 hover:text-white transition-colors z-10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <!-- Service content -->
                    <div class="p-4 sm:p-8">
                        <h3 class="text-xl sm:text-2xl font-bold text-white mb-4" x-text="selectedService?.title"></h3>
                        <p class="text-gray-300 mb-6 text-sm sm:text-base" x-text="selectedService?.description"></p>

                        <!-- Service features -->
                        <div class="mb-6">
                            <h4 class="text-base sm:text-lg font-semibold text-red-500 mb-3">Services Offered</h4>
                            <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3">
                                <template x-for="feature in selectedService?.features" :key="feature">
                                    <li class="flex items-center text-gray-300 text-sm sm:text-base">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span x-text="feature"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <!-- Service details -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">
                            <div class="bg-black rounded-lg p-3 sm:p-4 text-center">
                                <div class="text-red-500 font-semibold mb-1 text-sm sm:text-base">Pricing</div>
                                <div class="text-white text-sm sm:text-base" x-text="selectedService?.pricing"></div>
                            </div>
                            <div class="bg-black rounded-lg p-3 sm:p-4 text-center">
                                <div class="text-red-500 font-semibold mb-1 text-sm sm:text-base">Turnaround</div>
                                <div class="text-white text-sm sm:text-base" x-text="selectedService?.turnaround"></div>
                            </div>
                            <div class="bg-black rounded-lg p-3 sm:p-4 text-center">
                                <div class="text-red-500 font-semibold mb-1 text-sm sm:text-base">Warranty</div>
                                <div class="text-white text-sm sm:text-base" x-text="selectedService?.warranty"></div>
                            </div>
                        </div>

                        <!-- Call to action -->
                        <div class="flex justify-center">
                            <a href="https://m.me/vinzTSV" target="_blank" rel="noopener noreferrer" 
                               class="inline-flex items-center px-4 py-2 sm:px-6 sm:py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors text-sm sm:text-base">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.486 2 2 6.262 2 11.5c0 2.545 1.088 4.988 3 6.772v4.228l4.135-2.525c.912.256 1.876.397 2.865.397 5.514 0 10-4.262 10-9.5S17.514 2 12 2zm1.593 12.226l-2.593-2.766-5.066 2.766 5.566-5.934 2.657 2.766 5.003-2.766-5.567 5.934z"/>
                                </svg>
                                Message Us
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Chat Functions -->
    <script>
        function chat() {
            return {
                isChatOpen: false,
                chatMessages: [
                    {
                        sender: 'bot',
                        message: 'Hello! Welcome to VSMART TUNE UP. How can we help you today?',
                        time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})
                    }
                ],
                isTyping: false,
                newMessage: '',
                showServiceModal: false,
                selectedService: null,

                initChat() {
                    // Initialize chat functionality
                },

                toggleChat() {
                    this.isChatOpen = !this.isChatOpen;
                },

                sendMessage() {
                    if (this.newMessage.trim() === '') return;
                    
                    // Add user message
                    this.chatMessages.push({
                        sender: 'user',
                        message: this.newMessage,
                        time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})
                    });
                    
                    const userMessage = this.newMessage;
                    this.newMessage = '';
                    
                    // Show typing indicator
                    this.isTyping = true;
                    
                    // Simulate bot response
                    setTimeout(() => {
                        this.isTyping = false;
                        this.chatMessages.push({
                            sender: 'bot',
                            message: 'Thank you for your message! For immediate assistance, please contact us via Facebook Messenger or call us at (+63) 995 627 7648.',
                            time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})
                        });
                        
                        // Scroll to bottom
                        this.$nextTick(() => {
                            const chatContainer = document.querySelector('.chat-messages');
                            if (chatContainer) {
                                chatContainer.scrollTop = chatContainer.scrollHeight;
                            }
                        });
                    }, 1500);
                },

                openServiceModal(serviceType) {
                    const services = {
                        computer: {
                            title: 'Computer Services',
                            description: 'Professional computer repair and maintenance services for desktops and workstations with expert diagnostics and quality solutions.',
                            features: [
                                'Hardware Diagnostics',
                                'Component Replacement',
                                'Software Installation',
                                'Virus Removal',
                                'Data Recovery',
                                'System Optimization'
                            ],
                            pricing: 'Starting at ₱500',
                            turnaround: '1-3 Days',
                            warranty: '30 Days'
                        },
                        mobile: {
                            title: 'Mobile Services',
                            description: 'Expert repair solutions for all major mobile brands with quality parts and prompt service for smartphones and tablets.',
                            features: [
                                'Screen Replacement',
                                'Battery Replacement',
                                'Bypass Services',
                                'Water Damage Repair',
                                'Charging Port Fix',
                                'Software Issues'
                            ],
                            pricing: 'Starting at ₱300',
                            turnaround: '1-2 Days',
                            warranty: '15 Days'
                        },
                        laptop: {
                            title: 'Laptop Services',
                            description: 'Comprehensive laptop repair services including hardware upgrades, screen replacements, and performance optimization.',
                            features: [
                                'Screen Replacement',
                                'Keyboard Repair',
                                'Battery Replacement',
                                'RAM/SSD Upgrade',
                                'Cooling System Cleaning',
                                'Motherboard Repair'
                            ],
                            pricing: 'Starting at ₱800',
                            turnaround: '2-4 Days',
                            warranty: '30 Days'
                        },
                        printer: {
                            title: 'Printer Services',
                            description: 'Professional printer repair and maintenance services for all types of printers with genuine parts and expert care.',
                            features: [
                                'Print Head Cleaning',
                                'Paper Jam Resolution',
                                'Ink System Repair',
                                'Driver Installation',
                                'Network Setup',
                                'Maintenance Service'
                            ],
                            pricing: 'Starting at ₱400',
                            turnaround: '1-2 Days',
                            warranty: '15 Days'
                        },
                        website: {
                            title: 'Website Development',
                            description: 'Custom website development and design services to establish your online presence with modern, responsive designs.',
                            features: [
                                'Custom Design',
                                'Responsive Layout',
                                'SEO Optimization',
                                'Content Management',
                                'E-commerce Integration',
                                'Maintenance Support'
                            ],
                            pricing: 'Starting at ₱15,000',
                            turnaround: '1-2 Weeks',
                            warranty: '90 Days'
                        }
                    };
                    
                    this.selectedService = services[serviceType] || null;
                    this.showServiceModal = true;
                },

                closeServiceModal() {
                    this.showServiceModal = false;
                    this.selectedService = null;
                }
            }
        }
    </script>
    </body>
</html>