<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TechnicianModal from '@/Components/TechnicianModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    technician: Object,
    stats: Object,
});

// Modal states
const showEditModal = ref(false);

// Functions
const openEditModal = () => {
    showEditModal.value = true;
};

const handleTechnicianSaved = () => {
    showEditModal.value = false;
    router.reload({ only: ['technician'] });
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString();
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
    <Head :title="`${technician.user?.name} - Technician Details`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <Link :href="route('technicians.index')" class="text-gray-400 hover:text-white transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-lg font-bold text-white">{{ technician.user?.name?.charAt(0) }}</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">
                            {{ technician.user?.name }}
                        </h2>
                        <p class="text-gray-400 text-sm">{{ technician.specialization }} • Employee ID: {{ technician.employee_id }}</p>
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
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Total Orders</p>
                            <p class="text-3xl font-bold text-white">{{ stats.total_orders }}</p>
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
                            <p class="text-sm text-gray-400">Completed</p>
                            <p class="text-3xl font-bold text-white">{{ stats.completed_orders }}</p>
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
                            <p class="text-sm text-gray-400">In Progress</p>
                            <p class="text-3xl font-bold text-white">{{ stats.in_progress_orders }}</p>
                        </div>
                        <div class="p-3 bg-orange-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Total Revenue</p>
                            <p class="text-3xl font-bold text-white">{{ formatCurrency(stats.total_revenue) }}</p>
                        </div>
                            <div class="p-3 bg-purple-600 rounded-lg">
                                 <span class="text-white text-xl font-bold">₱</span>
                            </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Technician Information -->
                <div class="lg:col-span-1">
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl">
                        <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-700 rounded-t-xl">
                            <h3 class="text-lg font-semibold text-white">Technician Information</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div>
                                <label class="text-sm font-medium text-gray-400">Full Name</label>
                                <p class="text-white font-medium">{{ technician.user?.name }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-400">Email</label>
                                <p class="text-white font-medium">{{ technician.user?.email }}</p>
                            </div>
                            
                            <div v-if="technician.phone">
                                <label class="text-sm font-medium text-gray-400">Phone</label>
                                <p class="text-white font-medium">{{ technician.phone }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-400">Employee ID</label>
                                <p class="text-white font-medium">{{ technician.employee_id }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-400">Specialization</label>
                                <p class="text-white font-medium">{{ technician.specialization }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-400">Hire Date</label>
                                <p class="text-white font-medium">{{ formatDate(technician.hire_date) }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-400">Status</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border" 
                                      :class="technician.is_active ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200'">
                                    {{ technician.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <div v-if="technician.certifications">
                                <label class="text-sm font-medium text-gray-400">Certifications</label>
                                <p class="text-white text-sm bg-gray-800 rounded-lg p-3 mt-1">{{ technician.certifications }}</p>
                            </div>

                            <div v-if="technician.skills">
                                <label class="text-sm font-medium text-gray-400">Skills & Expertise</label>
                                <p class="text-white text-sm bg-gray-800 rounded-lg p-3 mt-1">{{ technician.skills }}</p>
                            </div>

                            <div v-if="technician.notes">
                                <label class="text-sm font-medium text-gray-400">Notes</label>
                                <p class="text-white text-sm bg-gray-800 rounded-lg p-3 mt-1">{{ technician.notes }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="lg:col-span-2">
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl">
                        <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-700 rounded-t-xl">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-white">Recent Repair Orders</h3>
                                <Link :href="route('repair-orders.index', { technician: technician.id })" class="text-red-400 hover:text-red-300 text-sm font-medium">
                                    View All →
                                </Link>
                            </div>
                        </div>
                        <div class="p-6">
                            <div v-if="technician.repair_orders?.length > 0" class="space-y-4">
                                <div v-for="order in technician.repair_orders" :key="order.id" class="bg-gray-800 rounded-lg p-4 border border-gray-700 hover:border-gray-600 transition-colors duration-200">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <Link :href="route('repair-orders.show', order.id)" class="text-lg font-semibold text-white hover:text-red-300 transition-colors duration-200">
                                                    {{ order.order_number }}
                                                </Link>
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full border"
                                                      :class="{
                                                          'bg-yellow-100 text-yellow-800 border-yellow-200': order.status === 'pending',
                                                          'bg-blue-100 text-blue-800 border-blue-200': order.status === 'in_progress',
                                                          'bg-orange-100 text-orange-800 border-orange-200': order.status === 'waiting_parts',
                                                          'bg-green-100 text-green-800 border-green-200': order.status === 'completed',
                                                          'bg-red-100 text-red-800 border-red-200': order.status === 'cancelled',
                                                          'bg-purple-100 text-purple-800 border-purple-200': order.status === 'delivered'
                                                      }">
                                                    {{ order.status.replace('_', ' ').toUpperCase() }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-300 mb-1">{{ order.customer?.full_name }}</p>
                                            <p class="text-sm text-gray-400">{{ order.device?.brand }} {{ order.device?.model }}</p>
                                            <p class="text-xs text-gray-500 mt-2">{{ order.service?.name }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-400">{{ formatDate(order.created_at) }}</p>
                                            <p class="text-sm font-medium text-white">{{ formatCurrency(order.total_cost) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-gray-400 text-sm">No repair orders assigned yet</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl">
                <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-700 rounded-t-xl">
                    <h3 class="text-lg font-semibold text-white">Performance Metrics</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-white mb-2">
                                {{ stats.avg_completion_time ? Math.round(stats.avg_completion_time) : 'N/A' }}
                            </div>
                            <div class="text-sm text-gray-400">Average Completion Time (days)</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-white mb-2">
                                {{ stats.completed_orders > 0 ? Math.round((stats.completed_orders / stats.total_orders) * 100) : 0 }}%
                            </div>
                            <div class="text-sm text-gray-400">Completion Rate</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-white mb-2">
                                {{ formatCurrency(stats.total_revenue / Math.max(stats.completed_orders, 1)) }}
                            </div>
                            <div class="text-sm text-gray-400">Average Order Value</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <TechnicianModal
            :show="showEditModal"
            :technician="technician"
            @close="showEditModal = false"
            @saved="handleTechnicianSaved"
        />
    </AuthenticatedLayout>
</template>
