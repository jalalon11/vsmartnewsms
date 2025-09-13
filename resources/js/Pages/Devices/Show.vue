<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeviceModal from '@/Components/DeviceModal.vue';
import RepairOrderModal from '@/Components/RepairOrderModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    device: Object,
    customers: Array,
    deviceTypes: Array,
    services: Array,
    technicians: Array,
    parts: Array,
});

// Modal states
const showEditModal = ref(false);
const showRepairOrderModal = ref(false);
const showDeleteModal = ref(false);
const isDeleting = ref(false);

// Computed properties
const recentRepairs = computed(() => {
    return props.device.repair_orders?.slice(0, 5) || [];
});

// Functions
const openEditModal = () => {
    showEditModal.value = true;
};

const openRepairOrderModal = () => {
    showRepairOrderModal.value = true;
};

const openDeleteModal = () => {
    showDeleteModal.value = true;
};

const handleDeviceSaved = () => {
    showEditModal.value = false;
    router.reload({ only: ['device'] });
};

const handleRepairOrderSaved = () => {
    showRepairOrderModal.value = false;
    router.reload({ only: ['device'] });
};

const handleDeleteConfirm = () => {
    isDeleting.value = true;
    router.delete(route('devices.destroy', props.device.id), {
        onSuccess: () => {
            router.visit(route('devices.index'));
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

const getDeviceIcon = (deviceType) => {
    const icons = {
        'Smartphone': 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',
        'Laptop': 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'Desktop': 'M9 17v-2m3 2v-4m3 4v-6m2 10H4a2 2 0 01-2-2V5a2 2 0 012-2h16a2 2 0 012 2v14a2 2 0 01-2 2z',
        'Printer': 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z',
        'Tablet': 'M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-7.172a2 2 0 00-1.414.586L3 12z'
    };
    return icons[deviceType] || icons['Smartphone'];
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
    <Head :title="`${device.brand} ${device.model} - Device Details`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-4">
                    <Link :href="route('devices.index')" class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getDeviceIcon(device.device_type?.name)" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">{{ device.brand }} {{ device.model }}</h2>
                        <p class="text-gray-400 text-sm">{{ device.device_type?.name }} • Registered {{ new Date(device.created_at).toLocaleDateString() }}</p>
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
            <!-- Device Information -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Device Details -->
                <div class="lg:col-span-2">
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-purple-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Device Information</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Brand & Model</label>
                                <p class="text-white text-lg font-semibold">{{ device.brand }} {{ device.model }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Device Type</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ device.device_type?.name }}
                                </span>
                            </div>

                            <div v-if="device.serial_number">
                                <label class="block text-sm font-medium text-gray-400 mb-1">Serial Number</label>
                                <p class="text-white font-mono">{{ device.serial_number }}</p>
                            </div>

                            <div v-if="device.imei">
                                <label class="block text-sm font-medium text-gray-400 mb-1">IMEI</label>
                                <p class="text-white font-mono">{{ device.imei }}</p>
                            </div>

                            <div v-if="device.year">
                                <label class="block text-sm font-medium text-gray-400 mb-1">Year</label>
                                <p class="text-white">{{ device.year }}</p>
                            </div>

                            <div v-if="device.color">
                                <label class="block text-sm font-medium text-gray-400 mb-1">Color</label>
                                <p class="text-white">{{ device.color }}</p>
                            </div>

                            <div v-if="device.specifications" class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-400 mb-1">Specifications</label>
                                <p class="text-gray-300">{{ device.specifications }}</p>
                            </div>

                            <div v-if="device.accessories" class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-400 mb-1">Accessories</label>
                                <p class="text-gray-300">{{ device.accessories }}</p>
                            </div>

                            <div v-if="device.condition_notes" class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-400 mb-1">Condition Notes</label>
                                <p class="text-gray-300">{{ device.condition_notes }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Owner & Statistics -->
                <div class="space-y-6">
                    <!-- Owner Information -->
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="p-2 bg-blue-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Owner</h3>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-lg">
                                <span class="text-sm font-bold text-white">{{ device.customer?.first_name?.charAt(0) }}{{ device.customer?.last_name?.charAt(0) }}</span>
                            </div>
                            <div>
                                <Link :href="route('customers.show', device.customer?.id)" class="text-lg font-semibold text-white hover:text-red-300 transition-colors duration-200">
                                    {{ device.customer?.full_name }}
                                </Link>
                                <p class="text-sm text-gray-400">{{ device.customer?.email }}</p>
                                <p class="text-sm text-gray-400">{{ device.customer?.phone }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                        <h3 class="text-lg font-semibold text-white mb-4">Statistics</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Total Repairs</span>
                                <span class="text-2xl font-bold text-white">{{ device.repair_orders?.length || 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Registered</span>
                                <span class="text-sm text-white">{{ new Date(device.created_at).toLocaleDateString() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Last Updated</span>
                                <span class="text-sm text-white">{{ new Date(device.updated_at).toLocaleDateString() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                        <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <button @click="openRepairOrderModal" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <span>Create Repair Order</span>
                            </button>
                            <Link :href="route('customers.show', device.customer?.id)" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>View Customer</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Repair History -->
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl">
                <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-700 rounded-t-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-green-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Repair History</h3>
                        </div>
                        <Link :href="route('repair-orders.index', { device: device.id })" class="text-red-400 hover:text-red-300 text-sm font-medium">
                            View All →
                        </Link>
                    </div>
                </div>
                <div class="p-6">
                    <div v-if="recentRepairs.length > 0" class="space-y-4">
                        <div v-for="repair in recentRepairs" :key="repair.id" class="bg-gray-800 rounded-lg p-4 hover:bg-gray-750 transition-colors duration-200">
                            <div class="flex items-center justify-between mb-2">
                                <Link :href="route('repair-orders.show', repair.id)" class="text-sm font-medium text-white hover:text-red-300 transition-colors duration-200">
                                    {{ repair.order_number }}
                                </Link>
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full border" :class="getStatusColor(repair.status)">
                                    {{ repair.status.replace('_', ' ').toUpperCase() }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-400 mb-2">{{ repair.service?.name }}</p>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>{{ new Date(repair.created_at).toLocaleDateString() }}</span>
                                <span v-if="repair.technician">${{ repair.total_cost }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-400 text-sm mb-4">No repair history yet</p>
                        <button @click="openRepairOrderModal" class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-6 py-2 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl inline-flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span>Create First Repair Order</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Device Modal -->
        <DeviceModal
            :show="showEditModal"
            :device="device"
            :customers="customers"
            :device-types="deviceTypes"
            @close="showEditModal = false"
            @saved="handleDeviceSaved"
        />

        <!-- Repair Order Modal -->
        <RepairOrderModal
            :show="showRepairOrderModal"
            :customers="customers"
            :devices="[device]"
            :services="services"
            :technicians="technicians"
            :parts="parts"
            :preselected-customer="device.customer_id"
            @close="showRepairOrderModal = false"
            @saved="handleRepairOrderSaved"
        />

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal
            :show="showDeleteModal"
            :processing="isDeleting"
            title="Delete Device"
            :message="`Are you sure you want to delete ${device.brand} ${device.model}? This action cannot be undone and will also delete all associated repair orders.`"
            confirm-text="Delete Device"
            cancel-text="Cancel"
            type="danger"
            @confirm="handleDeleteConfirm"
            @cancel="handleDeleteCancel"
        />
    </AuthenticatedLayout>
</template>
