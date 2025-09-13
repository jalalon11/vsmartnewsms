<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    feedback: Object,
});

const getRatingStars = (rating) => {
    const stars = [];
    for (let i = 1; i <= 5; i++) {
        stars.push(i <= rating);
    }
    return stars;
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Feedback Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-200 leading-tight">
                    Feedback Details
                </h2>
                <Link
                    :href="route('feedback.index')"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Feedback
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- Compact Header Section -->
                    <div class="p-4 border-b border-gray-700">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <!-- Customer Info -->
                            <div>
                                <h3 class="text-lg font-bold text-white">{{ feedback.name }}</h3>
                                <p class="text-sm text-gray-400">{{ feedback.email }}</p>
                            </div>
                            
                            <!-- Rating & Status -->
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center">
                                    <template v-for="(filled, index) in getRatingStars(feedback.rating)" :key="index">
                                        <svg
                                            class="w-4 h-4"
                                            :class="filled ? 'text-yellow-400' : 'text-gray-600'"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </template>
                                    <span class="ml-2 text-sm font-medium text-gray-300">{{ feedback.rating }}/5</span>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                        :class="feedback.is_featured ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                    >
                                        {{ feedback.is_featured ? 'Featured' : 'Regular' }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ feedback.service_type || 'General' }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Date -->
                            <div class="text-right">
                                <p class="text-xs text-gray-400">Submitted</p>
                                <p class="text-sm font-medium text-white">{{ formatDate(feedback.created_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Compact Message Section -->
                    <div class="p-4">
                        <h4 class="text-sm font-semibold text-white mb-2">Message</h4>
                        <div class="bg-gray-700 rounded-lg p-3">
                            <p class="text-sm text-gray-300 leading-relaxed whitespace-pre-wrap">{{ feedback.message }}</p>
                        </div>
                    </div>

                    <!-- Compact User Information -->
                    <div v-if="feedback.user" class="p-4 border-t border-gray-700">
                        <h4 class="text-sm font-semibold text-white mb-2">User Account</h4>
                        <div class="bg-gray-700 rounded-lg p-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                                <div>
                                    <span class="text-gray-400">Name:</span> <span class="text-white">{{ feedback.user.name }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400">Email:</span> <span class="text-white">{{ feedback.user.email }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400">ID:</span> <span class="text-white font-mono text-xs">{{ feedback.user.id }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400">Joined:</span> <span class="text-white">{{ formatDate(feedback.user.created_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Compact Metadata Section -->
                    <div class="p-4 border-t border-gray-700 bg-gray-750">
                        <h4 class="text-sm font-semibold text-white mb-2">Details</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                            <div>
                                <span class="text-gray-400">ID:</span>
                                <p class="text-white font-mono">{{ feedback.id }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Category:</span>
                                <p class="text-white">{{ feedback.service_type || 'General' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Created:</span>
                                <p class="text-white">{{ new Date(feedback.created_at).toLocaleDateString() }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Updated:</span>
                                <p class="text-white">{{ new Date(feedback.updated_at).toLocaleDateString() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Compact Action Buttons -->
                    <div class="p-3 border-t border-gray-700 flex justify-between items-center">
                        <Link
                            :href="route('feedback.index')"
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm transition-colors duration-200 flex items-center gap-2"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back
                        </Link>
                        
                        <button
                            @click="window.print()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm transition-colors duration-200 flex items-center gap-2"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@media print {
    .no-print {
        display: none !important;
    }
    
    .bg-gray-800,
    .bg-gray-700,
    .bg-gray-750 {
        background-color: white !important;
        color: black !important;
    }
    
    .text-white,
    .text-gray-300,
    .text-gray-400 {
        color: black !important;
    }
    
    .border-gray-700 {
        border-color: #ccc !important;
    }
}
</style>