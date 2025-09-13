<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CustomerModal from '@/Components/CustomerModal.vue';
import DeviceModal from '@/Components/DeviceModal.vue';
import RepairOrderModal from '@/Components/RepairOrderModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    customer: Object,
});

// Modal states
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const showDeviceModal = ref(false);
const showRepairOrderModal = ref(false);
const isDeleting = ref(false);

// Computed properties
const recentRepairs = computed(() => {
    return props.customer.repair_orders?.slice(0, 5) || [];
});

const customerDevices = computed(() => {
    return props.customer.devices || [];
});

// Functions
const openEditModal = () => {
    showEditModal.value = true;
};

const openDeviceModal = () => {
    showDeviceModal.value = true;
};

const openRepairOrderModal = () => {
    showRepairOrderModal.value = true;
};

const openDeleteModal = () => {
    showDeleteModal.value = true;
};

const handleCustomerSaved = () => {
    showEditModal.value = false;
    router.reload({ only: ['customer'] });
};

const handleDeleteConfirm = () => {
    isDeleting.value = true;
    router.delete(route('customers.destroy', props.customer.id), {
        onSuccess: () => {
            router.visit(route('customers.index'));
        },
        onError: () => {
            isDeleting.value = false;
        },
    });
};

const handleDeleteCancel = () => {
    showDeleteModal.value = false;
    isDeleting.value = false;
};

const handleDeviceSaved = () => {
    showDeviceModal.value = false;
    router.reload({ only: ['customer'] });
};

const handleRepairOrderSaved = () => {
    showRepairOrderModal.value = false;
    router.reload({ only: ['customer'] });
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
</script>

<template>
    <Head :title="`${customer.full_name} - Customer Details`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-4">
                    <Link :href="route('customers.index')" class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-lg font-bold text-white">{{ customer.first_name?.charAt(0) }}{{ customer.last_name?.charAt(0) }}</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">{{ customer.full_name }}</h2>
                        <p class="text-gray-400 text-sm">Customer ID: #{{ String(customer.id).padStart(4, '0') }}</p>
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
                    <button
                        @click="openDeleteModal"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Delete</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="p-6 space-y-8">
            <!-- Customer Information -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Contact Information -->
                <div class="lg:col-span-2">
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-blue-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Contact Information</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Facebook Profile</label>
                                <div class="flex items-center space-x-2">
                                    <p class="text-white truncate">{{ customer.facebook_link }}</p>
                                    <a :href="customer.facebook_link" target="_blank" class="text-blue-400 hover:text-blue-300 flex-shrink-0">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Phone Number</label>
                                <div class="flex items-center space-x-2">
                                    <p class="text-white">{{ customer.phone }}</p>
                                    <a :href="`tel:${customer.phone}`" class="text-red-400 hover:text-red-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <div class="md:col-span-2" v-if="customer.address">
                                <label class="block text-sm font-medium text-gray-400 mb-1">Address</label>
                                <p class="text-white">
                                    {{ customer.address }}<br>
                                    {{ customer.city }}, {{ customer.state }} {{ customer.zip_code }}
                                </p>
                            </div>

                            <div class="md:col-span-2" v-if="customer.notes">
                                <label class="block text-sm font-medium text-gray-400 mb-1">Notes</label>
                                <p class="text-gray-300">{{ customer.notes }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="space-y-6">
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                        <h3 class="text-lg font-semibold text-white mb-4">Statistics</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Total Devices</span>
                                <span class="text-2xl font-bold text-white">{{ customerDevices.length }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Total Repairs</span>
                                <span class="text-2xl font-bold text-white">{{ customer.repair_orders?.length || 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Customer Since</span>
                                <span class="text-sm text-white">{{ new Date(customer.created_at).toLocaleDateString() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                        <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <button @click="openDeviceModal" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <span>Add Device</span>
                            </button>
                            <button @click="openRepairOrderModal" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Create Repair Order</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Devices and Repairs -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Customer Devices -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl">
                    <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-700 rounded-t-xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-purple-600 rounded-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-white">Registered Devices</h3>
                            </div>
                            <Link :href="route('devices.create', { customer_id: customer.id })" class="text-red-400 hover:text-red-300 text-sm font-medium">
                                Add Device →
                            </Link>
                        </div>
                    </div>
                    <div class="p-6">
                        <div v-if="customerDevices.length > 0" class="space-y-4">
                            <div v-for="device in customerDevices" :key="device.id" class="bg-gray-800 rounded-lg p-4 hover:bg-gray-750 transition-colors duration-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-white">{{ device.brand }} {{ device.model }}</h4>
                                        <p class="text-sm text-gray-400">{{ device.device_type?.name }}</p>
                                        <p class="text-xs text-gray-500" v-if="device.serial_number">S/N: {{ device.serial_number }}</p>
                                    </div>
                                    <Link :href="route('devices.show', device.id)" class="text-red-400 hover:text-red-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <p class="text-gray-400 text-sm">No devices registered</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Repairs -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl">
                    <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-700 rounded-t-xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-green-600 rounded-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-white">Recent Repairs</h3>
                            </div>
                            <Link :href="route('repair-orders.index', { customer: customer.id })" class="text-red-400 hover:text-red-300 text-sm font-medium">
                                View All →
                            </Link>
                        </div>
                    </div>
                    <div class="p-6">
                        <div v-if="recentRepairs.length > 0" class="space-y-4">
                            <div v-for="repair in recentRepairs" :key="repair.id" class="bg-gray-800 rounded-lg p-4 hover:bg-gray-750 transition-colors duration-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-white">{{ repair.order_number }}</span>
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full border" :class="getStatusColor(repair.status)">
                                        {{ repair.status.replace('_', ' ').toUpperCase() }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-400 mb-2">{{ repair.service?.name }}</p>
                                <p class="text-xs text-gray-500">{{ new Date(repair.created_at).toLocaleDateString() }}</p>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-gray-400 text-sm">No repair orders yet</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Customer Modal -->
        <CustomerModal
            :show="showEditModal"
            :customer="customer"
            @close="showEditModal = false"
            @saved="handleCustomerSaved"
        />

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal
            :show="showDeleteModal"
            :processing="isDeleting"
            title="Delete Customer"
            :message="`Are you sure you want to delete ${customer.full_name}? This action cannot be undone and will also delete all associated devices and repair orders.`"
            confirm-text="Delete Customer"
            cancel-text="Cancel"
            type="danger"
            @confirm="handleDeleteConfirm"
            @cancel="handleDeleteCancel"
        />

        <!-- Device Modal -->
        <DeviceModal
            :show="showDeviceModal"
            :customers="[customer]"
            :device-types="$page.props.deviceTypes || []"
            :preselected-customer="customer.id"
            @close="showDeviceModal = false"
            @saved="handleDeviceSaved"
        />

        <!-- Repair Order Modal -->
        <RepairOrderModal
            :show="showRepairOrderModal"
            :customers="[customer]"
            :devices="customer.devices || []"
            :services="$page.props.services || []"
            :technicians="$page.props.technicians || []"
            :parts="$page.props.parts || []"
            :preselected-customer="customer.id"
            @close="showRepairOrderModal = false"
            @saved="handleRepairOrderSaved"
        />
    </AuthenticatedLayout>
</template>
