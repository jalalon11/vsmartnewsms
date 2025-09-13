<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Chart from '@/Components/Chart.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    data: {
        type: Object,
        default: () => ({})
    },
    charts: {
        type: Object,
        default: () => ({})
    },
    period: {
        type: String,
        default: '30days'
    },
    dateRange: {
        type: Object,
        default: () => ({})
    }
});

// Utility functions
const formatCurrency = (amount) => {
    return '₱' + parseFloat(amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2 });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
};

const formatPercentage = (value) => {
    return `${value.toFixed(1)}%`;
};

// Chart data
const ordersChartData = computed(() => {
    const chartData = props.charts.daily_trend || [];
    return {
        labels: chartData.map(item => formatDate(item.date)),
        datasets: [{
            label: 'Daily Orders',
            data: chartData.map(item => parseInt(item.count || 0)),
            borderColor: 'rgb(16, 185, 129)',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true,
        }]
    };
});

const statusChartData = computed(() => {
    const statusData = props.data.orders_by_status || {};
    const statusColors = {
        'pending': 'rgba(245, 158, 11, 0.8)',
        'in_progress': 'rgba(59, 130, 246, 0.8)',
        'waiting_parts': 'rgba(249, 115, 22, 0.8)',
        'completed': 'rgba(16, 185, 129, 0.8)',
        'delivered': 'rgba(34, 197, 94, 0.8)',
        'cancelled': 'rgba(239, 68, 68, 0.8)',
    };

    const labels = Object.keys(statusData).map(status => status.replace('_', ' ').toUpperCase());
    const data = Object.values(statusData);
    const colors = Object.keys(statusData).map(status => statusColors[status] || 'rgba(156, 163, 175, 0.8)');

    return {
        labels,
        datasets: [{
            data,
            backgroundColor: colors,
            borderWidth: 2,
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
                font: { family: 'Inter, system-ui, sans-serif' }
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
            ticks: { color: '#9CA3AF', font: { family: 'Inter, system-ui, sans-serif' } },
            grid: { color: '#374151', borderColor: '#4B5563' }
        },
        y: {
            beginAtZero: true,
            ticks: { color: '#9CA3AF', font: { family: 'Inter, system-ui, sans-serif' } },
            grid: { color: '#374151', borderColor: '#4B5563' }
        }
    }
};

const doughnutOptions = {
    ...chartOptions,
    scales: undefined
};

const changePeriod = (newPeriod) => {
    window.location.href = route('reports.orders', { period: newPeriod });
};

const getStatusColor = (status) => {
    const colors = {
        'pending': 'text-yellow-400',
        'in_progress': 'text-blue-400',
        'waiting_parts': 'text-orange-400',
        'completed': 'text-green-400',
        'delivered': 'text-green-500',
        'cancelled': 'text-red-400',
    };
    return colors[status] || 'text-gray-400';
};

const getStatusBadgeColor = (status) => {
    const colors = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'in_progress': 'bg-blue-100 text-blue-800',
        'waiting_parts': 'bg-orange-100 text-orange-800',
        'completed': 'bg-green-100 text-green-800',
        'delivered': 'bg-green-200 text-green-900',
        'cancelled': 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Orders Analytics" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <Link :href="route('reports.index')" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <h2 class="text-2xl font-bold leading-tight text-white">
                            Orders Analytics
                        </h2>
                        <p class="text-sm text-gray-400 mt-1">
                            Detailed order analysis and performance metrics
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <select 
                        :value="period" 
                        @change="changePeriod($event.target.value)"
                        class="bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="7days">Last 7 Days</option>
                        <option value="30days">Last 30 Days</option>
                        <option value="90days">Last 90 Days</option>
                        <option value="thisyear">This Year</option>
                    </select>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Orders Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Total Orders</p>
                                <p class="text-3xl font-bold mt-1">{{ data.total_orders?.toLocaleString() }}</p>
                            </div>
                            <div class="p-3 bg-green-500 bg-opacity-30 rounded-lg">
                                <svg class="w-8 h-8 text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Completed Orders</p>
                                <p class="text-3xl font-bold mt-1">{{ data.completed_orders?.toLocaleString() }}</p>
                            </div>
                            <div class="p-3 bg-blue-500 bg-opacity-30 rounded-lg">
                                <svg class="w-8 h-8 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Completion Rate</p>
                                <p class="text-3xl font-bold mt-1">{{ formatPercentage(data.completion_rate) }}</p>
                            </div>
                            <div class="p-3 bg-purple-500 bg-opacity-30 rounded-lg">
                                <svg class="w-8 h-8 text-purple-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm font-medium">Avg. Order Value</p>
                                <p class="text-3xl font-bold mt-1">{{ formatCurrency(data.average_order_value) }}</p>
                            </div>
                            <div class="p-3 bg-orange-500 bg-opacity-30 rounded-lg">
                                <svg class="w-8 h-8 text-orange-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Orders Trend Chart -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
                        <h3 class="text-lg font-semibold text-white mb-6">Orders Trend</h3>
                        <Chart
                            type="line"
                            :data="ordersChartData"
                            :options="chartOptions"
                            :height="400"
                        />
                    </div>

                    <!-- Orders by Status Chart -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
                        <h3 class="text-lg font-semibold text-white mb-6">Orders by Status</h3>
                        <Chart
                            type="doughnut"
                            :data="statusChartData"
                            :options="doughnutOptions"
                            :height="400"
                        />
                    </div>
                </div>

                <!-- Orders Status Breakdown -->
                <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-lg">
                    <div class="p-6 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-white">Order Status Breakdown</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div v-for="(count, status) in data.orders_by_status" :key="status" 
                                 class="bg-gray-700 rounded-lg p-4 border border-gray-600">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                              :class="getStatusBadgeColor(status)">
                                            {{ status.replace('_', ' ').toUpperCase() }}
                                        </span>
                                        <p class="text-2xl font-bold mt-2" :class="getStatusColor(status)">{{ count }}</p>
                                        <p class="text-sm text-gray-400">
                                            {{ formatPercentage((count / data.total_orders) * 100) }} of total
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
