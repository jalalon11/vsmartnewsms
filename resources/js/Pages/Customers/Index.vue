<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CustomerModal from '@/Components/CustomerModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    customers: Object,
    filters: Object,
});

// Modal states
const showCustomerModal = ref(false);
const showDeleteModal = ref(false);
const editingCustomer = ref(null);
const deletingCustomer = ref(null);
const isDeleting = ref(false);

// Search and filter states
const searchQuery = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || 'all');
const sortBy = ref(props.filters?.sort || 'name');
const sortDirection = ref(props.filters?.direction || 'asc');

// Computed properties
const filteredCustomers = computed(() => {
    if (!props.customers?.data) return [];

    let filtered = [...props.customers.data];

    // Apply search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(customer =>
            customer.full_name?.toLowerCase().includes(query) ||
            customer.facebook_link?.toLowerCase().includes(query) ||
            customer.phone?.toLowerCase().includes(query)
        );
    }

    return filtered;
});

// Watch for search changes and debounce
let searchTimeout;
watch(searchQuery, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        updateFilters();
    }, 300);
});

// Functions
const openCreateModal = () => {
    editingCustomer.value = null;
    showCustomerModal.value = true;
};

const openEditModal = (customer) => {
    editingCustomer.value = customer;
    showCustomerModal.value = true;
};

const openDeleteModal = (customer) => {
    deletingCustomer.value = customer;
    showDeleteModal.value = true;
};

const handleCustomerSaved = () => {
    showCustomerModal.value = false;
    router.reload({ only: ['customers'] });
};

const handleDeleteConfirm = () => {
    if (!deletingCustomer.value) return;

    isDeleting.value = true;
    router.delete(route('customers.destroy', deletingCustomer.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            deletingCustomer.value = null;
            isDeleting.value = false;
        },
        onError: () => {
            isDeleting.value = false;
        },
    });
};

const handleDeleteCancel = () => {
    showDeleteModal.value = false;
    deletingCustomer.value = null;
    isDeleting.value = false;
};

const updateFilters = () => {
    const params = {
        search: searchQuery.value || undefined,
        status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
        sort: sortBy.value !== 'name' ? sortBy.value : undefined,
        direction: sortDirection.value !== 'asc' ? sortDirection.value : undefined,
    };

    router.get(route('customers.index'), params, {
        preserveState: true,
        replace: true,
    });
};

const exportCustomers = () => {
    // TODO: Implement export functionality
    console.log('Exporting customers...');
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = 'all';
    sortBy.value = 'name';
    sortDirection.value = 'asc';
    updateFilters();
};
</script>

