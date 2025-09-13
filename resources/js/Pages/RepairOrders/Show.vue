<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RepairOrderModal from '@/Components/RepairOrderModal.vue';
import CancelOrderModal from '@/Components/CancelOrderModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    repairOrder: Object,
    customers: Array,
    devices: Array,
    services: Array,
    technicians: Array,
});

// Modal states
const showEditModal = ref(false);
const showCancelModal = ref(false);

// Functions
const openEditModal = () => {
    showEditModal.value = true;
};

const handleOrderSaved = () => {
    showEditModal.value = false;
    router.reload({ only: ['repairOrder'] });
};

const updateStatus = (newStatus) => {
    router.patch(route('repair-orders.update-status', props.repairOrder.id), {
        status: newStatus
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Show success notification
            if (window.toast) {
                window.toast.success('Order status updated successfully!');
            }
            router.reload({ only: ['repairOrder'] });
        },
        onError: (errors) => {
            console.error('Status update failed:', errors);
            // Show error notification
            if (window.toast) {
                window.toast.error('Failed to update order status. Please try again.');
            }
        },
    });
};

const markDelivered = () => {
    router.patch(route('repair-orders.mark-delivered', props.repairOrder.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Show success notification
            if (window.toast) {
                window.toast.success('Order marked as delivered successfully!');
            }
            router.reload({ only: ['repairOrder'] });
        },
        onError: (errors) => {
            console.error('Mark delivered failed:', errors);
            // Show error notification
            if (window.toast) {
                window.toast.error('Failed to mark order as delivered. Please try again.');
            }
        },
    });
};

const cancelOrder = (data) => {
    router.patch(route('repair-orders.cancel', props.repairOrder.id), {
        cancellation_reason: data.reason,
        restore_parts: data.restore_parts
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Show success notification
            if (window.toast) {
                window.toast.success('Order cancelled successfully!');
            }
            showCancelModal.value = false;
            router.reload({ only: ['repairOrder'] });
        },
        onError: (errors) => {
            console.error('Cancel order failed:', errors);
            // Show error notification
            if (window.toast) {
                window.toast.error('Failed to cancel order. Please try again.');
            }
        },
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

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 2
    }).format(amount || 0);
};
</script>

