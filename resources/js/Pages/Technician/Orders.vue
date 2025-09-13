<script setup>
import TechnicianLayout from '@/Layouts/TechnicianLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    repairOrders: Object,
    technician: Object,
    filters: Object,
    isAdminView: {
        type: Boolean,
        default: false
    },
});

// Search and filter states
const searchQuery = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || 'all');
const priorityFilter = ref(props.filters?.priority || 'all');

// Watch for search changes and debounce
let searchTimeout;
watch(searchQuery, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        updateFilters();
    }, 300);
});

// Functions
const updateOrderStatus = (orderId, newStatus) => {
    router.patch(route('repair-orders.update-status', orderId), {
        status: newStatus
    }, {
        preserveScroll: true,
        onSuccess: () => {
            router.reload({ only: ['repairOrders'] });
        }
    });
};

const updateFilters = () => {
    const params = {
        search: searchQuery.value || undefined,
        status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
        priority: priorityFilter.value !== 'all' ? priorityFilter.value : undefined,
    };
    
    router.get(route('technician.orders'), params, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = 'all';
    priorityFilter.value = 'all';
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

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString();
};

const formatDateTime = (dateString) => {
    return new Date(dateString).toLocaleString();
};

// Enhanced analytics computed properties
const orderStats = computed(() => {
    const orders = props.repairOrders.data || [];

    return {
        total: orders.length,
        pending: orders.filter(o => o.status === 'pending').length,
        inProgress: orders.filter(o => o.status === 'in_progress').length,
        waitingParts: orders.filter(o => o.status === 'waiting_parts').length,
        completed: orders.filter(o => o.status === 'completed').length,
        delivered: orders.filter(o => o.status === 'delivered').length,
        cancelled: orders.filter(o => o.status === 'cancelled').length,
        urgent: orders.filter(o => o.priority === 'urgent').length,
        high: orders.filter(o => o.priority === 'high').length,
        totalRevenue: orders
            .filter(o => ['completed', 'delivered'].includes(o.status))
            .reduce((sum, o) => sum + (o.total_cost || 0), 0),
        avgCompletionTime: calculateAvgCompletionTime(orders),
        completionRate: orders.length > 0 ?
            ((orders.filter(o => ['completed', 'delivered'].includes(o.status)).length / orders.length) * 100).toFixed(1) : 0
    };
});

const calculateAvgCompletionTime = (orders) => {
    const completedOrders = orders.filter(o =>
        ['completed', 'delivered'].includes(o.status) && o.actual_completion
    );

    if (completedOrders.length === 0) return 0;

    const totalDays = completedOrders.reduce((sum, order) => {
        const start = new Date(order.created_at);
        const end = new Date(order.actual_completion);
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return sum + diffDays;
    }, 0);

    return (totalDays / completedOrders.length).toFixed(1);
};

const getOrderProgress = (order) => {
    const statusProgress = {
        'pending': 10,
        'in_progress': 50,
        'waiting_parts': 70,
        'completed': 90,
        'delivered': 100,
        'cancelled': 0
    };
    return statusProgress[order.status] || 0;
};

const getTimeElapsed = (createdAt) => {
    const now = new Date();
    const created = new Date(createdAt);
    const diffTime = Math.abs(now - created);
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
    const diffHours = Math.floor((diffTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

    if (diffDays > 0) {
        return `${diffDays}d ${diffHours}h ago`;
    } else if (diffHours > 0) {
        return `${diffHours}h ago`;
    } else {
        const diffMinutes = Math.floor((diffTime % (1000 * 60 * 60)) / (1000 * 60));
        return `${diffMinutes}m ago`;
    }
};
</script>

<template>
    <Head title="My Orders" />

    <TechnicianLayout :isAdminView="isAdminView">
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <div class="flex items-center space-x-3">
                        <h2 class="text-2xl font-bold text-white mb-1">
                            My Repair Orders
                        </h2>
                        <span v-if="isAdminView" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                            Admin View
                        </span>
                    </div>
                    <p class="text-gray-400 text-sm">
                        {{ isAdminView ? 'Orders you created as admin' : 'Manage your assigned repair orders' }}
                    </p>
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
                    </div>

                    <!-- Clear Filters and Back to Admin -->
                    <div class="flex items-center space-x-2">
                        <button
                            @click="clearFilters"
                            v-if="searchQuery || statusFilter !== 'all' || priorityFilter !== 'all'"
                            class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200"
                        >
                            Clear
                        </button>

                        <Link
                            v-if="isAdminView"
                            :href="route('dashboard')"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            <span>Back to Admin</span>
                        </Link>
                    </div>
                </div>
            </div>
        </template>

        <div class="p-6 space-y-6">
            <!-- Enhanced Analytics Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Orders -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Total Orders</p>
                            <p class="text-2xl font-bold text-white">{{ orderStats.total }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ orderStats.completionRate }}% completion rate</p>
                        </div>
                        <div class="p-2 bg-blue-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Active Orders -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Active Orders</p>
                            <p class="text-2xl font-bold text-white">{{ orderStats.pending + orderStats.inProgress + orderStats.waitingParts }}</p>
                            <p class="text-xs text-red-400 mt-1" v-if="orderStats.urgent > 0">{{ orderStats.urgent }} urgent</p>
                            <p class="text-xs text-gray-500 mt-1" v-else>{{ orderStats.high }} high priority</p>
                        </div>
                        <div class="p-2 bg-orange-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Completed Orders -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Completed</p>
                            <p class="text-2xl font-bold text-white">{{ orderStats.completed + orderStats.delivered }}</p>
                            <p class="text-xs text-gray-500 mt-1">Avg: {{ orderStats.avgCompletionTime }} days</p>
                        </div>
                        <div class="p-2 bg-green-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Revenue -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Total Revenue</p>
                            <p class="text-2xl font-bold text-white">₱{{ orderStats.totalRevenue.toLocaleString() }}</p>
                            <p class="text-xs text-gray-500 mt-1">From completed orders</p>
                        </div>
                        <div class="p-2 bg-purple-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Breakdown -->
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-400">Pending</p>
                    <p class="text-lg font-bold text-yellow-400">{{ orderStats.pending }}</p>
                </div>
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-400">In Progress</p>
                    <p class="text-lg font-bold text-blue-400">{{ orderStats.inProgress }}</p>
                </div>
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-400">Waiting Parts</p>
                    <p class="text-lg font-bold text-orange-400">{{ orderStats.waitingParts }}</p>
                </div>
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-400">Completed</p>
                    <p class="text-lg font-bold text-green-400">{{ orderStats.completed }}</p>
                </div>
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-400">Delivered</p>
                    <p class="text-lg font-bold text-purple-400">{{ orderStats.delivered }}</p>
                </div>
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-400">Cancelled</p>
                    <p class="text-lg font-bold text-red-400">{{ orderStats.cancelled }}</p>
                </div>
            </div>

            <!-- Orders List -->
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-red-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Assigned Orders</h3>
                        </div>
                        <span class="text-sm text-gray-400">{{ repairOrders.data?.length || 0 }} orders</span>
                    </div>
                </div>
                <div class="p-6">
                    <div v-if="repairOrders.data?.length > 0" class="space-y-6">
                        <div v-for="order in repairOrders.data" :key="order.id" class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-gray-700 hover:border-gray-600 transition-all duration-200 shadow-lg">
                            <!-- Header with Progress Bar -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <Link :href="route('repair-orders.show', order.id)" class="text-xl font-semibold text-white hover:text-red-300 transition-colors duration-200">
                                            {{ order.order_number }}
                                        </Link>
                                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full border" :class="getPriorityColor(order.priority)">
                                            {{ order.priority.toUpperCase() }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full border" :class="getStatusColor(order.status)">
                                            {{ order.status.replace('_', ' ').toUpperCase() }}
                                        </span>
                                        <span class="text-xs text-gray-400">{{ getTimeElapsed(order.created_at) }}</span>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="mb-4">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs text-gray-400">Progress</span>
                                            <span class="text-xs text-gray-400">{{ getOrderProgress(order) }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-700 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-red-500 to-red-600 h-2 rounded-full transition-all duration-300"
                                                 :style="{ width: getOrderProgress(order) + '%' }"></div>
                                        </div>
                                    </div>

                                    <!-- Enhanced Information Grid -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div class="bg-gray-700 rounded-lg p-3">
                                            <p class="text-xs text-gray-400 mb-1">Customer</p>
                                            <p class="text-white font-medium">{{ order.customer?.full_name }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ order.customer?.phone }}</p>
                                        </div>
                                        <div class="bg-gray-700 rounded-lg p-3">
                                            <p class="text-xs text-gray-400 mb-1">Device</p>
                                            <p class="text-white font-medium">{{ order.device?.brand }} {{ order.device?.model }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ order.device?.device_type?.name }}</p>
                                        </div>
                                        <div class="bg-gray-700 rounded-lg p-3">
                                            <p class="text-xs text-gray-400 mb-1">Services</p>
                                            <div v-if="order.services && order.services.length > 0">
                                                <div v-for="(service, index) in order.services" :key="service.id">
                                                    <p class="text-white font-medium text-sm">{{ service.name }}</p>
                                                    <span v-if="index < order.services.length - 1" class="text-gray-500 text-xs">, </span>
                                                </div>
                                            </div>
                                            <div v-else-if="order.service">
                                                <p class="text-white font-medium">{{ order.service.name }}</p>
                                            </div>
                                            <div v-else>
                                                <p class="text-gray-500 text-sm">No services</p>
                                            </div>
                                            <p class="text-xs text-green-400 mt-1">₱{{ order.total_cost?.toLocaleString() }}</p>
                                        </div>
                                    </div>

                                    <!-- Time Information -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div class="bg-gray-700 rounded-lg p-3">
                                            <p class="text-xs text-gray-400 mb-1">Created</p>
                                            <p class="text-white text-sm">{{ formatDateTime(order.created_at) }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ getTimeElapsed(order.created_at) }}</p>
                                        </div>
                                        <div class="bg-gray-700 rounded-lg p-3" v-if="order.estimated_completion">
                                            <p class="text-xs text-gray-400 mb-1">Estimated Completion</p>
                                            <p class="text-white text-sm">{{ formatDate(order.estimated_completion) }}</p>
                                            <p class="text-xs" :class="new Date(order.estimated_completion) < new Date() ? 'text-red-400' : 'text-green-400'">
                                                {{ new Date(order.estimated_completion) < new Date() ? 'Overdue' : 'On Track' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <p class="text-sm text-gray-400 mb-2">Issue Description</p>
                                        <p class="text-gray-300 text-sm bg-gray-700 rounded-lg p-3">{{ order.issue_description }}</p>
                                    </div>

                                    <div v-if="order.customer_notes" class="mb-4">
                                        <p class="text-sm text-gray-400 mb-2">Customer Notes</p>
                                        <p class="text-gray-300 text-sm bg-gray-700 rounded-lg p-3">{{ order.customer_notes }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Action Buttons -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-700">
                                <div class="flex items-center space-x-2">
                                    <!-- <Link :href="route('repair-orders.show', order.id)"
                                          class="px-3 py-1.5 bg-gray-600 hover:bg-gray-500 text-white text-xs rounded-lg transition-colors duration-200 flex items-center space-x-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>View Details</span>
                                    </Link> -->
                                </div>
                                <div class="flex items-center space-x-3">
                                <button
                                    v-if="order.status === 'pending'"
                                    @click="updateOrderStatus(order.id, 'in_progress')"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors duration-200 flex items-center space-x-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m2-10V7a2 2 0 01-2 2H5a2 2 0 01-2-2V4a2 2 0 012-2h14a2 2 0 012 2z" />
                                    </svg>
                                    <span>Start Work</span>
                                </button>
                                
                                <button
                                    v-if="order.status === 'in_progress'"
                                    @click="updateOrderStatus(order.id, 'waiting_parts')"
                                    class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm rounded-lg transition-colors duration-200 flex items-center space-x-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Wait for Parts</span>
                                </button>
                                
                                <button
                                    v-if="order.status === 'in_progress'"
                                    @click="updateOrderStatus(order.id, 'completed')"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition-colors duration-200 flex items-center space-x-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Mark Complete</span>
                                </button>

                                <button
                                    v-if="order.status === 'waiting_parts'"
                                    @click="updateOrderStatus(order.id, 'in_progress')"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors duration-200 flex items-center space-x-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <span>Resume Work</span>
                                </button>

                                <!-- <Link
                                    :href="route('repair-orders.show', order.id)"
                                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded-lg transition-colors duration-200 flex items-center space-x-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>View Details</span>
                                </Link> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-12">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-white mb-2">
                                {{ searchQuery ? 'No orders found' : 'No orders assigned' }}
                            </h3>
                            <p class="text-gray-400 text-sm mb-4">
                                {{ searchQuery ? `No orders match "${searchQuery}"` : 'You have no repair orders assigned at the moment' }}
                            </p>
                            <button 
                                v-if="searchQuery"
                                @click="clearFilters"
                                class="text-red-400 hover:text-red-300 text-sm font-medium"
                            >
                                Clear search and show all orders →
                            </button>
                        </div>
                    </div>
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
                                    <template v-for="link in repairOrders.links" :key="link.label">
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
    </TechnicianLayout>
</template>