<template>
    <Head title="Customers" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-1">
                        Customer Management
                    </h2>
                    <p class="text-gray-400 text-sm">Manage your customer database and relationships</p>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">
                    <!-- Search Bar -->
                    <div class="relative flex-1 max-w-md">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search customers..."
                            class="w-full bg-gray-800 border border-gray-700 text-white placeholder-gray-400 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors duration-200"
                        >
                        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <button
                            v-if="searchQuery"
                            @click="searchQuery = ''"
                            class="absolute right-3 top-2.5 text-gray-400 hover:text-white"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>



                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2">
                        <button
                            @click="exportCustomers"
                            class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Export</span>
                        </button>

                        <button
                            @click="clearFilters"
                            v-if="searchQuery || statusFilter !== 'all'"
                            class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200"
                        >
                            Clear
                        </button>

                        <button
                            @click="openCreateModal"
                            class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-6 py-2 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl flex items-center space-x-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span>Add Customer</span>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <div class="p-6 space-y-6">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Total Customers</p>
                            <p class="text-2xl font-bold text-white">{{ customers.total || 0 }}</p>
                        </div>
                        <div class="p-2 bg-blue-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Active This Month</p>
                            <p class="text-2xl font-bold text-white">{{ Math.floor((customers.total || 0) * 0.7) }}</p>
                        </div>
                        <div class="p-2 bg-green-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">New This Week</p>
                            <p class="text-2xl font-bold text-white">{{ Math.floor((customers.total || 0) * 0.1) }}</p>
                        </div>
                        <div class="p-2 bg-purple-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Avg. Devices</p>
                            <p class="text-2xl font-bold text-white">2.3</p>
                        </div>
                        <div class="p-2 bg-orange-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customers Table -->
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-red-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Customer Directory</h3>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                                </svg>
                            </button>
                            <button class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Devices</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Pending</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">In Progress</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Completed</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-900 divide-y divide-gray-700">
                            <tr v-for="customer in filteredCustomers" :key="customer.id" class="hover:bg-gray-800 transition-colors duration-200 group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center mr-4 shadow-lg group-hover:shadow-xl transition-shadow duration-200">
                                            <span class="text-sm font-bold text-white">{{ customer.first_name?.charAt(0) }}{{ customer.last_name?.charAt(0) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-white group-hover:text-red-300 transition-colors duration-200">{{ customer.full_name }}</div>
                                            <div class="text-xs text-gray-400">Customer ID: #{{ String(customer.id).padStart(4, '0') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-300">
                                        <a :href="customer.facebook_link" target="_blank" class="text-blue-400 hover:text-blue-300 transition-colors duration-200">
                                            Facebook Profile
                                        </a>
                                    </div>
                                    <div class="text-sm text-gray-400">{{ customer.phone }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-300">{{ customer.city || 'N/A' }}, {{ customer.state || 'N/A' }}</div>
                                    <div class="text-xs text-gray-400">{{ customer.zip_code || 'No ZIP' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 shadow-sm">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                            {{ customer.devices_count || 0 }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 shadow-sm">
                                        {{ customer.pending_orders_count || 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 shadow-sm">
                                        {{ customer.in_progress_orders_count || 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 shadow-sm">
                                        {{ customer.completed_orders_count || 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <Link
                                            :href="route('customers.show', customer.id)"
                                            class="p-2 text-blue-400 hover:text-blue-300 hover:bg-blue-400 hover:bg-opacity-10 rounded-lg transition-all duration-200"
                                            title="View Customer"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </Link>
                                        <button
                                            @click="openEditModal(customer)"
                                            class="p-2 text-yellow-400 hover:text-yellow-300 hover:bg-yellow-400 hover:bg-opacity-10 rounded-lg transition-all duration-200"
                                            title="Edit Customer"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            @click="openDeleteModal(customer)"
                                            class="p-2 text-red-400 hover:text-red-300 hover:bg-red-400 hover:bg-opacity-10 rounded-lg transition-all duration-200"
                                            title="Delete Customer"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredCustomers.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-white mb-2">
                                            {{ searchQuery ? 'No customers found' : 'No customers yet' }}
                                        </h3>
                                        <p class="text-gray-400 text-sm mb-4">
                                            {{ searchQuery ? `No customers match "${searchQuery}"` : 'Get started by adding your first customer' }}
                                        </p>
                                        <button
                                            v-if="!searchQuery"
                                            @click="openCreateModal"
                                            class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-6 py-2 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl flex items-center space-x-2"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            <span>Add your first customer</span>
                                        </button>
                                        <button
                                            v-else
                                            @click="clearFilters"
                                            class="text-red-400 hover:text-red-300 text-sm font-medium"
                                        >
                                            Clear search and show all customers →
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="customers.links" class="bg-gray-800 px-4 py-3 border-t border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="customers.prev_page_url" :href="customers.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-800 hover:bg-gray-700">
                                Previous
                            </Link>
                            <Link v-if="customers.next_page_url" :href="customers.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-800 hover:bg-gray-700">
                                Next
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-400">
                                    Showing {{ customers.from }} to {{ customers.to }} of {{ customers.total }} results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <template v-for="link in customers.links" :key="link.label">
                                        <Link v-if="link.url" :href="link.url"
                                              class="relative inline-flex items-center px-2 py-2 border text-sm font-medium"
                                              :class="{
                                                  'z-10 bg-red-600 border-red-600 text-white': link.active,
                                                  'bg-gray-800 border-gray-600 text-gray-300 hover:bg-gray-700': !link.active
                                              }"
                                              v-html="link.label">
                                        </Link>
                                        <span v-else
                                              class="relative inline-flex items-center px-2 py-2 border text-sm font-medium cursor-not-allowed opacity-50 bg-gray-800 border-gray-600 text-gray-500"
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

        <!-- Customer Modal -->
        <CustomerModal
            :show="showCustomerModal"
            :customer="editingCustomer"
            @close="showCustomerModal = false"
            @saved="handleCustomerSaved"
        />

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal
            :show="showDeleteModal"
            :processing="isDeleting"
            title="Delete Customer"
            :message="`Are you sure you want to delete ${deletingCustomer?.full_name}? This action cannot be undone and will also delete all associated devices and repair orders.`"
            confirm-text="Delete Customer"
            cancel-text="Cancel"
            type="danger"
            @confirm="handleDeleteConfirm"
            @cancel="handleDeleteCancel"
        />
    </AuthenticatedLayout>
</template>
