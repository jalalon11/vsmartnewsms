<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TechnicianModal from '@/Components/TechnicianModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    technicians: Object,
    specializations: Array,
    filters: Object,
});

// Modal states
const showTechnicianModal = ref(false);
const showDeleteModal = ref(false);
const editingTechnician = ref(null);
const deletingTechnician = ref(null);
const isDeleting = ref(false);

// Search and filter states
const searchQuery = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || 'all');
const specializationFilter = ref(props.filters?.specialization || 'all');
const sortBy = ref(props.filters?.sort || 'created_at');
const sortDirection = ref(props.filters?.direction || 'desc');

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
    editingTechnician.value = null;
    showTechnicianModal.value = true;
};

const openEditModal = (technician) => {
    editingTechnician.value = technician;
    showTechnicianModal.value = true;
};

const openDeleteModal = (technician) => {
    deletingTechnician.value = technician;
    showDeleteModal.value = true;
};

const handleTechnicianSaved = () => {
    showTechnicianModal.value = false;
    router.reload({ only: ['technicians'] });
};

const handleDeleteConfirm = () => {
    if (!deletingTechnician.value) return;

    isDeleting.value = true;
    router.patch(route('technicians.toggle-status', deletingTechnician.value.id), {}, {
        onSuccess: (response) => {
            // Show success notification
            if (window.toast) {
                window.toast.success(response.props?.flash?.success || 'Technician status updated successfully!');
            } else {
                alert(response.props?.flash?.success || 'Technician status updated successfully!');
            }
            showDeleteModal.value = false;
            deletingTechnician.value = null;
            isDeleting.value = false;
        },
        onError: (errors) => {
            // Show error notification
            if (window.toast) {
                window.toast.error(errors.error || 'Failed to update technician status. Please try again.');
            } else {
                alert(errors.error || 'Failed to update technician status. Please try again.');
            }
            isDeleting.value = false;
        },
    });
};

const handleDeleteCancel = () => {
    showDeleteModal.value = false;
    deletingTechnician.value = null;
    isDeleting.value = false;
};

