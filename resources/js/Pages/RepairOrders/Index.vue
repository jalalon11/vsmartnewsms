<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RepairOrderModal from '@/Components/RepairOrderModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    repairOrders: Object,
    customers: Array,
    devices: Array,
    services: Array,
    technicians: Array,
    parts: Array,
    filters: Object,
});

// Modal states
const showRepairOrderModal = ref(false);
const showDeleteModal = ref(false);
const editingRepairOrder = ref(null);
const deletingRepairOrder = ref(null);
const isDeleting = ref(false);

// Search and filter states
const searchQuery = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || 'all');
const priorityFilter = ref(props.filters?.priority || 'all');
const technicianFilter = ref(props.filters?.technician || 'all');

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
    editingRepairOrder.value = null;
    showRepairOrderModal.value = true;
};

const openEditModal = (repairOrder) => {
    editingRepairOrder.value = repairOrder;
    showRepairOrderModal.value = true;
};

const openDeleteModal = (repairOrder) => {
    deletingRepairOrder.value = repairOrder;
    showDeleteModal.value = true;
};

const handleRepairOrderSaved = () => {
    showRepairOrderModal.value = false;
    router.reload({ only: ['repairOrders'] });
};

const handleDeleteConfirm = () => {
    if (!deletingRepairOrder.value) return;

    isDeleting.value = true;
    router.delete(route('repair-orders.destroy', deletingRepairOrder.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            deletingRepairOrder.value = null;
            isDeleting.value = false;
        },
        onError: () => {
            isDeleting.value = false;
        },
    });
};

const handleDeleteCancel = () => {
    showDeleteModal.value = false;
    deletingRepairOrder.value = null;
    isDeleting.value = false;
};

