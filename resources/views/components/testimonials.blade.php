<!-- Testimonials Section -->
<section id="testimonials" class="py-20 bg-black border-t border-red-800" 
    x-data="{ 
        showFeedbackForm: false,
        toggleForm() {
            console.log('Toggle form called, current state:', this.showFeedbackForm);
            this.showFeedbackForm = !this.showFeedbackForm;
            console.log('New state:', this.showFeedbackForm);
        },
        init() {
            console.log('Alpine.js initialized, showFeedbackForm:', this.showFeedbackForm);
            // Only show modal if there's a success message AND we're specifically on the testimonials section
            if(window.location.hash === '#testimonials' && document.getElementById('success-alert')) {
                this.showFeedbackForm = true;
                console.log('Hash detected with success alert, showFeedbackForm set to:', this.showFeedbackForm);
                // Scroll to testimonials section
                this.$el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }" x-cloak>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <div class="inline-block px-3 py-1 bg-red-900/30 rounded-full text-red-500 text-sm font-semibold mb-4 scroll-fade-up">WHAT PEOPLE SAY</div>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4 scroll-fade-up delay-200">What Our Customers Say</h2>
            <p class="text-lg text-gray-400 scroll-fade-up delay-300">Don't just take our word for it - hear from our satisfied customers</p>
        </div>

        <!-- Testimonials Grid/Carousel -->
        @php
            $featuredTestimonials = App\Models\Feedback::where('is_featured', true)->latest()->get();
            $needsScrolling = $featuredTestimonials->count() > 3;
        @endphp

        <div class="mb-16 relative">
            @if($needsScrolling)
                <!-- Scroll Controls -->
                <div class="flex justify-end mb-4 gap-2 scroll-controls">
                    <button id="scroll-left" class="p-3 rounded-full bg-red-900/30 text-red-500 hover:bg-red-900/50 transition-colors duration-300 focus:outline-none shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <button id="scroll-right" class="p-3 rounded-full bg-red-900/30 text-red-500 hover:bg-red-900/50 transition-colors duration-300 focus:outline-none shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Fixed 3-card display with smooth sliding -->
                <div class="relative overflow-hidden">
                    <div id="testimonials-container" class="testimonials-wrapper">
                        <div class="testimonial-track" style="transform: translateX(0);">
                            @foreach($featuredTestimonials as $testimonial)
                            <div class="testimonial-card bg-gray-900 rounded-xl p-0 shadow-xl border border-gray-800 overflow-hidden">
                                <!-- Card header with rating -->
                                <div class="bg-gradient-to-r from-gray-800 to-gray-900 px-6 py-4 border-b border-gray-800 flex items-center justify-between">
                                    <div class="flex items-center space-x-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="h-5 w-5 {{ $i <= $testimonial->rating ? 'text-red-500' : 'text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    @if($testimonial->service_type)
                                    <div class="bg-red-900/30 text-red-400 text-xs px-3 py-1 rounded-full font-medium">
                                        {{ $testimonial->service_type }}
                                    </div>
                                    @endif
                                </div>
                                
                                <!-- Card content -->
                                <div class="p-6 flex flex-col flex-grow">
                                    <!-- Quote content -->
                                    <div class="quote-container relative mb-6 flex-grow">
                                        <svg class="absolute top-0 left-0 h-10 w-10 text-red-600/40 -ml-2 -mt-1 z-0" fill="currentColor" viewBox="0 0 32 32">
                                            <path d="M9.352 4C4.456 7.456 1 13.12 1 19.36c0 5.088 3.072 8.064 6.624 8.064 3.36 0 5.856-2.688 5.856-5.856 0-3.168-2.208-5.472-5.088-5.472-.576 0-1.344.096-1.536.192.48-3.264 3.552-7.104 6.624-9.024L9.352 4zm16.512 0c-4.8 3.456-8.256 9.12-8.256 15.36 0 5.088 3.072 8.064 6.624 8.064 3.264 0 5.856-2.688 5.856-5.856 0-3.168-2.304-5.472-5.184-5.472-.576 0-1.248.096-1.44.192.48-3.264 3.456-7.104 6.528-9.024L25.864 4z" />
                                        </svg>
                                        <div class="text-gray-300 pt-6 px-6 italic leading-relaxed quote-text adaptive-text">
                                            {{ $testimonial->message }}
                                        </div>
                                    </div>
                                    
                                    <!-- Author info -->
                                    <div class="flex items-center mt-auto">
                                        <div class="flex-shrink-0">
                                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-red-900 to-red-600 flex items-center justify-center shadow-lg border-2 border-red-900/50">
                                                <span class="text-white font-bold text-lg">{{ strtoupper(substr($testimonial->name, 0, 1)) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-white font-semibold text-base">{{ $testimonial->name }}</div>
                                            <div class="text-gray-400 text-sm">{{ \Carbon\Carbon::parse($testimonial->created_at)->format('M Y') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Scroll Indicators -->
                <div class="flex justify-center mt-8 space-x-3">
                    @for($i = 0; $i < ceil($featuredTestimonials->count() / 3); $i++)
                        <button class="testimonial-indicator w-3 h-3 rounded-full bg-gray-600 hover:bg-red-500 focus:outline-none transition-all duration-300 {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}"></button>
                    @endfor
                </div>
            @else
                <!-- Regular Grid for <= 3 testimonials -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($featuredTestimonials as $testimonial)
                    <div class="bg-gray-900 rounded-xl p-0 shadow-xl border border-gray-800 overflow-hidden transform hover:scale-105 transition-transform duration-300">
                        <!-- Card header with rating -->
                        <div class="bg-gradient-to-r from-gray-800 to-gray-900 px-6 py-4 border-b border-gray-800 flex items-center justify-between">
                            <div class="flex items-center space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="h-5 w-5 {{ $i <= $testimonial->rating ? 'text-red-500' : 'text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                            @if($testimonial->service_type)
                            <div class="bg-red-900/30 text-red-400 text-xs px-3 py-1 rounded-full font-medium">
                                {{ $testimonial->service_type }}
                            </div>
                            @endif
                        </div>
                        
                        <!-- Card content -->
                        <div class="p-6 flex flex-col flex-grow">
                            <!-- Quote content -->
                            <div class="quote-container relative mb-6 flex-grow">
                                <svg class="absolute top-0 left-0 h-10 w-10 text-red-600/40 -ml-2 -mt-1 z-0" fill="currentColor" viewBox="0 0 32 32">
                                    <path d="M9.352 4C4.456 7.456 1 13.12 1 19.36c0 5.088 3.072 8.064 6.624 8.064 3.36 0 5.856-2.688 5.856-5.856 0-3.168-2.208-5.472-5.088-5.472-.576 0-1.344.096-1.536.192.48-3.264 3.552-7.104 6.624-9.024L9.352 4zm16.512 0c-4.8 3.456-8.256 9.12-8.256 15.36 0 5.088 3.072 8.064 6.624 8.064 3.264 0 5.856-2.688 5.856-5.856 0-3.168-2.304-5.472-5.184-5.472-.576 0-1.248.096-1.44.192.48-3.264 3.456-7.104 6.528-9.024L25.864 4z" />
                                </svg>
                                <div class="text-gray-300 pt-6 px-6 italic leading-relaxed quote-text adaptive-text">
                                    {{ $testimonial->message }}
                                </div>
                            </div>
                            
                            <!-- Author info -->
                            <div class="flex items-center mt-auto">
                    <div class="flex-shrink-0">
                                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-red-900 to-red-600 flex items-center justify-center shadow-lg border-2 border-red-900/50">
                                        <span class="text-white font-bold text-lg">{{ strtoupper(substr($testimonial->name, 0, 1)) }}</span>
                        </div>
                    </div>
                    <div class="ml-4">
                                    <div class="text-white font-semibold text-base">{{ $testimonial->name }}</div>
                                    <div class="text-gray-400 text-sm">{{ \Carbon\Carbon::parse($testimonial->created_at)->format('M Y') }}</div>
                                </div>
                    </div>
                </div>
            </div>
            @endforeach
                </div>
            @endif
        </div>

        <!-- Call to Action Button -->
        <div class="text-center mb-16 scroll-fade-up delay-800" x-show="!showFeedbackForm">
            <button @click="toggleForm()" 
                class="inline-flex items-center px-8 py-4 border-2 border-red-600 rounded-full text-lg font-semibold text-white bg-red-600 hover:bg-red-700 hover:border-red-700 transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <span>Share Your Experience</span>
                <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                </svg>
            </button>
        </div>

        <!-- Modal Backdrop -->
        <div x-show="showFeedbackForm" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            @click="showFeedbackForm = false">
            
            <!-- Modal Content -->
            <div id="feedback-form-container" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="bg-gray-900 rounded-lg p-8 relative max-w-2xl w-full max-h-[90vh] overflow-y-auto"
                @click.stop>
                <!-- Close Button -->
                <button @click="showFeedbackForm = false" class="absolute top-4 right-4 text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <h3 class="text-2xl font-bold text-white mb-6 text-center">Share Your Experience</h3>
                
                @if(session('success'))
                <div id="success-alert" class="bg-gray-900 border-l-4 border-red-500 text-white px-6 py-5 rounded-lg shadow-lg mb-6 relative transform transition-all duration-500 ease-in-out" role="alert">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-10 w-10 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-semibold text-red-400">{{ session('success') }}</h4>
                            <p class="mt-1 text-gray-300">Your feedback is valuable to us and helps improve our services!</p>
                        </div>
                    </div>
                    <div class="absolute top-3 right-3">
                        <button @click="showFeedbackForm = false" id="close-alert" type="button" class="text-gray-400 hover:text-white focus:outline-none">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="progress-bar absolute bottom-0 left-0 h-1 bg-red-500 transition-all duration-5000" style="width: 100%;"></div>
                </div>
                @else
                <form action="{{ route('feedback.store') }}#testimonials" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Fallback for when JavaScript is disabled -->
                    <noscript>
                        <div class="bg-red-900/30 text-red-400 p-4 rounded-lg mb-4">
                            <p>JavaScript is disabled. The form will still work, but you will be redirected after submission.</p>
                        </div>
                    </noscript>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-300">Name</label>
                            <input type="text" name="name" id="name" required
                                class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                            <input type="email" name="email" id="email" required
                                class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50">
                        </div>
                    </div>

                    <div>
                        <label for="service_type" class="block text-sm font-medium text-gray-300">Service Type (Optional)</label>
                        <select name="service_type" id="service_type" 
                            class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50">
                            <option value="">Select a service</option>
                            <option value="Computer Service">Computer Service</option>
                            <option value="Mobile Service">Mobile Service</option>
                            <option value="Printer Service">Printer Service</option>
                            <option value="Website Development">Website Development</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Rating</label>
                        <div class="flex space-x-2">
                            @for ($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer group">
                                <input type="radio" name="rating" value="{{ $i }}" class="sr-only" {{ $i == 5 ? 'checked' : '' }}>
                                <svg class="w-8 h-8 text-gray-400 group-hover:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </label>
                            @endfor
                        </div>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-300">Your Message</label>
                        <textarea id="message" name="message" rows="4" required
                            class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50"></textarea>
                    </div>

                    <div class="flex justify-center">
                        <button type="submit"
                            class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                            Submit Feedback
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

    <style>
        /* Hide scrollbar but keep functionality */
        .hide-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
            overflow-x: hidden; /* Hide horizontal scrollbar */
        }
        
        .hide-scrollbar::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }
        
        /* Active indicator */
        .testimonial-indicator.active {
            background-color: rgb(239, 68, 68); /* Red-500 */
            width: 20px;
            transform: scale(1.1);
        }

        /* Improved testimonial track for smooth sliding */
        .testimonials-wrapper {
            position: relative;
            overflow: hidden;
            width: 100%;
            min-height: 420px;
        }

        .testimonial-track {
            display: flex;
            transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            height: 100%;
        }

        /* Enhanced testimonial cards */
        .testimonial-card {
            flex: 0 0 calc(33.333% - 1rem);
            margin-right: 1rem;
            display: flex;
            flex-direction: column;
            min-height: 380px;
            transition: all 0.5s ease;
            transform: scale(0.95);
            opacity: 0.9;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        .testimonial-card:hover {
            transform: scale(1);
            opacity: 1;
            box-shadow: 0 8px 30px rgba(220, 38, 38, 0.2);
        }

        /* Quote styling improvements */
        .quote-container {
            position: relative;
            min-height: 140px;
            padding: 0 0.5rem;
        }

        .quote-text {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            word-wrap: break-word;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }
        
        /* Adaptive text sizing based on content length */
        .adaptive-text {
            font-size: 1rem;
        }
        
        .adaptive-text:not(.text-clamp-active) {
            font-size: 1.125rem;
            line-height: 1.7;
        }
        
        .adaptive-text.very-short {
            font-size: 1.25rem;
            line-height: 1.8;
            font-weight: 500;
        }
        
        /* Improved quote marks */
        .quote-container svg {
            filter: drop-shadow(0 2px 4px rgba(220, 38, 38, 0.2));
        }
        
        .testimonial-card:hover .quote-container svg {
            transform: scale(1.1);
            opacity: 0.6;
            transition: all 0.3s ease;
        }

        /* Professional card header */
        .testimonial-card .bg-gradient-to-r {
            background-size: 200% 200%;
            animation: subtle-gradient 8s ease infinite;
        }

        @keyframes subtle-gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Improved avatar styling */
        .testimonial-card .rounded-full {
            transition: all 0.3s ease;
        }

        .testimonial-card:hover .rounded-full {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(220, 38, 38, 0.3);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .testimonial-card {
                flex: 0 0 calc(100% - 1rem);
                min-height: 320px;
            }
            
            .testimonial-card .quote-text {
                -webkit-line-clamp: 5;
            }
        }

        /* Enhanced indicators and controls */
        .scroll-controls button {
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            opacity: 0.7;
        }

        .scroll-controls button:hover {
            transform: scale(1.15);
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);
            opacity: 1;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.85; }
            50% { opacity: 1; }
        }

        .testimonial-indicator {
            transition: all 0.3s ease;
            opacity: 0.6;
        }

        .testimonial-indicator:hover {
            opacity: 0.9;
            transform: scale(1.2);
        }

        .testimonial-indicator.active {
            opacity: 1;
            animation: pulse 2s infinite;
        }

        /* Success Alert Animations */
        #success-alert {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.5s ease, transform 0.5s ease;
            position: relative;
            overflow: hidden;
        }
        
        .success-alert.opacity-0 {
            opacity: 0;
            transform: translateY(-20px);
        }
        
        .success-alert .progress-bar {
            animation: countdown 5s linear forwards;
        }
        
        @keyframes countdown {
            from { width: 100%; }
            to { width: 0%; }
        }
    </style>

    <!-- Hidden input with count value -->
    <div class="hidden">
        <input type="hidden" id="testimonial-count" value="{{ $featuredTestimonials->count() }}">
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
        // Star rating functionality
        document.querySelectorAll('input[name="rating"]').forEach(input => {
            input.addEventListener('change', function() {
                const rating = this.value;
                document.querySelectorAll('label svg').forEach((star, index) => {
                    star.classList.toggle('text-yellow-400', index < rating);
                    star.classList.toggle('text-gray-400', index >= rating);
                });
            });
        });

            // Custom event listener for toggling the form from outside Alpine
            document.getElementById('testimonials')?.addEventListener('toggle-form', function(e) {
                console.log('Received toggle-form event:', e.detail);
                if (this.__x) {
                    this.__x.$data.showFeedbackForm = !!e.detail.show;
                    console.log('Set form visibility to:', this.__x.$data.showFeedbackForm);
                }
            });

            // Success message handling
        if (document.querySelector('[role="alert"]')) {
                const alertElement = document.getElementById('success-alert');
                const progressBar = alertElement.querySelector('.progress-bar');
                
                // Entrance animation
                alertElement.classList.add('animate-fadeIn');
                
                // Start progress bar animation
                setTimeout(() => {
                    progressBar.style.width = '0%';
                }, 300);
                
                // Setup exit animation after 4.7 seconds
                setTimeout(() => {
                    alertElement.classList.add('opacity-0', 'translate-y-2');
                }, 4700);
                
                // Close alert on click
                document.getElementById('close-alert').addEventListener('click', () => {
                    alertElement.classList.add('opacity-0', 'translate-y-2');
                    setTimeout(() => {
                        alertElement.style.display = 'none';
                    }, 500);
                });
            }

            // Handle scroll to sections on page load
            if (window.location.hash) {
                const targetId = window.location.hash.substring(1); // Remove the # character
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    // Add slight delay to ensure page is fully loaded
            setTimeout(() => {
                        targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 100);
                }
            }
            
            // Adaptive text sizing based on content length
            document.querySelectorAll('.adaptive-text').forEach(textElement => {
                const text = textElement.textContent.trim();
                const wordCount = text.split(/\s+/).length;
                
                // Check if content is short enough to display larger text
                if (wordCount < 15) {
                    textElement.classList.add('very-short');
                } else {
                    // Create a clone to test if text is being clamped
                    const clone = textElement.cloneNode(true);
                    clone.style.webkitLineClamp = 'unset';
                    clone.style.display = 'block';
                    clone.style.visibility = 'hidden';
                    clone.style.position = 'absolute';
                    clone.style.width = textElement.offsetWidth + 'px';
                    document.body.appendChild(clone);
                    
                    // If original height is less than clone, text is being clamped
                    if (textElement.offsetHeight < clone.offsetHeight) {
                        textElement.classList.add('text-clamp-active');
                    }
                    
                    document.body.removeChild(clone);
                }
            });

            // Enhanced smooth testimonial carousel
            const container = document.getElementById('testimonials-container');
            if (!container) return;
            
            const track = container.querySelector('.testimonial-track');
            if (!track) return;
            
            const scrollLeft = document.getElementById('scroll-left');
            const scrollRight = document.getElementById('scroll-right');
            const indicators = document.querySelectorAll('.testimonial-indicator');
            const cards = track.querySelectorAll('.testimonial-card');
            
            if (cards.length <= 3) return; // No need for carousel if 3 or fewer testimonials
            
            // Calculate dimensions for sliding
            const cardWidth = window.innerWidth > 768 ? 
                container.offsetWidth / 3 : container.offsetWidth;
            
            // Setup for sliding transition
            const visibleCards = window.innerWidth > 768 ? 3 : 1;
            const totalSets = Math.ceil(cards.length / visibleCards);
            let currentSet = 0;
            
            // Apply initial positioning
            positionCards();
            
            // Start automatic scrolling every 5 seconds
            let autoScrollInterval = setInterval(nextSlide, 5000);
            
            // Navigation controls
            if (scrollLeft) {
                scrollLeft.addEventListener('click', function() {
                    prevSlide();
                });
            }
            
            if (scrollRight) {
                scrollRight.addEventListener('click', function() {
                    nextSlide();
                });
            }
            
            // Indicator clicks
            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', function() {
                    goToSlide(index);
                });
            });
            
            // Responsive handling
            window.addEventListener('resize', function() {
                // Recalculate dimensions
                const newCardWidth = window.innerWidth > 768 ? 
                    container.offsetWidth / 3 : container.offsetWidth;
                
                // Update visible cards
                const newVisibleCards = window.innerWidth > 768 ? 3 : 1;
                
                // Only reposition if needed
                if (newCardWidth !== cardWidth || newVisibleCards !== visibleCards) {
                    positionCards();
                }
                
                // Re-check text adaption on resize
                document.querySelectorAll('.adaptive-text').forEach(textElement => {
                    textElement.classList.remove('text-clamp-active');
                    textElement.classList.remove('very-short');
                    
                    const text = textElement.textContent.trim();
                    const wordCount = text.split(/\s+/).length;
                    
                    if (wordCount < 15) {
                        textElement.classList.add('very-short');
                    } else {
                        const clone = textElement.cloneNode(true);
                        clone.style.webkitLineClamp = 'unset';
                        clone.style.display = 'block';
                        clone.style.visibility = 'hidden';
                        clone.style.position = 'absolute';
                        clone.style.width = textElement.offsetWidth + 'px';
                        document.body.appendChild(clone);
                        
                        if (textElement.offsetHeight < clone.offsetHeight) {
                            textElement.classList.add('text-clamp-active');
                        }
                        
                        document.body.removeChild(clone);
                    }
                });
            });
            
            // Smooth transition to next slide
            function nextSlide() {
                goToSlide((currentSet + 1) % totalSets);
            }
            
            // Smooth transition to previous slide
            function prevSlide() {
                goToSlide((currentSet - 1 + totalSets) % totalSets);
            }
            
            // Go to specific slide with smooth animation
            function goToSlide(slideIndex) {
                currentSet = slideIndex;
                positionCards();
                updateIndicators();
                resetAutoScroll();
            }
            
            // Position cards using transform for smooth animation
            function positionCards() {
                const newCardWidth = window.innerWidth > 768 ? 
                    container.offsetWidth / 3 : container.offsetWidth;
                
                // Update the transform to slide to current position
                const offset = -currentSet * (newCardWidth * (window.innerWidth > 768 ? 3 : 1));
                track.style.transform = `translateX(${offset}px)`;
            }
            
            // Update the indicator dots
            function updateIndicators() {
                indicators.forEach((indicator, i) => {
                    indicator.classList.toggle('active', i === currentSet);
                });
            }
            
            // Reset auto-scroll timer
            function resetAutoScroll() {
                clearInterval(autoScrollInterval);
                autoScrollInterval = setInterval(nextSlide, 5000);
            }
            
            // Pause auto-scroll on hover
            container.addEventListener('mouseenter', () => {
                clearInterval(autoScrollInterval);
            });
            
            // Resume auto-scroll when mouse leaves
            container.addEventListener('mouseleave', () => {
                autoScrollInterval = setInterval(nextSlide, 5000);
            });

            // Handle feedback form submission
            const feedbackForm = document.querySelector('form#feedback-form');
            if (feedbackForm) {
                // Create fallback success message function
                function showSuccessMessage(message = 'Thank you for your feedback!') {
                    console.log('Showing success message:', message);
                    
                    // Reset the form
                    feedbackForm.reset();
                    
                    // Create and show success alert
                    const successAlert = document.createElement('div');
                    successAlert.id = 'success-alert';
                    successAlert.className = 'success-alert fixed top-4 right-4 bg-black bg-opacity-90 border-l-4 border-red-500 text-white p-4 rounded shadow-lg z-50 transform transition-transform duration-500 ease-out';
                    successAlert.innerHTML = `
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <p>${message}</p>
                            </div>
                            <button type="button" class="text-white hover:text-gray-300" onclick="this.parentElement.parentElement.remove()">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="progress-bar absolute bottom-0 left-0 h-1 bg-red-500 transition-all duration-5000" style="width: 100%;"></div>
                    `;
                    document.body.appendChild(successAlert);
                    
                    // Hide the form via Alpine.js
                    const testimonialSection = document.getElementById('testimonials');
                    if (testimonialSection && typeof testimonialSection.__x !== 'undefined') {
                        console.log('Found testimonial section with Alpine.js data:', testimonialSection.__x.$data);
                        try {
                            const alpineData = testimonialSection.__x.$data;
                            alpineData.showFeedbackForm = false;
                            console.log('Set showFeedbackForm to false');
                            
                            // Alternative approach if the above doesn't work
                            testimonialSection.dispatchEvent(new CustomEvent('toggle-form', {
                                detail: { show: false }
                            }));
                            console.log('Dispatched toggle-form event');
                        } catch (e) {
                            console.error('Error toggling form:', e);
                        }
                    } else {
                        console.warn('Could not find testimonial section with Alpine.js data');
                    }
                    
                    // Auto-remove the alert after 5 seconds
                    setTimeout(() => {
                        if (successAlert.parentElement) {
                            successAlert.classList.add('opacity-0');
                            setTimeout(() => {
                                if (successAlert.parentElement) {
                                    successAlert.remove();
                                }
                            }, 500);
                }
            }, 5000);
                    
                    // Directly click the close button if it exists
                    try {
                        const closeBtn = document.querySelector('#feedback-form-container button');
                        if (closeBtn) {
                            console.log('Found close button, clicking it');
                            closeBtn.click();
                        } else {
                            console.warn('Could not find close button');
                        }
                    } catch (e) {
                        console.error('Error clicking close button:', e);
                    }
                }
            
                feedbackForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    console.log('Form submission intercepted');
                    
                    // Show a temporary loading indicator
                    const submitBtn = feedbackForm.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Submitting...
                    `;
                    
                    // Set a timeout to show success message even if fetch fails
                    let timeoutId = setTimeout(() => {
                        console.log('Fetch timeout reached, submitting form directly');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                        
                        // Fall back to standard form submission
                        const oldAction = feedbackForm.action;
                        feedbackForm.removeEventListener('submit', arguments.callee);
                        feedbackForm.submit();
                        return;
                    }, 2000);
                });
            }
        });
    </script>
</section>