const updateFilters = () => {
    const params = {
        search: searchQuery.value || undefined,
        status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
        specialization: specializationFilter.value !== 'all' ? specializationFilter.value : undefined,
        sort: sortBy.value !== 'created_at' ? sortBy.value : undefined,
        direction: sortDirection.value !== 'desc' ? sortDirection.value : undefined,
    };
    
    router.get(route('technicians.index'), params, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = 'all';
    specializationFilter.value = 'all';
    sortBy.value = 'created_at';
    sortDirection.value = 'desc';
    updateFilters();
};

const getStatusColor = (isActive) => {
    return isActive 
        ? 'bg-green-100 text-green-800 border-green-200'
        : 'bg-red-100 text-red-800 border-red-200';
};

const getWorkloadColor = (activeOrders) => {
    if (activeOrders === 0) return 'bg-gray-100 text-gray-800 border-gray-200';
    if (activeOrders <= 3) return 'bg-green-100 text-green-800 border-green-200';
    if (activeOrders <= 6) return 'bg-yellow-100 text-yellow-800 border-yellow-200';
    return 'bg-red-100 text-red-800 border-red-200';
};
</script>

<template>
    <Head title="Technicians" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-1">
                        Technician Management
                    </h2>
                    <p class="text-gray-400 text-sm">Manage repair technicians and their profiles</p>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">
                    <!-- Search Bar -->
                    <div class="relative flex-1 max-w-md">
                        <input 
                            v-model="searchQuery"
                            type="text" 
                            placeholder="Search technicians..." 
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

                    <!-- Filter Dropdowns -->
                    <div class="flex items-center space-x-2">
                        <select 
                            v-model="statusFilter"
                            @change="updateFilters"
                            class="bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>

                        <select 
                            v-model="specializationFilter"
                            @change="updateFilters"
                            class="bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                            <option value="all">All Specializations</option>
                            <option v-for="spec in specializations" :key="spec" :value="spec">
                                {{ spec }}
                            </option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2">
                        <button
                            @click="clearFilters"
                            v-if="searchQuery || statusFilter !== 'all' || specializationFilter !== 'all'"
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
                            <span>Add Technician</span>
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
                            <p class="text-sm text-gray-400">Total Technicians</p>
                            <p class="text-2xl font-bold text-white">{{ technicians.total || 0 }}</p>
                        </div>
                        <div class="p-2 bg-blue-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Active</p>
                            <p class="text-2xl font-bold text-white">{{ technicians.data?.filter(t => t.is_active).length || 0 }}</p>
                        </div>
                        <div class="p-2 bg-green-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Specializations</p>
                            <p class="text-2xl font-bold text-white">{{ specializations.length }}</p>
                        </div>
                        <div class="p-2 bg-purple-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Avg Workload</p>
                            <p class="text-2xl font-bold text-white">{{ Math.round((technicians.data?.reduce((sum, t) => sum + (t.active_orders_count || 0), 0) || 0) / Math.max(technicians.data?.length || 1, 1)) }}</p>
                        </div>
                        <div class="p-2 bg-orange-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technicians Table -->
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-red-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Technician Directory</h3>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Technician</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Employee ID</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Specialization</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Workload</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-900 divide-y divide-gray-700">
                            <tr v-for="technician in technicians.data" :key="technician.id" class="hover:bg-gray-800 transition-colors duration-200 group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-4 shadow-lg group-hover:shadow-xl transition-shadow duration-200">
                                            <span class="text-sm font-bold text-white">{{ technician.user?.name?.charAt(0) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-white group-hover:text-red-300 transition-colors duration-200">{{ technician.user?.name }}</div>
                                            <div class="text-xs text-gray-400">{{ technician.user?.email }}</div>
                                            <div class="text-xs text-gray-400" v-if="technician.phone">{{ technician.phone }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-white">{{ technician.employee_id }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 shadow-sm">
                                        {{ technician.specialization }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border" :class="getStatusColor(technician.is_active)">
                                        {{ technician.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border" :class="getWorkloadColor(technician.active_orders_count)">
                                        {{ technician.active_orders_count || 0 }} active
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <Link 
                                            :href="route('technicians.show', technician.id)" 
                                            class="p-2 text-blue-400 hover:text-blue-300 hover:bg-blue-400 hover:bg-opacity-10 rounded-lg transition-all duration-200"
                                            title="View Technician"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </Link>
                                        <button 
                                            @click="openEditModal(technician)"
                                            class="p-2 text-yellow-400 hover:text-yellow-300 hover:bg-yellow-400 hover:bg-opacity-10 rounded-lg transition-all duration-200"
                                            title="Edit Technician"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            @click="openDeleteModal(technician)"
                                            :class="[
                                                'p-2 rounded-lg transition-all duration-200',
                                                technician.is_active
                                                    ? 'text-red-400 hover:text-red-300 hover:bg-red-400 hover:bg-opacity-10'
                                                    : 'text-green-400 hover:text-green-300 hover:bg-green-400 hover:bg-opacity-10'
                                            ]"
                                            :title="technician.is_active ? 'Deactivate Technician' : 'Reactivate Technician'"
                                        >
                                            <svg v-if="technician.is_active" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                            </svg>
                                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="technicians.data?.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-white mb-2">
                                            {{ searchQuery ? 'No technicians found' : 'No technicians yet' }}
                                        </h3>
                                        <p class="text-gray-400 text-sm mb-4">
                                            {{ searchQuery ? `No technicians match "${searchQuery}"` : 'Get started by adding your first technician' }}
                                        </p>
                                        <button 
                                            v-if="!searchQuery"
                                            @click="openCreateModal"
                                            class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-6 py-2 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl flex items-center space-x-2"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            <span>Add your first technician</span>
                                        </button>
                                        <button 
                                            v-else
                                            @click="clearFilters"
                                            class="text-red-400 hover:text-red-300 text-sm font-medium"
                                        >
                                            Clear search and show all technicians →
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="technicians.links" class="bg-gray-800 px-4 py-3 border-t border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="technicians.prev_page_url" :href="technicians.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-800 hover:bg-gray-700">
                                Previous
                            </Link>
                            <Link v-if="technicians.next_page_url" :href="technicians.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-800 hover:bg-gray-700">
                                Next
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-400">
                                    Showing {{ technicians.from }} to {{ technicians.to }} of {{ technicians.total }} results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <template v-for="link in technicians.links" :key="link.label">
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

        <!-- Technician Modal -->
        <TechnicianModal
            :show="showTechnicianModal"
            :technician="editingTechnician"
            @close="showTechnicianModal = false"
            @saved="handleTechnicianSaved"
        />

        <!-- Status Toggle Confirmation Modal -->
        <ConfirmationModal
            :show="showDeleteModal"
            :processing="isDeleting"
            :title="deletingTechnician?.is_active ? 'Deactivate Technician' : 'Reactivate Technician'"
            :message="deletingTechnician?.is_active
                ? `Are you sure you want to deactivate ${deletingTechnician?.user?.name}? They will no longer be able to access the system, but their repair history will be preserved.`
                : `Are you sure you want to reactivate ${deletingTechnician?.user?.name}? They will regain access to the system and can be assigned new repair orders.`"
            :confirm-text="deletingTechnician?.is_active ? 'Deactivate' : 'Reactivate'"
            cancel-text="Cancel"
            :type="deletingTechnician?.is_active ? 'danger' : 'success'"
            @confirm="handleDeleteConfirm"
            @cancel="handleDeleteCancel"
        />
    </AuthenticatedLayout>
</template>