<template>
    <Head :title="`${repairOrder.order_number} - Repair Order Details`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <Link :href="route('repair-orders.index')" class="text-gray-400 hover:text-white transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <h2 class="text-2xl font-bold text-white">
                            {{ repairOrder.order_number }}
                        </h2>
                        <p class="text-gray-400 text-sm">Repair Order Details</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <button 
                        @click="openEditModal"
                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Edit</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="p-6 space-y-8">
            <!-- Status and Priority Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Status</p>
                            <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full border mt-2" :class="getStatusColor(repairOrder.status)">
                                {{ repairOrder.status.replace('_', ' ').toUpperCase() }}
                            </span>
                        </div>
                        <div class="p-3 bg-blue-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Priority</p>
                            <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full border mt-2" :class="getPriorityColor(repairOrder.priority)">
                                {{ repairOrder.priority.toUpperCase() }}
                            </span>
                        </div>
                        <div class="p-3 bg-orange-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Total Cost</p>
                            <p class="text-2xl font-bold text-white">{{ formatCurrency(repairOrder.total_cost) }}</p>
                        </div>
                        <div class="p-3 bg-green-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Created</p>
                            <p class="text-lg font-bold text-white">{{ formatDate(repairOrder.created_at) }}</p>
                        </div>
                        <div class="p-3 bg-purple-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Order Information -->
                <div class="lg:col-span-2">
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl">
                        <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-700 rounded-t-xl">
                            <h3 class="text-lg font-semibold text-white">Order Information</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="text-sm font-medium text-gray-400">Customer</label>
                                    <p class="text-white font-medium">{{ repairOrder.customer?.full_name }}</p>
                                    <p class="text-sm text-gray-400">{{ repairOrder.customer?.phone }}</p>
                                </div>
                                
                                <div>
                                    <label class="text-sm font-medium text-gray-400">Device</label>
                                    <p class="text-white font-medium">{{ repairOrder.device?.brand }} {{ repairOrder.device?.model }}</p>
                                    <p class="text-sm text-gray-400">{{ repairOrder.device?.device_type?.name }}</p>
                                </div>
                                
                                <div>
                                    <label class="text-sm font-medium text-gray-400">Services</label>
                                    <div v-if="repairOrder.services && repairOrder.services.length > 0">
                                        <div v-for="service in repairOrder.services" :key="service.id" class="mb-2">
                                            <p class="text-white font-medium">{{ service.name }}</p>
                                            <p class="text-sm text-gray-400">{{ formatCurrency(service.pivot?.service_price || service.base_price) }}</p>
                                        </div>
                                    </div>
                                    <div v-else-if="repairOrder.service">
                                        <p class="text-white font-medium">{{ repairOrder.service.name }}</p>
                                        <p class="text-sm text-gray-400">{{ formatCurrency(repairOrder.service.base_price) }}</p>
                                    </div>
                                    <div v-else>
                                        <p class="text-gray-500">No services assigned</p>
                                    </div>
                                </div>
                                
                                <div v-if="repairOrder.technician">
                                    <label class="text-sm font-medium text-gray-400">Assigned Technician</label>
                                    <p class="text-white font-medium">{{ repairOrder.technician?.user?.name }}</p>
                                    <p class="text-sm text-gray-400">{{ repairOrder.technician?.specialization }}</p>
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-400 mb-2 block">Issue Description</label>
                                <p class="text-white bg-gray-800 rounded-lg p-4">{{ repairOrder.issue_description }}</p>
                            </div>

                            <div v-if="repairOrder.customer_notes">
                                <label class="text-sm font-medium text-gray-400 mb-2 block">Customer Notes</label>
                                <p class="text-white bg-gray-800 rounded-lg p-4">{{ repairOrder.customer_notes }}</p>
                            </div>

                            <div v-if="repairOrder.diagnosis">
                                <label class="text-sm font-medium text-gray-400 mb-2 block">Diagnosis</label>
                                <p class="text-white bg-gray-800 rounded-lg p-4">{{ repairOrder.diagnosis }}</p>
                            </div>

                            <div v-if="repairOrder.solution">
                                <label class="text-sm font-medium text-gray-400 mb-2 block">Solution</label>
                                <p class="text-white bg-gray-800 rounded-lg p-4">{{ repairOrder.solution }}</p>
                            </div>

                            <div v-if="repairOrder.internal_notes">
                                <label class="text-sm font-medium text-gray-400 mb-2 block">Internal Notes</label>
                                <p class="text-white bg-gray-800 rounded-lg p-4">{{ repairOrder.internal_notes }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Actions & Timeline -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Quick Status Updates -->
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl">
                        <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-700 rounded-t-xl">
                            <h3 class="text-lg font-semibold text-white">Quick Actions</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <button
                                v-if="repairOrder.status === 'pending'"
                                @click="updateStatus('in_progress')"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m2-10V7a2 2 0 01-2 2H5a2 2 0 01-2-2V4a2 2 0 012-2h14a2 2 0 012 2z" />
                                </svg>
                                <span>Start Work</span>
                            </button>
                            
                            <button
                                v-if="repairOrder.status === 'in_progress'"
                                @click="updateStatus('waiting_parts')"
                                class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Wait for Parts</span>
                            </button>
                            
                            <button
                                v-if="repairOrder.status === 'in_progress'"
                                @click="updateStatus('completed')"
                                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Mark Complete</span>
                            </button>

                            <button
                                v-if="repairOrder.status === 'waiting_parts'"
                                @click="updateStatus('in_progress')"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span>Resume Work</span>
                            </button>

                            <button
                                v-if="repairOrder.status === 'completed' || repairOrder.status === 'delivered'"
                                @click="repairOrder.status === 'completed' ? markDelivered() : null"
                                :disabled="repairOrder.status === 'delivered'"
                                :class="[
                                    'w-full px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2',
                                    repairOrder.status === 'delivered'
                                        ? 'bg-gray-600 text-gray-400 cursor-not-allowed'
                                        : 'bg-purple-600 hover:bg-purple-700 text-white'
                                ]"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <span>{{ repairOrder.status === 'delivered' ? 'Delivered' : 'Mark Delivered' }}</span>
                            </button>

                            <!-- Cancel Button -->
                            <button
                                v-if="['pending', 'in_progress', 'waiting_parts'].includes(repairOrder.status)"
                                @click="showCancelModal = true"
                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span>Cancel Order</span>
                            </button>
                        </div>
                    </div>

                    <!-- Cost Breakdown -->
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl">
                        <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-700 rounded-t-xl">
                            <h3 class="text-lg font-semibold text-white">Cost Breakdown</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Labor Cost</span>
                                <span class="text-white font-medium">{{ formatCurrency(repairOrder.labor_cost) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Parts Cost</span>
                                <span class="text-white font-medium">{{ formatCurrency(repairOrder.parts_cost) }}</span>
                            </div>
                            <div class="border-t border-gray-700 pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-white">Total</span>
                                    <span class="text-lg font-bold text-white">{{ formatCurrency(repairOrder.total_cost) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl">
                        <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-700 rounded-t-xl">
                            <h3 class="text-lg font-semibold text-white">Timeline</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <div>
                                    <p class="text-white text-sm">Order Created</p>
                                    <p class="text-gray-400 text-xs">{{ formatDateTime(repairOrder.created_at) }}</p>
                                </div>
                            </div>
                            <div v-if="repairOrder.estimated_completion" class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                <div>
                                    <p class="text-white text-sm">Estimated Completion</p>
                                    <p class="text-gray-400 text-xs">{{ formatDateTime(repairOrder.estimated_completion) }}</p>
                                </div>
                            </div>
                            <div v-if="repairOrder.actual_completion" class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <div>
                                    <p class="text-white text-sm">Completed</p>
                                    <p class="text-gray-400 text-xs">{{ formatDateTime(repairOrder.actual_completion) }}</p>
                                </div>
                            </div>
                            <div v-if="repairOrder.cancelled_at" class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                <div>
                                    <p class="text-white text-sm">Cancelled</p>
                                    <p class="text-gray-400 text-xs">{{ formatDateTime(repairOrder.cancelled_at) }}</p>
                                    <p v-if="repairOrder.cancellation_reason" class="text-gray-500 text-xs mt-1">
                                        Reason: {{ repairOrder.cancellation_reason }}
                                    </p>
                                </div>
                            </div>
                            <div v-if="repairOrder.delivered_at" class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                                <div>
                                    <p class="text-white text-sm">Delivered</p>
                                    <p class="text-gray-400 text-xs">{{ formatDateTime(repairOrder.delivered_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <RepairOrderModal
            :show="showEditModal"
            :repair-order="repairOrder"
            :customers="customers"
            :devices="devices"
            :services="services"
            :technicians="technicians"
            @close="showEditModal = false"
            @saved="handleOrderSaved"
        />

        <CancelOrderModal
            :show="showCancelModal"
            :repair-order="repairOrder"
            @close="showCancelModal = false"
            @cancel="cancelOrder"
        />
    </AuthenticatedLayout>
</template>
