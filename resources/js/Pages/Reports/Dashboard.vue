<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Chart from '@/Components/Chart.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    overview: {
        type: Object,
        default: () => ({})
    },
    customerMetrics: {
        type: Object,
        default: () => ({})
    },
    salesMetrics: {
        type: Object,
        default: () => ({})
    },
    repairMetrics: {
        type: Object,
        default: () => ({})
    },
    partsMetrics: {
        type: Object,
        default: () => ({})
    },
    technicianMetrics: {
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

const formatNumber = (number) => {
    return parseFloat(number || 0).toLocaleString('en-US');
};

const formatPercentage = (value) => {
    return `${parseFloat(value || 0).toFixed(1)}%`;
};

// Period options
const periodOptions = [
    { value: '7days', label: 'Last 7 Days' },
    { value: '30days', label: 'Last 30 Days' },
    { value: '90days', label: 'Last 90 Days' },
    { value: '1year', label: 'Last Year' },
];

const selectedPeriod = ref(props.period);

const changePeriod = () => {
    window.location.href = route('reports.index', { period: selectedPeriod.value });
};

// Chart configurations
const revenueChartConfig = computed(() => ({
    type: 'line',
    data: {
        labels: props.charts.revenue_trend?.map(item => new Date(item.date).toLocaleDateString()) || [],
        datasets: [{
            label: 'Revenue',
            data: props.charts.revenue_trend?.map(item => item.revenue) || [],
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                labels: { color: '#e5e7eb' }
            }
        },
        scales: {
            x: { ticks: { color: '#9ca3af' } },
            y: { ticks: { color: '#9ca3af' } }
        }
    }
}));

const repairsStatusChartConfig = computed(() => ({
    type: 'doughnut',
    data: {
        labels: Object.keys(props.charts.repairs_by_status || {}),
        datasets: [{
            data: Object.values(props.charts.repairs_by_status || {}),
            backgroundColor: [
                '#f59e0b', // pending
                '#3b82f6', // in_progress
                '#ef4444', // waiting_parts
                '#10b981', // completed
                '#6b7280', // cancelled
                '#22c55e'  // delivered
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                labels: { color: '#e5e7eb' }
            }
        }
    }
}));
</script>

<template>
    <Head title="Reports Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-white leading-tight">
                    Reports Dashboard
                </h2>
                <div class="flex items-center space-x-4">
                    <select v-model="selectedPeriod" @change="changePeriod" 
                            class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 px-3 py-2">
                        <option v-for="option in periodOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Overview Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Revenue -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Total Revenue</p>
                                <p class="text-2xl font-semibold text-white">{{ formatCurrency(overview.total_revenue) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Orders -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Total Orders</p>
                                <p class="text-2xl font-semibold text-white">{{ formatNumber(overview.total_orders) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- New Customers -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">New Customers</p>
                                <p class="text-2xl font-semibold text-white">{{ formatNumber(customerMetrics.new_customers) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Completion Rate -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Completion Rate</p>
                                <p class="text-2xl font-semibold text-white">{{ formatPercentage(overview.completion_rate) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Revenue Trend Chart -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-white mb-4">Revenue Trend</h3>
                            <Chart :config="revenueChartConfig" />
                        </div>
                    </div>

                    <!-- Repairs by Status Chart -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-white mb-4">Repairs by Status</h3>
                            <Chart :config="repairsStatusChartConfig" />
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Sales Metrics -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-white mb-4">Sales Overview</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Total Sales:</span>
                                <span class="text-white font-medium">{{ formatCurrency(salesMetrics.total_sales) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Pending Amount:</span>
                                <span class="text-yellow-400 font-medium">{{ formatCurrency(salesMetrics.pending_amount) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Average Order:</span>
                                <span class="text-white font-medium">{{ formatCurrency(overview.average_order_value) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Parts Metrics -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-white mb-4">Parts Overview</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Inventory Value:</span>
                                <span class="text-white font-medium">{{ formatCurrency(partsMetrics.total_inventory_value) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Low Stock Items:</span>
                                <span class="text-red-400 font-medium">{{ formatNumber(partsMetrics.low_stock_count) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Parts Cost:</span>
                                <span class="text-white font-medium">{{ formatCurrency(overview.total_parts_cost) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Repair Metrics -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-white mb-4">Repair Overview</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Active Repairs:</span>
                                <span class="text-blue-400 font-medium">{{ formatNumber(overview.active_repairs) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Completed:</span>
                                <span class="text-green-400 font-medium">{{ formatNumber(overview.completed_orders) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Avg. Repair Time:</span>
                                <span class="text-white font-medium">{{ Math.round(repairMetrics.average_repair_time || 0) }}h</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link :href="route('reports.customer-analytics')"
                          class="bg-gray-800 hover:bg-gray-700 overflow-hidden shadow-xl sm:rounded-lg p-6 transition-colors duration-200 block">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-white">Customer Analytics</h3>
                                <p class="text-gray-400 text-sm">Customer growth, retention, and behavior analysis</p>
                            </div>
                        </div>
                    </Link>

                    <Link :href="route('reports.sales-analytics')"
                          class="bg-gray-800 hover:bg-gray-700 overflow-hidden shadow-xl sm:rounded-lg p-6 transition-colors duration-200 block">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-white">Sales Analytics</h3>
                                <p class="text-gray-400 text-sm">Revenue trends, payment methods, and sales performance</p>
                            </div>
                        </div>
                    </Link>

                    <Link :href="route('reports.parts-analytics')"
                          class="bg-gray-800 hover:bg-gray-700 overflow-hidden shadow-xl sm:rounded-lg p-6 transition-colors duration-200 block">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-white">Parts Analytics</h3>
                                <p class="text-gray-400 text-sm">Inventory management, usage patterns, and cost analysis</p>
                            </div>
                        </div>
                    </Link>

                    <Link :href="route('reports.device-repair-analytics')"
                          class="bg-gray-800 hover:bg-gray-700 overflow-hidden shadow-xl sm:rounded-lg p-6 transition-colors duration-200 block">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-white">Device Repair Analytics</h3>
                                <p class="text-gray-400 text-sm">Repair trends, device types, and completion analysis</p>
                            </div>
                        </div>
                    </Link>

                    <Link :href="route('reports.technician-performance')"
                          class="bg-gray-800 hover:bg-gray-700 overflow-hidden shadow-xl sm:rounded-lg p-6 transition-colors duration-200 block">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-white">Technician Performance</h3>
                                <p class="text-gray-400 text-sm">Productivity metrics, workload, and specialization analysis</p>
                            </div>
                        </div>
                    </Link>

                    <Link :href="route('reports.services')"
                          class="bg-gray-800 hover:bg-gray-700 overflow-hidden shadow-xl sm:rounded-lg p-6 transition-colors duration-200 block">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-white">Service Analytics</h3>
                                <p class="text-gray-400 text-sm">Popular services, profitability, and completion times</p>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
