<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Chart from '@/Components/Chart.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    ordersData: {
        type: Object,
        default: () => ({})
    },
    ordersByStatus: {
        type: Object,
        default: () => ({})
    },
    ordersTrend: {
        type: Array,
        default: () => []
    },
    averageOrderValue: {
        type: Number,
        default: 0
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    dateRange: {
        type: Object,
        default: () => ({})
    }
});

const formatCurrency = (amount) => {
    return '₱' + parseFloat(amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2 });
};

const formatPercentage = (value) => {
    const sign = value >= 0 ? '+' : '';
    return `${sign}${value.toFixed(1)}%`;
};

const getGrowthColor = (value) => {
    return value >= 0 ? 'text-green-400' : 'text-red-400';
};

const getStatusColor = (status) => {
    const colors = {
        'pending': 'text-yellow-400 bg-yellow-400/20',
        'in_progress': 'text-blue-400 bg-blue-400/20',
        'waiting_parts': 'text-orange-400 bg-orange-400/20',
        'completed': 'text-green-400 bg-green-400/20',
        'delivered': 'text-green-400 bg-green-400/20',
        'cancelled': 'text-red-400 bg-red-400/20',
    };
    return colors[status] || 'text-gray-400 bg-gray-400/20';
};

// Chart data for orders by status
const ordersByStatusChartData = computed(() => {
    const statusData = props.ordersByStatus || {};
    const statuses = Object.keys(statusData);
    const counts = Object.values(statusData);
    
    return {
        labels: statuses.map(status => status.replace('_', ' ').toUpperCase()),
        datasets: [{
            label: 'Orders by Status',
            data: counts,
            backgroundColor: [
                'rgba(245, 158, 11, 0.8)', // pending - yellow
                'rgba(59, 130, 246, 0.8)', // in_progress - blue
                'rgba(249, 115, 22, 0.8)', // waiting_parts - orange
                'rgba(16, 185, 129, 0.8)', // completed - green
                'rgba(34, 197, 94, 0.8)', // delivered - green
                'rgba(239, 68, 68, 0.8)', // cancelled - red
            ],
            borderColor: [
                'rgb(245, 158, 11)',
                'rgb(59, 130, 246)',
                'rgb(249, 115, 22)',
                'rgb(16, 185, 129)',
                'rgb(34, 197, 94)',
                'rgb(239, 68, 68)',
            ],
            borderWidth: 2,
        }]
    };
});

// Orders trend chart
const ordersTrendChartData = computed(() => {
    const trendData = props.ordersTrend || [];
    return {
        labels: trendData.map(item => item.date || ''),
        datasets: [{
            label: 'Daily Orders',
            data: trendData.map(item => parseInt(item.count || 0)),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true,
        }]
    };
});

// Chart options
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'top',
            labels: {
                color: '#D1D5DB',
                font: {
                    family: 'Inter, system-ui, sans-serif'
                }
            }
        },
        tooltip: {
            backgroundColor: '#1F2937',
            titleColor: '#F9FAFB',
            bodyColor: '#D1D5DB',
            borderColor: '#374151',
            borderWidth: 1,
            cornerRadius: 8,
        }
    },
    scales: {
        x: {
            ticks: {
                color: '#9CA3AF',
                font: {
                    family: 'Inter, system-ui, sans-serif'
                }
            },
            grid: {
                color: '#374151',
                borderColor: '#4B5563'
            }
        },
        y: {
            beginAtZero: true,
            ticks: {
                color: '#9CA3AF',
                font: {
                    family: 'Inter, system-ui, sans-serif'
                }
            },
            grid: {
                color: '#374151',
                borderColor: '#4B5563'
            }
        }
    }
};
</script>

<template>
    <Head title="Orders Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-3">
                        <Link :href="route('reports.index')" class="text-gray-400 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </Link>
                        <h2 class="text-xl font-semibold leading-tight text-white">
                            Orders Details
                        </h2>
                    </div>
                    <p class="text-sm text-gray-400 mt-1">
                        Comprehensive order analytics and insights
                    </p>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Orders Overview Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Orders -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-400">Total Orders</p>
                                <p class="text-2xl font-bold text-white">
                                    {{ ordersData.total_orders || 0 }}
                                </p>
                            </div>
                            <div class="p-3 bg-blue-500/20 rounded-lg">
                                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center">
                            <span :class="getGrowthColor(ordersData.orders_growth)" class="text-sm font-medium">
                                {{ formatPercentage(ordersData.orders_growth || 0) }}
                            </span>
                            <span class="text-gray-400 text-sm ml-2">vs previous period</span>
                        </div>
                    </div>

                    <!-- Average Order Value -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-400">Avg Order Value</p>
                                <p class="text-2xl font-bold text-white">
                                    {{ formatCurrency(averageOrderValue) }}
                                </p>
                            </div>
                            <div class="p-3 bg-green-500/20 rounded-lg">
                                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Average -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-400">Daily Average</p>
                                <p class="text-2xl font-bold text-white">
                                    {{ Math.round((ordersData.total_orders || 0) / 30) }}
                                </p>
                            </div>
                            <div class="p-3 bg-purple-500/20 rounded-lg">
                                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Completion Rate -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-400">Completion Rate</p>
                                <p class="text-2xl font-bold text-white">
                                    {{ Math.round(((ordersByStatus.completed || 0) + (ordersByStatus.delivered || 0)) / (ordersData.total_orders || 1) * 100) }}%
                                </p>
                            </div>
                            <div class="p-3 bg-yellow-500/20 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Orders by Status Chart -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-white">Orders by Status</h3>
                        </div>
                        <Chart
                            type="doughnut"
                            :data="ordersByStatusChartData"
                            :options="chartOptions"
                            :height="300"
                        />
                    </div>

                    <!-- Orders Trend -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-white">Orders Trend</h3>
                        </div>
                        <Chart
                            type="line"
                            :data="ordersTrendChartData"
                            :options="chartOptions"
                            :height="300"
                        />
                    </div>
                </div>

                <!-- Orders Status Breakdown Table -->
                <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-xl">
                    <div class="px-6 py-4 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-white">Orders Status Breakdown</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Count</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Percentage</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                <tr v-for="(count, status) in ordersByStatus" :key="status" class="hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusColor(status)">
                                            {{ status.replace('_', ' ').toUpperCase() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ Math.round((count / (ordersData.total_orders || 1)) * 100) }}%
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
