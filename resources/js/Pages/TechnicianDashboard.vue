<script setup>
import TechnicianLayout from '@/Layouts/TechnicianLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    stats: Object,
    assignedOrders: Array,
    recentCompleted: Array,
    performanceStats: Object,
    technician: Object,
    isAdminView: {
        type: Boolean,
        default: false
    },
});

const updateOrderStatus = (orderId, newStatus) => {
    router.patch(route('repair-orders.update-status', orderId), {
        status: newStatus
    }, {
        preserveScroll: true,
        onSuccess: () => {
            router.reload({ only: ['assignedOrders', 'stats', 'recentCompleted'] });
        }
    });
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
</script>

<template>
    <Head title="Technician Dashboard" />

    <TechnicianLayout :isAdminView="isAdminView">
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-lg font-bold text-white">{{ technician.user?.name?.charAt(0) }}</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">
                            Welcome back, {{ technician.user?.name }}
                            <span v-if="isAdminView" class="text-sm text-blue-300 ml-2">(Admin View)</span>
                        </h2>
                        <p class="text-gray-400 text-sm">{{ technician.specialization }} • Employee ID: {{ technician.employee_id }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <Link
                        v-if="isAdminView"
                        :href="route('dashboard')"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Admin
                    </Link>
                    <div class="text-right">
                        <p class="text-sm text-gray-400">Today</p>
                        <p class="text-lg font-semibold text-white">{{ new Date().toLocaleDateString() }}</p>
                    </div>
                </div>
            </div>
        </template>

        <div class="p-6 space-y-8">
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Active Orders</p>
                            <p class="text-3xl font-bold text-white">{{ stats.assigned_orders }}</p>
                        </div>
                        <div class="p-3 bg-blue-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Completed Today</p>
                            <p class="text-3xl font-bold text-white">{{ stats.completed_today }}</p>
                        </div>
                        <div class="p-3 bg-green-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Total Completed</p>
                            <p class="text-3xl font-bold text-white">{{ stats.total_completed }}</p>
                        </div>
                        <div class="p-3 bg-purple-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Pending Orders</p>
                            <p class="text-3xl font-bold text-white">{{ stats.pending_orders }}</p>
                        </div>
                        <div class="p-3 bg-yellow-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Assigned Orders -->
                <div class="lg:col-span-2">
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl">
                        <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-700 rounded-t-xl">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-blue-600 rounded-lg">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-white">My Assigned Orders</h3>
                                </div>
                                <span class="text-sm text-gray-400">{{ assignedOrders.length }} active</span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div v-if="assignedOrders.length > 0" class="space-y-4">
                                <div v-for="order in assignedOrders" :key="order.id" class="bg-gray-800 rounded-lg p-4 border border-gray-700 hover:border-gray-600 transition-colors duration-200">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <Link :href="route('repair-orders.show', order.id)" class="text-lg font-semibold text-white hover:text-red-300 transition-colors duration-200">
                                                    {{ order.order_number }}
                                                </Link>
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full border" :class="getPriorityColor(order.priority)">
                                                    {{ order.priority.toUpperCase() }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-300 mb-1">{{ order.customer?.full_name }}</p>
                                            <p class="text-sm text-gray-400">{{ order.device?.brand }} {{ order.device?.model }}</p>
                                            <p class="text-xs text-gray-500 mt-2">{{ order.service?.name }}</p>
                                        </div>
                                        <div class="flex flex-col items-end space-y-2">
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full border" :class="getStatusColor(order.status)">
                                                {{ order.status.replace('_', ' ').toUpperCase() }}
                                            </span>
                                            <div class="flex space-x-1">
                                                <button
                                                    v-if="order.status === 'pending'"
                                                    @click="updateOrderStatus(order.id, 'in_progress')"
                                                    class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg transition-colors duration-200"
                                                >
                                                    Start Work
                                                </button>
                                                <button
                                                    v-if="order.status === 'in_progress'"
                                                    @click="updateOrderStatus(order.id, 'completed')"
                                                    class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded-lg transition-colors duration-200"
                                                >
                                                    Complete
                                                </button>
                                                <button
                                                    v-if="order.status === 'in_progress'"
                                                    @click="updateOrderStatus(order.id, 'waiting_parts')"
                                                    class="px-3 py-1 bg-orange-600 hover:bg-orange-700 text-white text-xs rounded-lg transition-colors duration-200"
                                                >
                                                    Wait Parts
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Created: {{ formatDateTime(order.created_at) }}
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-gray-400 text-sm">No active orders assigned</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Performance Stats -->
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="p-2 bg-purple-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Performance (30 Days)</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Orders Completed</span>
                                <span class="text-xl font-bold text-white">{{ performanceStats.orders_completed }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Avg. Completion</span>
                                <span class="text-xl font-bold text-white">
                                    {{ performanceStats.avg_completion_time ? Math.round(performanceStats.avg_completion_time) + ' days' : 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Completed -->
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl">
                        <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-700 rounded-t-xl">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-green-600 rounded-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-white">Recently Completed</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <div v-if="recentCompleted.length > 0" class="space-y-3">
                                <div v-for="order in recentCompleted" :key="order.id" class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                                    <Link :href="route('repair-orders.show', order.id)" class="text-sm font-medium text-white hover:text-red-300 transition-colors duration-200">
                                        {{ order.order_number }}
                                    </Link>
                                    <p class="text-xs text-gray-400 mt-1">{{ order.customer?.full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ formatDate(order.actual_completion) }}</p>
                                </div>
                            </div>
                            <div v-else class="text-center py-4">
                                <p class="text-gray-400 text-sm">No completed orders yet</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </TechnicianLayout>
</template>
