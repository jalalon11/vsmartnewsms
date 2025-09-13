<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    feedback: Object,
    filters: Object,
});

const form = useForm({
    search: props.filters.search || '',
});

const search = () => {
    form.get(route('feedback.index'), {
        preserveState: true,
        replace: true,
    });
};

const clearSearch = () => {
    form.search = '';
    search();
};

const toggleFeatured = (id) => {
    router.patch(route('feedback.toggle-featured', id), {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const deleteFeedback = (id) => {
    if (confirm('Are you sure you want to delete this feedback?')) {
        router.delete(route('feedback.destroy', id), {
            preserveState: true,
            preserveScroll: true,
        });
    }
};

const getRatingStars = (rating) => {
    const stars = [];
    for (let i = 1; i <= 5; i++) {
        stars.push(i <= rating);
    }
    return stars;
};
</script>

<template>
    <Head title="Feedback Management" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-200 leading-tight">
                Feedback Management
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- Header with Search -->
                    <div class="p-6 border-b border-gray-700">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex-1">
                                <form @submit.prevent="search" class="flex gap-2">
                                    <input
                                        v-model="form.search"
                                        type="text"
                                        placeholder="Search feedback by name, email, or message..."
                                        class="flex-1 bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                    <button
                                        type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200"
                                    >
                                        Search
                                    </button>
                                    <button
                                        v-if="form.search"
                                        type="button"
                                        @click="clearSearch"
                                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200"
                                    >
                                        Clear
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Feedback Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Customer
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Rating
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Message
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Service Type
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                <tr v-for="item in feedback.data" :key="item.id" class="hover:bg-gray-700 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-white">{{ item.name }}</div>
                                        <div class="text-sm text-gray-400">{{ item.email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <template v-for="(filled, index) in getRatingStars(item.rating)" :key="index">
                                                <svg
                                                    class="w-4 h-4"
                                                    :class="filled ? 'text-yellow-400' : 'text-gray-600'"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </template>
                                            <span class="ml-2 text-sm text-gray-300">({{ item.rating }}/5)</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-300 max-w-xs truncate" :title="item.message">
                                            {{ item.message }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ item.service_type || 'General' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            :class="item.is_featured ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                        >
                                            {{ item.is_featured ? 'Featured' : 'Regular' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ new Date(item.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button
                                                @click="toggleFeatured(item.id)"
                                                class="transition-colors duration-200"
                                                :class="item.is_featured ? 'text-yellow-400 hover:text-yellow-300' : 'text-indigo-400 hover:text-indigo-300'"
                                                :title="item.is_featured ? 'Unfeature' : 'Feature'"
                                            >
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" :class="item.is_featured ? 'fill-current' : 'fill-none stroke-current'">
                                                    <path v-if="item.is_featured" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                </svg>
                                            </button>
                                            <Link
                                                :href="route('feedback.share', item.id)"
                                                class="text-blue-400 hover:text-blue-300 transition-colors duration-200"
                                                title="View Details"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </Link>
                                            <button
                                                @click="deleteFeedback(item.id)"
                                                class="text-red-400 hover:text-red-300 transition-colors duration-200"
                                                title="Delete"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div v-if="!feedback.data.length" class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10m0 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2m0 0v10a2 2 0 002 2h6a2 2 0 002-2V8M9 12h6" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-300">No feedback found</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ form.search ? 'Try adjusting your search criteria.' : 'No feedback has been submitted yet.' }}
                        </p>
                    </div>

                    <!-- Pagination -->
                    <div v-if="feedback.links" class="bg-gray-800 px-4 py-3 border-t border-gray-700 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link v-if="feedback.prev_page_url" :href="feedback.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-800 hover:bg-gray-700">
                                    Previous
                                </Link>
                                <Link v-if="feedback.next_page_url" :href="feedback.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-800 hover:bg-gray-700">
                                    Next
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-400">
                                        Showing {{ feedback.from }} to {{ feedback.to }} of {{ feedback.total }} results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <template v-for="link in feedback.links" :key="link.label">
                                            <Link v-if="link.url" :href="link.url"
                                                  class="relative inline-flex items-center px-2 py-2 border text-sm font-medium"
                                                  :class="{
                                                      'z-10 bg-blue-600 border-blue-600 text-white': link.active,
                                                      'bg-gray-800 border-gray-600 text-gray-300 hover:bg-gray-700': !link.active
                                                  }"
                                                  v-html="link.label">
                                            </Link>
                                            <span v-else
                                                  class="relative inline-flex items-center px-2 py-2 border border-gray-600 bg-gray-800 text-gray-500 text-sm font-medium"
                                                  v-html="link.label">
                                            </span>
                                        </template>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Custom animations for status highlighting */
.status-featured {
    animation: pulse-green 2s infinite;
}

@keyframes pulse-green {
    0%, 100% {
        background-color: rgb(34, 197, 94);
    }
    50% {
        background-color: rgb(22, 163, 74);
    }
}
</style>