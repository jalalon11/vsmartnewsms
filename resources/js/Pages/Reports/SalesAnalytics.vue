<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Chart from '@/Components/Chart.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    metrics: {
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

// Period options
const periodOptions = [
    { value: '7days', label: 'Last 7 Days' },
    { value: '30days', label: 'Last 30 Days' },
    { value: '90days', label: 'Last 90 Days' },
    { value: '1year', label: 'Last Year' },
];

const selectedPeriod = ref(props.period);

const changePeriod = () => {
    window.location.href = route('reports.sales-analytics', { period: selectedPeriod.value });
};

// Chart configurations
const salesTrendChartConfig = computed(() => ({
    type: 'line',
    data: {
        labels: props.charts.sales_trend?.map(item => new Date(item.date).toLocaleDateString()) || [],
        datasets: [{
            label: 'Sales Revenue',
            data: props.charts.sales_trend?.map(item => item.sales) || [],
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4,
            fill: true,
            yAxisID: 'y'
        }, {
            label: 'Number of Orders',
            data: props.charts.sales_trend?.map(item => item.orders) || [],
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: false,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: {
                labels: { color: '#e5e7eb' }
            }
        },
        scales: {
            x: { ticks: { color: '#9ca3af' } },
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                ticks: { color: '#9ca3af' }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                ticks: { color: '#9ca3af' },
                grid: {
                    drawOnChartArea: false,
                },
            }
        }
    }
}));

const paymentMethodsChartConfig = computed(() => ({
    type: 'doughnut',
    data: {
        labels: props.charts.payment_methods?.map(item => item.payment_method || 'Unknown') || [],
        datasets: [{
            data: props.charts.payment_methods?.map(item => item.total) || [],
            backgroundColor: [
                '#10b981', // cash - green
                '#3b82f6', // card - blue
                '#f59e0b', // bank transfer - yellow
                '#ef4444', // gcash - red
                '#8b5cf6', // paymaya - purple
                '#06b6d4', // other - cyan
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

// Computed values
const totalCollected = computed(() => {
    return props.metrics.total_sales || 0;
});

const totalPending = computed(() => {
    return (props.metrics.pending_amount || 0) + (props.metrics.partially_paid_amount || 0);
});

const collectionRate = computed(() => {
    const total = totalCollected.value + totalPending.value;
    return total > 0 ? (totalCollected.value / total) * 100 : 0;
});
</script>

<template>
    <Head title="Sales Analytics" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <Link :href="route('reports.index')" class="text-gray-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-white leading-tight">
                        Sales Analytics
                    </h2>
                </div>
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

                <!-- Key Metrics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Sales -->
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
                                <p class="text-sm font-medium text-gray-400">Total Sales</p>
                                <p class="text-2xl font-semibold text-white">{{ formatCurrency(metrics.total_sales) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Amount -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Pending Amount</p>
                                <p class="text-2xl font-semibold text-white">{{ formatCurrency(metrics.pending_amount) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Partially Paid -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Partially Paid</p>
                                <p class="text-2xl font-semibold text-white">{{ formatCurrency(metrics.partially_paid_amount) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Collection Rate -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Collection Rate</p>
                                <p class="text-2xl font-semibold text-white">{{ collectionRate.toFixed(1) }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Sales Trend Chart -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-white mb-4">Sales Trend</h3>
                            <Chart :config="salesTrendChartConfig" />
                        </div>
                    </div>

                    <!-- Payment Methods Chart -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-white mb-4">Payment Methods Distribution</h3>
                            <Chart :config="paymentMethodsChartConfig" />
                        </div>
                    </div>
                </div>

                <!-- Payment Methods Table -->
                <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-white mb-6">Payment Methods Breakdown</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-700">
                                <thead class="bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Payment Method
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Total Amount
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Transaction Count
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Average Transaction
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Percentage
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-800 divide-y divide-gray-700">
                                    <tr v-for="method in metrics.payment_methods" :key="method.payment_method" class="hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-white capitalize">{{ method.payment_method || 'Unknown' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-green-400">{{ formatCurrency(method.total) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-300">{{ formatNumber(method.count) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-300">{{ formatCurrency(method.total / method.count) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-300">
                                                {{ ((method.total / metrics.total_sales) * 100).toFixed(1) }}%
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="!metrics.payment_methods || metrics.payment_methods.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-400">
                                            No payment data available for the selected period.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
