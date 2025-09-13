<!-- Services Carousel Component -->
<section id="services" class="py-24 bg-gradient-to-b from-black to-gray-900 relative overflow-hidden" 
         x-data="{ 
            activeSlide: 0,
            autoPlayInterval: null,
            slides: 5,
            isHovering: false,
            
            init() {
                // Start the autoplay immediately
                this.startAutoPlay();
                
                // Set up listeners using proper scope handling
                const el = this.$el;
                
                // Pause auto-rotation when mouse enters carousel area
                el.addEventListener('mouseenter', () => {
                    this.isHovering = true;
                    this.pauseAutoPlay();
                });
                
                // Resume auto-rotation when mouse leaves carousel area
                el.addEventListener('mouseleave', () => {
                    this.isHovering = false;
                    this.startAutoPlay();
                });
                
                // Clean up when component is destroyed
                this.$cleanup = () => {
                    this.pauseAutoPlay();
                    el.removeEventListener('mouseenter');
                    el.removeEventListener('mouseleave');
                };
            },
            
            startAutoPlay() {
                // Only start if not hovering and not already running
                if (this.isHovering || this.autoPlayInterval) return;
                
                this.autoPlayInterval = setInterval(() => {
                    this.nextSlide();
                }, 5000);
            },
            
            pauseAutoPlay() {
                if (this.autoPlayInterval) {
                    clearInterval(this.autoPlayInterval);
                    this.autoPlayInterval = null;
                }
            },
            
            nextSlide() {
                this.activeSlide = (this.activeSlide + 1) % this.slides;
            },
            
            prevSlide() {
                this.activeSlide = (this.activeSlide - 1 + this.slides) % this.slides;
            },
            
            goToSlide(index) {
                this.activeSlide = index;
                // Reset timer when manually changing slides
                this.pauseAutoPlay();
                this.startAutoPlay();
            }
         }"
         @visibilitychange.window="document.hidden ? pauseAutoPlay() : startAutoPlay()">
         
    <!-- Floating Background Icons -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <!-- Computer/Laptop Icons -->
        <div class="floating-icon" style="top: 15%; left: 5%; animation-delay: 0s; animation-duration: 15s;">
            <svg class="w-12 h-12 text-blue-500/40" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20 18c1.1 0 1.99-.9 1.99-2L22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2h-4zM4 6h16v10H4V6z"/>
            </svg>
        </div>
        <div class="floating-icon" style="top: 60%; left: 8%; animation-delay: 3s; animation-duration: 18s;">
            <svg class="w-16 h-16 text-indigo-600/30" fill="currentColor" viewBox="0 0 24 24">
                <path d="M21 14c0-.55-.45-1-1-1h-2v2h2c.55 0 1-.45 1-1zm-1 3h-2v2h2c.55 0 1-.45 1-1s-.45-1-1-1zm-8-3h-2v4h2c1.1 0 2-.9 2-2s-.9-2-2-2zm8-11H4c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 18H4V5h16v16z"/>
            </svg>
        </div>
        
        <!-- Mobile/Tablet Icons -->
        <div class="floating-icon" style="top: 25%; right: 5%; animation-delay: 1s; animation-duration: 12s;">
            <svg class="w-14 h-14 text-green-500/40" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/>
            </svg>
        </div>
        <div class="floating-icon" style="top: 70%; right: 10%; animation-delay: 5s; animation-duration: 20s;">
            <svg class="w-10 h-10 text-emerald-600/30" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19.5 3h-15C3.12 3 2 4.12 2 5.5v13C2 19.88 3.12 21 4.5 21h15c1.38 0 2.5-1.12 2.5-2.5v-13C22 4.12 20.88 3 19.5 3zm-.5 15H5V5h14v13z"/>
            </svg>
        </div>
        
        <!-- Hardware Icons -->
        <div class="floating-icon" style="top: 40%; left: 15%; animation-delay: 2s; animation-duration: 17s;">
            <svg class="w-12 h-12 text-amber-500/40" fill="currentColor" viewBox="0 0 24 24">
                <path d="M2 20h20v-4H2v4zm2-3h2v2H4v-2zM2 4v4h20V4H2zm4 3H4V5h2v2zm-4 7h20v-4H2v4zm2-3h2v2H4v-2z"/>
            </svg>
        </div>
        <div class="floating-icon" style="top: 20%; left: 30%; animation-delay: 4s; animation-duration: 14s;">
            <svg class="w-8 h-8 text-orange-500/40" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20.84 3.6l-0.8-0.8c-0.2-0.2-0.51-0.2-0.71 0L17 5.13V2h-2v4.83l-0.8 0.8c-1.3 1.3-1.3 3.41 0 4.71l0.8 0.8c-1.49 1.49-3.89 1.49-5.38 0-1.49-1.49-1.49-3.89 0-5.38 0.2-0.2 0.2-0.51 0-0.71l-0.8-0.8c-0.2-0.2-0.51-0.2-0.71 0-2.58 2.58-2.58 6.78 0 9.36 2.56 2.56 6.65 2.59 9.24 0.09l2.49 2.49 1.41-1.41-2.49-2.49c0.71-0.74 1.22-1.62 1.47-2.57 0.25-0.95 0.25-1.95 0-2.9-0.25-0.94-0.76-1.82-1.47-2.57z"/>
            </svg>
        </div>
        
        <!-- Software Icons -->
        <div class="floating-icon" style="top: 50%; right: 25%; animation-delay: 3.5s; animation-duration: 16s;">
            <svg class="w-14 h-14 text-purple-500/40" fill="currentColor" viewBox="0 0 24 24">
                <path d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0l4.6-4.6-4.6-4.6L16 6l6 6-6 6-1.4-1.4z"/>
            </svg>
        </div>
        <div class="floating-icon" style="top: 75%; left: 30%; animation-delay: 6s; animation-duration: 19s;">
            <svg class="w-10 h-10 text-fuchsia-600/30" fill="currentColor" viewBox="0 0 24 24">
                <path d="M21 2H3c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 18H4c-.55 0-1-.45-1-1V5c0-.55.45-1 1-1h16c.55 0 1 .45 1 1v14c0 .55-.45 1-1 1zM8 9h8v2H8V9zm0 4h8v2H8v-2zm0-8h8v2H8V5zm0 12h4v-2H8v2z"/>
            </svg>
        </div>
        
        <!-- Network Icons -->
        <div class="floating-icon" style="top: 35%; right: 40%; animation-delay: 0.5s; animation-duration: 13s;">
            <svg class="w-16 h-16 text-cyan-500/40" fill="currentColor" viewBox="0 0 24 24">
                <path d="M1 9l2 2c4.97-4.97 13.03-4.97 18 0l2-2c-6.07-6.07-15.92-6.07-22 0zm8 8l3 3 3-3c-1.65-1.66-4.34-1.66-6 0zm-4-4l2 2c2.76-2.76 7.24-2.76 10 0l2-2c-3.86-3.86-10.13-3.86-14 0z"/>
            </svg>
        </div>
        
        <!-- Additional Colorful Icons -->
        <div class="floating-icon" style="top: 55%; left: 40%; animation-delay: 7s; animation-duration: 16s;">
            <svg class="w-14 h-14 text-pink-500/40" fill="currentColor" viewBox="0 0 24 24">
                <path d="M2,6C2,4.89 2.9,4 4,4H20A2,2 0 0,1 22,6V18A2,2 0 0,1 20,20H4C2.89,20 2,19.1 2,18V6M4,6V18H20V6H4M6,9H18V11H6V9M6,13H16V15H6V13Z" />
            </svg>
        </div>
        <div class="floating-icon" style="top: 10%; right: 35%; animation-delay: 8s; animation-duration: 18s;">
            <svg class="w-12 h-12 text-yellow-500/40" fill="currentColor" viewBox="0 0 24 24">
                <path d="M7,2V4H8V18A4,4 0 0,0 12,22A4,4 0 0,0 16,18V4H17V2H7M11,16C10.4,16 10,15.6 10,15C10,14.4 10.4,14 11,14C11.6,14 12,14.4 12,15C12,15.6 11.6,16 11,16M13,12C12.4,12 12,11.6 12,11C12,10.4 12.4,10 13,10C13.6,10 14,10.4 14,11C14,11.6 13.6,12 13,12M14,7H10V4H14V7Z" />
            </svg>
        </div>
        <div class="floating-icon" style="top: 85%; right: 45%; animation-delay: 2.5s; animation-duration: 15s;">
            <svg class="w-10 h-10 text-teal-500/40" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8,2H16A2,2 0 0,1 18,4V20A2,2 0 0,1 16,22H8A2,2 0 0,1 6,20V4A2,2 0 0,1 8,2M8,4V6H16V4H8M16,8H8V10H16V8M16,12H8V14H16V12M16,16H8V18H16V16M16,20H8V22H16V20Z" />
            </svg>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <div class="inline-block px-3 py-1 bg-red-900/30 rounded-full text-red-500 text-sm font-semibold mb-4 scroll-fade-up">OUR SERVICES</div>
            <h2 class="text-3xl sm:text-5xl font-extrabold text-white mb-4 scroll-fade-up delay-200">
                Professional Repair Services
            </h2>
            <p class="mt-4 text-xl text-gray-400 max-w-3xl mx-auto scroll-fade-up delay-300">
                Expert solutions for all your device repair needs with quality parts and guaranteed workmanship
            </p>
        </div>

        <div class="mt-16 relative scroll-fade-in delay-400">
            <!-- Carousel Container -->
            <div class="relative overflow-hidden rounded-2xl">
                <div class="flex transition-transform duration-700 ease-in-out" 
                     :style="{ transform: `translateX(-${activeSlide * 100}%)` }">
                    
                    <!-- Service 1: Computer Service -->
                    <div class="w-full flex-shrink-0">
                        <div class="max-w-4xl mx-auto px-4">
                            <div class="bg-gradient-to-br from-gray-900 to-black rounded-2xl p-8 md:p-12 shadow-2xl border border-red-800 hover:border-red-600 transition-all duration-300">
                                <div class="flex flex-col md:flex-row items-center gap-12">
                                    <div class="w-full md:w-2/5">
                                        <div class="aspect-square bg-gradient-to-br from-black to-gray-900 rounded-xl p-8 flex items-center justify-center border border-red-800/50 shadow-lg transform hover:scale-105 transition-all duration-300">
                                            <svg class="w-40 h-40 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="w-full md:w-3/5 text-center md:text-left">
                                        <span class="text-red-500 text-sm font-semibold tracking-wider uppercase">Hardware & Software</span>
                                        <h3 class="text-3xl font-bold text-white mb-6 mt-2">Computer Services</h3>
                                        <p class="text-gray-400 mb-6 text-lg">Comprehensive computer repair and maintenance services with certified technicians and genuine parts.</p>
                                        <ul class="space-y-4 text-gray-300 mb-8">
                                            <li class="flex items-center gap-3">
                                                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-red-600/20 text-red-500">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <span>Hardware Diagnostics</span>
                                            </li>
                                            <li class="flex items-center gap-3">
                                                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-red-600/20 text-red-500">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <span>Virus Removal</span>
                                            </li>
                                            <li class="flex items-center gap-3">
                                                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-red-600/20 text-red-500">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <span>System Optimization</span>
                                            </li>
                                        </ul>
                                        <button @click="openServiceModal('computer')" 
                                                class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white text-lg font-medium rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-gray-900">
                                            View Details
                                            <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Service 2: Mobile Services -->
                    <div class="w-full flex-shrink-0">
                        <div class="max-w-4xl mx-auto px-4">
                            <div class="bg-gradient-to-br from-gray-900 to-black rounded-2xl p-8 md:p-12 shadow-2xl border border-red-800 hover:border-red-600 transition-all duration-300">
                                <div class="flex flex-col md:flex-row items-center gap-12">
                                    <div class="w-full md:w-2/5">
                                        <div class="aspect-square bg-gradient-to-br from-black to-gray-900 rounded-xl p-8 flex items-center justify-center border border-red-800/50 shadow-lg transform hover:scale-105 transition-all duration-300">
                                            <svg class="w-40 h-40 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="w-full md:w-3/5 text-center md:text-left">
                                        <span class="text-red-500 text-sm font-semibold tracking-wider uppercase">Smartphones & Tablets</span>
                                        <h3 class="text-3xl font-bold text-white mb-6 mt-2">Mobile Services</h3>
                                        <p class="text-gray-400 mb-6 text-lg">Expert repair solutions for all major mobile brands with quality parts and prompt service.</p>
                                        <ul class="space-y-4 text-gray-300 mb-8">
                                            <li class="flex items-center gap-3">
                                                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-red-600/20 text-red-500">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <span>Screen Replacement</span>
                                            </li>
                                            <li class="flex items-center gap-3">
                                                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-red-600/20 text-red-500">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <span>Battery Replacement</span>
                                            </li>
                                            <li class="flex items-center gap-3">
                                                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-red-600/20 text-red-500">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <span>Bypass Services</span>
                                            </li>
                                        </ul>
                                        <button @click="openServiceModal('mobile')" 
                                                class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white text-lg font-medium rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-gray-900">
                                            View Details
                                            <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Service 3: Laptop Services -->
                    <div class="w-full flex-shrink-0">
                        <div class="max-w-4xl mx-auto px-4">
                            <div class="bg-gradient-to-br from-gray-900 to-black rounded-2xl p-8 md:p-12 shadow-2xl border border-red-800 hover:border-red-600 transition-all duration-300">
                                <div class="flex flex-col md:flex-row items-center gap-12">
                                    <div class="w-full md:w-2/5">
                                        <div class="aspect-square bg-gradient-to-br from-black to-gray-900 rounded-xl p-8 flex items-center justify-center border border-red-800/50 shadow-lg transform hover:scale-105 transition-all duration-300">
                                            <svg class="w-40 h-40 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17H7C4.79086 17 3 15.2091 3 13V8C3 5.79086 4.79086 4 7 4H17C19.2091 4 21 5.79086 21 8V13C21 15.2091 19.2091 17 17 17H15M9 17V21M9 17H15M15 17V21"/>
                                                <rect x="7" y="7" width="10" height="7" rx="1" stroke-width="1.5"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="w-full md:w-3/5 text-center md:text-left">
                                        <span class="text-red-500 text-sm font-semibold tracking-wider uppercase">All Models & Brands</span>
                                        <h3 class="text-3xl font-bold text-white mb-6 mt-2">Laptop Services</h3>
                                        <p class="text-gray-400 mb-6 text-lg">Comprehensive laptop repair and upgrade services to maximize performance and extend lifespan.</p>
                                        <ul class="space-y-4 text-gray-300 mb-8">
                                            <li class="flex items-center gap-3">
                                                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-red-600/20 text-red-500">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <span>Hardware Upgrades</span>
                                            </li>
                                            <li class="flex items-center gap-3">
                                                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-red-600/20 text-red-500">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <span>Screen Replacement</span>
                                            </li>
                                            <li class="flex items-center gap-3">
                                                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-red-600/20 text-red-500">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <span>Data Recovery</span>
                                            </li>
                                        </ul>
                                        <button @click="openServiceModal('laptop')" 
                                                class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white text-lg font-medium rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-gray-900">
                                            View Details
                                            <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Service 4: Printer Services -->
                    <div class="w-full flex-shrink-0">
                        <div class="max-w-4xl mx-auto px-4">
                            <div class="bg-gradient-to-br from-gray-900 to-black rounded-2xl p-8 md:p-12 shadow-2xl border border-red-800 hover:border-red-600 transition-all duration-300">
                                <div class="flex flex-col md:flex-row items-center gap-12">
                                    <div class="w-full md:w-2/5">
                                        <div class="aspect-square bg-gradient-to-br from-black to-gray-900 rounded-xl p-8 flex items-center justify-center border border-red-800/50 shadow-lg transform hover:scale-105 transition-all duration-300">
                                            <svg class="w-40 h-40 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="w-full md:w-3/5 text-center md:text-left">
                                        <span class="text-red-500 text-sm font-semibold tracking-wider uppercase">Home & Office</span>
                                        <h3 class="text-3xl font-bold text-white mb-6 mt-2">Printer Services</h3>
                                        <p class="text-gray-400 mb-6 text-lg">Professional printer repair and maintenance services to ensure optimal printing quality and performance.</p>
                                        <ul class="space-y-4 text-gray-300 mb-8">
                                            <li class="flex items-center gap-3">
                                                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-red-600/20 text-red-500">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <span>Printer Repair</span>
                                            </li>
                                            <li class="flex items-center gap-3">
                                                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-red-600/20 text-red-500">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <span>Ink System Maintenance</span>
                                            </li>
                                            <li class="flex items-center gap-3">
                                                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-red-600/20 text-red-500">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <span>Network Configuration</span>
                                            </li>
                                        </ul>
                                        <button @click="openServiceModal('printer')" 
                                                class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white text-lg font-medium rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-gray-900">
                                            View Details
                                            <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Service 5: Website Development -->
                    <div class="w-full flex-shrink-0">
                        <div class="max-w-4xl mx-auto px-4">
                            <div class="bg-gradient-to-br from-gray-900 to-black rounded-2xl p-8 md:p-12 shadow-2xl border border-red-800 hover:border-red-600 transition-all duration-300">
                                <div class="flex flex-col md:flex-row items-center gap-12">
                                    <div class="w-full md:w-2/5">
                                        <div class="aspect-square bg-gradient-to-br from-black to-gray-900 rounded-xl p-8 flex items-center justify-center border border-red-800/50 shadow-lg transform hover:scale-105 transition-all duration-300">
                                            <svg class="w-40 h-40 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="w-full md:w-3/5 text-center md:text-left">
                                        <span class="text-red-500 text-sm font-semibold tracking-wider uppercase">Digital Presence</span>
                                        <h3 class="text-3xl font-bold text-white mb-6 mt-2">Website Development</h3>
                                        <p class="text-gray-400 mb-6 text-lg">Professional website design and development services to establish your online presence and grow your business.</p>
                                        <ul class="space-y-4 text-gray-300 mb-8">
                                            <li class="flex items-center gap-3">
                                                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-red-600/20 text-red-500">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <span>Responsive Design</span>
                                            </li>
                                            <li class="flex items-center gap-3">
                                                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-red-600/20 text-red-500">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <span>Custom Web Applications</span>
                                            </li>
                                            <li class="flex items-center gap-3">
                                                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-red-600/20 text-red-500">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <span>SEO Optimization</span>
                                            </li>
                                        </ul>
                                        <button @click="openServiceModal('website')" 
                                                class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white text-lg font-medium rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-gray-900">
                                            View Details
                                            <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carousel Controls -->
            <button @click="prevSlide" 
                    class="absolute -left-4 md:left-4 top-1/2 -translate-y-1/2 bg-black p-3 rounded-full shadow-lg hover:bg-gray-900 focus:outline-none border border-red-800 group hover:scale-110 transition-all duration-300 z-10">
                <svg class="w-6 h-6 text-red-500 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button @click="nextSlide" 
                    class="absolute -right-4 md:right-4 top-1/2 -translate-y-1/2 bg-black p-3 rounded-full shadow-lg hover:bg-gray-900 focus:outline-none border border-red-800 group hover:scale-110 transition-all duration-300 z-10">
                <svg class="w-6 h-6 text-red-500 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <!-- Carousel Indicators -->
            <div class="flex justify-center mt-12 gap-4">
                <template x-for="(_, index) in [0,1,2,3,4]" :key="index">
                    <button @click="goToSlide(index)"
                            :class="activeSlide === index ? 'w-12 bg-red-600' : 'w-3 bg-gray-600 hover:bg-gray-500'"
                            class="h-3 rounded-full transition-all duration-500 focus:outline-none">
                    </button>
                </template>
            </div>
        </div>

        <!-- Stats -->
        <div class="mt-20 max-w-5xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center scroll-scale-up delay-100">
                    <div class="text-4xl font-bold text-red-500 mb-2">190+</div>
                    <div class="text-gray-400">Happy Customers</div>
                </div>
                <div class="text-center scroll-scale-up delay-300">
                    <div class="text-4xl font-bold text-red-500 mb-2">4.9</div>
                    <div class="text-gray-400">Customer Rating</div>
                </div>
                <div class="text-center scroll-scale-up delay-500">
                    <div class="text-4xl font-bold text-red-500 mb-2">90%</div>
                    <div class="text-gray-400">Same-Day Service</div>
                </div>
                <div class="text-center scroll-scale-up delay-700">
                    <div class="text-4xl font-bold text-red-500 mb-2">97%</div>
                    <div class="text-gray-400">Service Success Rate</div>
                </div>
            </div>
        </div>
    </div>
</section>