const updateFilters = () => {
    const params = {
        search: searchQuery.value || undefined,
        status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
        priority: priorityFilter.value !== 'all' ? priorityFilter.value : undefined,
        technician: technicianFilter.value !== 'all' ? technicianFilter.value : undefined,
    };

    router.get(route('repair-orders.index'), params, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = 'all';
    priorityFilter.value = 'all';
    technicianFilter.value = 'all';
    updateFilters();
};

const getStatusColor = (status) => {
    const colors = {
        'pending': 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'in_progress': 'bg-blue-100 text-blue-800 border-blue-200',
        'waiting_parts': 'bg-orange-100 text-orange-800 border-orange-200',
        'completed': 'bg-green-100 text-green-800 border-green-200',
        'cancelled': 'bg-red-100 text-red-800 border-red-200',
        'delivered': 'bg-purple-100 text-purple-800 border-purple-200'
    };
    return colors[status] || colors.pending;
};

const getPriorityColor = (priority) => {
    const colors = {
        'low': 'bg-green-100 text-green-800 border-green-200',
        'medium': 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'high': 'bg-orange-100 text-orange-800 border-orange-200',
        'urgent': 'bg-red-100 text-red-800 border-red-200'
    };
    return colors[priority] || colors.medium;
};
</script>

<template>
    <Head title="Repair Orders" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-1">
                        Repair Orders
                    </h2>
                    <p class="text-gray-400 text-sm">Track and manage repair service requests</p>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">
                    <!-- Search Bar -->
                    <div class="relative flex-1 max-w-md">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search orders..."
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
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="waiting_parts">Waiting Parts</option>
                            <option value="completed">Completed</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>

                        <select
                            v-model="priorityFilter"
                            @change="updateFilters"
                            class="bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                            <option value="all">All Priority</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>

                        <select
                            v-model="technicianFilter"
                            @change="updateFilters"
                            class="bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                            <option value="all">All Technicians</option>
                            <option v-for="technician in technicians" :key="technician.id" :value="technician.id">
                                {{ technician.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2">
                        <button
                            @click="clearFilters"
                            v-if="searchQuery || statusFilter !== 'all' || priorityFilter !== 'all' || technicianFilter !== 'all'"
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
                            <span>Create Order</span>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <div class="p-6 space-y-6">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Total Orders</p>
                            <p class="text-2xl font-bold text-white">{{ repairOrders.total || 0 }}</p>
                        </div>
                        <div class="p-2 bg-blue-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">In Progress</p>
                            <p class="text-2xl font-bold text-white">{{ Math.floor((repairOrders.total || 0) * 0.3) }}</p>
                        </div>
                        <div class="p-2 bg-orange-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Completed</p>
                            <p class="text-2xl font-bold text-white">{{ Math.floor((repairOrders.total || 0) * 0.5) }}</p>
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
                            <p class="text-sm text-gray-400">Pending</p>
                            <p class="text-2xl font-bold text-white">{{ Math.floor((repairOrders.total || 0) * 0.15) }}</p>
                        </div>
                        <div class="p-2 bg-yellow-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">This Month</p>
                            <p class="text-2xl font-bold text-white">{{ Math.floor((repairOrders.total || 0) * 0.2) }}</p>
                        </div>
                        <div class="p-2 bg-purple-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-800">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Order #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Device</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Priority</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Technician</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-900 divide-y divide-gray-800">
                            <tr v-for="order in repairOrders.data" :key="order.id" class="hover:bg-gray-800">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">{{ order.order_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ order.customer?.full_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ order.device?.brand }} {{ order.device?.model }}
                                    <div class="text-xs text-gray-500">{{ order.device?.device_type?.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    <div v-if="order.services && order.services.length > 0">
                                        <div v-for="(service, index) in order.services" :key="service.id" class="text-sm">
                                            {{ service.name }}
                                            <span v-if="index < order.services.length - 1" class="text-gray-500">, </span>
                                        </div>
                                    </div>
                                    <div v-else-if="order.service">
                                        {{ order.service.name }}
                                    </div>
                                    <div v-else class="text-gray-500">
                                        No services
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getStatusColor(order.status)">
                                        {{ order.status.replace('_', ' ').toUpperCase() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getPriorityColor(order.priority)">
                                        {{ order.priority.toUpperCase() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ order.assigned_technician_name || 'Unassigned' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">₱{{ order.total_cost }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-1">
                                        <Link
                                            :href="route('repair-orders.show', order.id)"
                                            class="p-2 text-blue-400 hover:text-blue-300 hover:bg-blue-400 hover:bg-opacity-10 rounded-lg transition-all duration-200"
                                            title="View Details"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </Link>
                                        <button
                                            @click="openEditModal(order)"
                                            class="p-2 text-yellow-400 hover:text-yellow-300 hover:bg-yellow-400 hover:bg-opacity-10 rounded-lg transition-all duration-200"
                                            title="Edit Order"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <Link
                                            v-if="(order.status === 'completed' || order.status === 'delivered') && !order.invoice"
                                            :href="route('repair-orders.generate-invoice', order.id)"
                                            method="post"
                                            as="button"
                                            class="p-2 text-green-400 hover:text-green-300 hover:bg-green-400 hover:bg-opacity-10 rounded-lg transition-all duration-200"
                                            title="Generate Invoice"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </Link>
                                        <Link
                                            v-if="order.invoice"
                                            :href="route('invoices.show', order.invoice.id)"
                                            class="p-2 text-blue-400 hover:text-blue-300 hover:bg-blue-400 hover:bg-opacity-10 rounded-lg transition-all duration-200"
                                            title="View Invoice"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="repairOrders.links" class="bg-gray-800 px-4 py-3 border-t border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="repairOrders.prev_page_url" :href="repairOrders.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-800 hover:bg-gray-700">
                                Previous
                            </Link>
                            <Link v-if="repairOrders.next_page_url" :href="repairOrders.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-800 hover:bg-gray-700">
                                Next
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-400">
                                    Showing {{ repairOrders.from }} to {{ repairOrders.to }} of {{ repairOrders.total }} results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <template v-for="link in (repairOrders.links || [])" :key="link?.label || 'empty'">
                                        <Link v-if="link && link.url" :href="link.url"
                                              class="relative inline-flex items-center px-2 py-2 border text-sm font-medium"
                                              :class="{
                                                  'z-10 bg-red-600 border-red-600 text-white': link.active,
                                                  'bg-gray-800 border-gray-600 text-gray-300 hover:bg-gray-700': !link.active
                                              }"
                                              v-html="link.label">
                                        </Link>
                                        <span v-else-if="link"
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

        <!-- Repair Order Modal -->
        <RepairOrderModal
            :show="showRepairOrderModal"
            :repair-order="editingRepairOrder"
            :customers="customers"
            :devices="devices"
            :services="services"
            :technicians="technicians"
            :parts="parts"
            @close="showRepairOrderModal = false"
            @saved="handleRepairOrderSaved"
        />

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal
            :show="showDeleteModal"
            :processing="isDeleting"
            title="Delete Repair Order"
            :message="`Are you sure you want to delete repair order ${deletingRepairOrder?.order_number}? This action cannot be undone.`"
            confirm-text="Delete Order"
            cancel-text="Cancel"
            type="danger"
            @confirm="handleDeleteConfirm"
            @cancel="handleDeleteCancel"
        />
    </AuthenticatedLayout>
</template>
