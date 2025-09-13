<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SalesChart from '@/Components/SalesChart.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    recentRepairs: Array,
    repairStatusStats: Object,
    monthlyRevenue: Array,
    salesAnalytics: Object,
});

// Quick action functions
const updateOrderStatus = (orderId, newStatus) => {
    router.patch(route('repair-orders.update-status', orderId), {
        status: newStatus
    }, {
        preserveScroll: true,
        onSuccess: () => {
            router.reload({ only: ['recentRepairs', 'stats'] });
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
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-white">
                    Dashboard
                </h2>
                <Link
                    :href="route('dashboard.technician-view')"
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    View as Technician
                </Link>
            </div>
        </template>

        <div class="p-6 space-y-8">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-xl p-6 text-white shadow-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold mb-2">Welcome back, {{ $page.props.auth.user.name }}!</h1>
                        <p class="text-red-100">Here's what's happening with your repair shop today.</p>
                    </div>
                    <div class="hidden md:block">
                        <svg class="w-16 h-16 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-400 mb-1">Total Customers</p>
                            <p class="text-3xl font-bold text-white">{{ stats?.total_customers || 0 }}</p>
                            <p class="text-xs text-green-400 mt-1">+12% from last month</p>
                        </div>
                        <div class="p-3 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-400 mb-1">Active Repairs</p>
                            <p class="text-3xl font-bold text-white">{{ stats?.active_repairs || 0 }}</p>
                            <p class="text-xs text-yellow-400 mt-1">{{ stats?.active_repairs > 10 ? 'High volume' : 'Normal' }}</p>
                        </div>
                        <div class="p-3 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-400 mb-1">Completed Today</p>
                            <p class="text-3xl font-bold text-white">{{ stats?.completed_today || 0 }}</p>
                            <p class="text-xs text-green-400 mt-1">Great progress!</p>
                        </div>
                        <div class="p-3 rounded-full bg-gradient-to-br from-green-500 to-green-600 shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-400 mb-1">Pending Invoices</p>
                            <p class="text-3xl font-bold text-white">{{ stats?.pending_invoices || 0 }}</p>
                            <p class="text-xs text-blue-400 mt-1">Awaiting payment</p>
                        </div>
                        <div class="p-3 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-400 mb-1">Low Stock Parts</p>
                            <p class="text-3xl font-bold text-white">{{ stats?.low_stock_parts || 0 }}</p>
                            <p class="text-xs" :class="stats?.low_stock_parts > 0 ? 'text-red-400' : 'text-green-400'">
                                {{ stats?.low_stock_parts > 0 ? 'Needs attention' : 'All good' }}
                            </p>
                        </div>
                        <div class="p-3 rounded-full bg-gradient-to-br from-red-500 to-red-600 shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Analytics Chart -->
            <div class="mb-8">
                <SalesChart :salesData="salesAnalytics" />
            </div>

            <!-- Recent Repairs -->
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl">
                <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-700 rounded-t-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-red-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Recent Repair Orders</h3>
                        </div>
                        <Link :href="route('repair-orders.index')" class="text-red-400 hover:text-red-300 text-sm font-medium transition-colors duration-200">
                            View All →
                        </Link>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Order #</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Device</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Service</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Technician</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-900 divide-y divide-gray-700">
                            <tr v-for="repair in recentRepairs" :key="repair.id" class="hover:bg-gray-800 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-white">{{ repair.order_number }}</div>
                                    <div class="text-xs text-gray-400">{{ new Date(repair.created_at).toLocaleDateString() }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-xs font-medium text-white">{{ repair.customer?.first_name?.charAt(0) }}{{ repair.customer?.last_name?.charAt(0) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-white">{{ repair.customer?.full_name }}</div>
                                            <div class="text-xs">
                                                <a v-if="repair.customer?.facebook_link"
                                                   :href="repair.customer.facebook_link"
                                                   target="_blank"
                                                   class="text-blue-400 hover:text-blue-300 transition-colors duration-200 flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                    </svg>
                                                    Facebook
                                                </a>
                                                <span v-else class="text-gray-500 italic">No Facebook link</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-white">{{ repair.device?.brand }} {{ repair.device?.model }}</div>
                                    <div class="text-xs text-gray-400">{{ repair.device?.device_type?.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-300">{{ repair.service?.name }}</div>
                                    <div class="text-xs text-gray-400">₱{{ repair.service?.base_price }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full shadow-sm"
                                          :class="{
                                              'bg-yellow-100 text-yellow-800 border border-yellow-200': repair.status === 'pending',
                                              'bg-blue-100 text-blue-800 border border-blue-200': repair.status === 'in_progress',
                                              'bg-orange-100 text-orange-800 border border-orange-200': repair.status === 'waiting_parts',
                                              'bg-green-100 text-green-800 border border-green-200': repair.status === 'completed',
                                              'bg-red-100 text-red-800 border border-red-200': repair.status === 'cancelled',
                                              'bg-purple-100 text-purple-800 border border-purple-200': repair.status === 'delivered'
                                          }">
                                        <div class="w-1.5 h-1.5 rounded-full mr-2"
                                             :class="{
                                                 'bg-yellow-500': repair.status === 'pending',
                                                 'bg-blue-500': repair.status === 'in_progress',
                                                 'bg-orange-500': repair.status === 'waiting_parts',
                                                 'bg-green-500': repair.status === 'completed',
                                                 'bg-red-500': repair.status === 'cancelled',
                                                 'bg-purple-500': repair.status === 'delivered'
                                             }"></div>
                                        {{ repair.status.replace('_', ' ').toUpperCase() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div v-if="repair.assigned_technician_name && repair.assigned_technician_name !== 'Unassigned'" class="flex items-center">
                                        <div class="w-6 h-6 bg-gray-600 rounded-full flex items-center justify-center mr-2">
                                            <span class="text-xs font-medium text-white">{{ repair.assigned_technician_name.charAt(0) }}</span>
                                        </div>
                                        <span class="text-sm text-gray-300">{{ repair.assigned_technician_name }}</span>
                                    </div>
                                    <span v-else class="text-sm text-gray-500 italic">Unassigned</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <!-- View Button -->
                                        <Link :href="route('repair-orders.show', repair.id)"
                                              class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-blue-600 text-white hover:bg-blue-700 transition-colors duration-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </Link>

                                        <!-- Edit Button -->
                                        <Link :href="route('repair-orders.edit', repair.id)"
                                              class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-gray-600 text-white hover:bg-gray-700 transition-colors duration-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </Link>

                                        <!-- Status Update Dropdown -->
                                        <div class="relative inline-block text-left">
                                            <select @change="updateOrderStatus(repair.id, $event.target.value)"
                                                    :value="repair.status"
                                                    class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-green-600 text-white hover:bg-green-700 transition-colors duration-200 border-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-gray-900">
                                                <option value="pending">Pending</option>
                                                <option value="in_progress">In Progress</option>
                                                <option value="waiting_parts">Waiting Parts</option>
                                                <option value="completed">Completed</option>
                                                <option value="delivered">Delivered</option>
                                                <option value="cancelled">Cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!recentRepairs || recentRepairs.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-gray-400 text-sm">No recent repair orders</p>
                                        <Link :href="route('repair-orders.create')" class="mt-2 text-red-400 hover:text-red-300 text-sm font-medium">
                                            Create your first repair order →
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
