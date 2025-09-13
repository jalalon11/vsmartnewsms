<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - VSMART TUNE UP</title>
    @vite(['resources/css/app.css'])
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-900 text-white">
    <!-- Navigation -->
    @include('components.navigation')

    <!-- About Us Content -->
    <div class="min-h-screen pt-20">
        <div class="container mx-auto px-4 py-16">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-4xl font-bold text-center mb-8">About <span class="text-red-600">VSMART TUNE UP</span></h1>
                
                <div class="prose prose-lg prose-invert mx-auto">
                    <p class="text-xl text-gray-300 text-center mb-12">
                        Your trusted partner for all hardware repair and technology solutions.
                    </p>
                    
                    <div class="grid md:grid-cols-2 gap-12 items-center mb-16">
                        <div>
                            <h2 class="text-2xl font-semibold mb-4 text-red-500">Our Story</h2>
                            <p class="text-gray-300 mb-4">
                                VSMART TUNE UP was founded with a simple mission: to provide reliable, affordable, and professional hardware repair services to our community. With years of experience in the technology industry, our team understands the importance of keeping your devices running smoothly.
                            </p>
                            <p class="text-gray-300">
                                We specialize in mobile phone repairs, computer diagnostics, laptop servicing, printer maintenance, and custom website development solutions.
                            </p>
                        </div>
                        <div class="text-center">
                            <img src="{{ asset('img/vsmart.png') }}" alt="VSMART TUNE UP" class="mx-auto rounded-lg shadow-lg">
                        </div>
                    </div>
                    
                    <div class="grid md:grid-cols-3 gap-8 mb-16">
                        <div class="text-center">
                            <div class="bg-red-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold mb-2">Quality Service</h3>
                            <p class="text-gray-400">We use only genuine parts and follow industry best practices for all repairs.</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="bg-red-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold mb-2">Fast Turnaround</h3>
                            <p class="text-gray-400">Most repairs completed within 24-48 hours with our efficient workflow.</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="bg-red-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2m-2-4h4m-4 0a1 1 0 00-1-1V9a2 2 0 012-2h1m-1 4a1 1 0 001 1v4a2 2 0 01-2 2H9a2 2 0 01-2-2v-4a1 1 0 001-1m2-4h4"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold mb-2">Affordable Pricing</h3>
                            <p class="text-gray-400">Competitive rates without compromising on quality or service excellence.</p>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <h2 class="text-2xl font-semibold mb-6 text-red-500">Ready to Get Started?</h2>
                        <p class="text-gray-300 mb-8">
                            Contact us today for a free consultation and let us help you get your devices back to perfect working condition.
                        </p>
                        <a href="{{ route('landing') }}#contact" class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-lg transition-colors">
                            Contact Us Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    @include('components.footer')
</body>
</html>