<!-- Hero Banner Component -->
<div class="relative overflow-hidden min-h-screen">
    <!-- Particle Background Canvas -->
    <canvas id="c" class="absolute inset-0 w-full h-full"></canvas>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 min-h-screen flex flex-col justify-center">
        <div class="relative py-16 sm:py-24">
            <!-- Hero Content -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left Column - Text Content -->
                <div class="text-center lg:text-left">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight scroll-fade-up">
                        <span class="block">Professional</span>
                        <span class="block text-red-500">IT Solutions & Services</span>
                    </h1>
                    <p class="mt-6 text-xl sm:text-2xl text-gray-300 max-w-2xl mx-auto lg:mx-0 scroll-fade-up" style="animation-delay: 100ms;">
                            Expert technical services for all your devices. Fast, reliable, and professional solutions you can trust.
                    </p>
                    <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start scroll-fade-up" style="animation-delay: 200ms;">
                        <a href="#services"
                           class="inline-flex items-center justify-center px-8 py-4 text-base font-medium rounded-xl text-white bg-red-600 hover:bg-red-700 transition duration-300 transform hover:scale-105">
                            Our Services
                            <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                    <!-- Trust Badges -->
                    <div class="mt-12 grid grid-cols-2 sm:grid-cols-4 gap-6 items-center justify-center lg:justify-start scroll-fade-up" style="animation-delay: 300ms;">
                        <div class="flex items-center justify-center lg:justify-start space-x-2 text-gray-300">
                            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Certified</span>
                        </div>
                        <div class="flex items-center justify-center lg:justify-start space-x-2 text-gray-300">
                            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                            </svg>
                            <span>Guaranteed</span>
                        </div>
                        <div class="flex items-center justify-center lg:justify-start space-x-2 text-gray-300">
                            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                            </svg>
                            <span>Expert Team</span>
                        </div>
                        <div class="flex items-center justify-center lg:justify-start space-x-2 text-gray-300">
                            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            <span>Fast Service</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Image -->
                <div class="relative lg:ml-10 hidden md:block">
                    <div class="relative w-full max-w-xl mx-auto">
                        <div class="absolute top-0 -left-4 w-80 h-80 bg-red-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
                        <div class="absolute top-0 -right-4 w-80 h-80 bg-gray-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
                        <div class="absolute -bottom-8 left-20 w-80 h-80 bg-red-400 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
                        <div class="relative">
                            <img class="relative rounded-lg shadow-2xl w-full h-auto" src="{{ asset('images/services/hero-services.svg') }}" alt="IT Services and Repairs">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="mt-16 pt-12 border-t border-gray-600 border-opacity-20">
                <dl class="grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-4">
                    <div class="text-center">
                        <dt class="text-4xl font-extrabold text-white">227+</dt>
                        <dd class="mt-2 text-red-400">Services Completed</dd>
                    </div>
                    <div class="text-center">
                        <dt class="text-4xl font-extrabold text-white">1</dt>
                        <dd class="mt-2 text-red-400">Year/s Experience</dd>
                    </div>
                    <div class="text-center">
                        <dt class="text-4xl font-extrabold text-white">1Hr</dt>
                        <dd class="mt-2 text-red-400">Average Service Time</dd>
                    </div>
                    <div class="text-center">
                        <dt class="text-4xl font-extrabold text-white">14Days</dt>
                        <dd class="mt-2 text-red-400">Service Warranty</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0px, 0px) scale(1); }
}
.animate-blob {
    animation: blob 7s infinite;
}
.animation-delay-2000 {
    animation-delay: 2s;
}
.animation-delay-4000 {
    animation-delay: 4s;
}
</style>