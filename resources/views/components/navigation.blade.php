<!-- Navigation Component -->
<nav class="fixed w-full z-50 bg-black border-b border-red-800" x-data="{ mobileMenuOpen: false }">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('landing') }}" class="flex items-center">
                        <img src="{{ asset('img/LogoClear.png') }}" alt="VSMART TUNE UP" class="h-10 w-auto mr-2">
                        <span class="text-white font-bold text-xl">V<span class="text-red-600 font-bold text-xl">SMART</span> TUNE UP</span>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="{{ route('landing') }}" 
                        class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium text-white hover:text-red-500 active:text-red-600 transition-all duration-200">
                        Home
                    </a>
                    <a href="{{ route('landing') }}#services" 
                        class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium text-white hover:text-red-500 active:text-red-600 transition-all duration-200">
                        Services
                    </a>
                    <a href="{{ route('landing') }}#testimonials" 
                        class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium text-white hover:text-red-500 active:text-red-600 transition-all duration-200">
                        Feedback
                    </a>
                    <a href="{{ route('about-us') }}" 
                        class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium text-white hover:text-red-500 active:text-red-600 transition-all duration-200 {{ request()->routeIs('about-us') ? 'border-b-2 border-red-500' : '' }}">
                        About Us
                    </a>
                </div>
            </div>

            <!-- Right Navigation -->
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
                <a href="{{ route('login') }}" class="nav-link inline-flex items-center px-4 py-2 text-sm font-medium text-white hover:text-red-500 active:text-red-600 transition-all duration-200">
                    Login
                </a>
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center sm:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                    class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-white hover:text-red-500 hover:bg-red-500/10 active:bg-red-500/20 focus:outline-none transition-all duration-200"
                    :class="{'bg-red-500/10': mobileMenuOpen}">
                    <svg class="h-6 w-6" :class="{'hidden': mobileMenuOpen, 'block': !mobileMenuOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="h-6 w-6" :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="sm:hidden" 
        x-show="mobileMenuOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        @click.away="mobileMenuOpen = false">
        <div class="pt-2 pb-3 space-y-1 bg-black/95 backdrop-blur-lg border-t border-red-800/20">
            <a href="{{ route('landing') }}" 
                class="mobile-menu-item block pl-3 pr-4 py-2 text-base font-medium text-white hover:text-red-500 hover:bg-red-500/10 active:bg-red-500/20 active:text-red-600 transition-all duration-200">
                Home
            </a>
            <a href="{{ route('landing') }}#services"
                class="mobile-menu-item block pl-3 pr-4 py-2 text-base font-medium text-white hover:text-red-500 hover:bg-red-500/10 active:bg-red-500/20 active:text-red-600 transition-all duration-200">
                Services
            </a>
            <a href="{{ route('landing') }}#testimonials"
                class="mobile-menu-item block pl-3 pr-4 py-2 text-base font-medium text-white hover:text-red-500 hover:bg-red-500/10 active:bg-red-500/20 active:text-red-600 transition-all duration-200">
                Feedback
            </a>
            <a href="{{ route('about-us') }}"
                class="mobile-menu-item block pl-3 pr-4 py-2 text-base font-medium text-white hover:text-red-500 hover:bg-red-500/10 active:bg-red-500/20 active:text-red-600 transition-all duration-200 {{ request()->routeIs('about-us') ? 'bg-red-500/10' : '' }}">
                About Us
            </a>
            <div class="mt-4 space-y-2 px-3">
                <a href="{{ route('login') }}"
                    class="mobile-menu-item block px-4 py-2 text-base font-medium text-white hover:text-red-500 hover:bg-red-500/10 active:bg-red-500/20 active:text-red-600 transition-all duration-200">
                    Login
                </a>
            </div>
        </div>
    </div>
</nav>