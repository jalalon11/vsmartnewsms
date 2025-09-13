<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black flex items-center justify-center px-4">
        <Head title="Registration Disabled - Hardware Repair System" />

        <div class="w-full max-w-md">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <div class="mx-auto w-16 h-16 bg-gradient-to-r from-red-600 to-red-700 rounded-xl flex items-center justify-center mb-4 shadow-lg">
                    <ApplicationLogo class="w-8 h-8 text-white" />
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Registration Disabled</h1>
                <p class="text-gray-400">New user registration is currently unavailable</p>
            </div>

            <!-- Blocked Message Card -->
            <div class="bg-gray-800 rounded-2xl shadow-2xl border border-gray-700 p-8">
                <div class="text-center">
                    <!-- Warning Icon -->
                    <div class="mx-auto w-16 h-16 bg-red-600/20 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    
                    <h2 class="text-xl font-semibold text-white mb-4">Registration Currently Disabled</h2>
                    
                    <div class="space-y-4 text-gray-300">
                        <p class="leading-relaxed">
                            New user registration has been temporarily disabled by the system administrator.
                        </p>
                        
                        <div class="bg-gray-700/50 rounded-lg p-4 border border-gray-600">
                            <p class="text-sm text-gray-400 mb-2">
                                <strong class="text-gray-300">What you can do:</strong>
                            </p>
                            <ul class="text-sm text-gray-400 space-y-1 text-left">
                                <li>• Contact the system administrator for access</li>
                                <li>• Check back later as registration may be re-enabled</li>
                                <li>• Use existing credentials if you already have an account</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 space-y-3">
                <Link
                    :href="route('login')"
                    class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    <span>Sign In Instead</span>
                </Link>
                
                <Link
                    :href="route('dashboard')"
                    class="w-full bg-gray-700 hover:bg-gray-600 text-white font-medium py-3 px-4 rounded-lg transition-all duration-200 border border-gray-600 hover:border-gray-500 flex items-center justify-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011 1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Back to Dashboard</span>
                </Link>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-gray-500 text-xs">
                    <span class="block mb-1">Powered by VSMART TUNE UP</span>
                    <a href="https://web.facebook.com/vinzTSV" target="_blank" title="wrench icons" class="text-gray-400 hover:text-gray-300 transition-colors duration-200">
                        Visit Us on Facebook
                    </a>
                </p>
            </div>
        </div>
    </div>
</template>