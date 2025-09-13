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
    window.location.href = route('reports.service-analytics', { period: selectedPeriod.value });
};

// Chart configurations
const servicePopularityChartConfig = computed(() => ({
    type: 'bar',
    data: {
        labels: props.charts.service_popularity?.map(item => item.service_name) || [],
        datasets: [{
            label: 'Service Count',
            data: props.charts.service_popularity?.map(item => item.count) || [],
            backgroundColor: 'rgba(16, 185, 129, 0.8)',
            borderColor: 'rgb(16, 185, 129)',
            borderWidth: 1
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

const serviceProfitabilityChartConfig = computed(() => ({
    type: 'doughnut',
    data: {
        labels: props.charts.service_profitability?.map(item => item.service_name) || [],
        datasets: [{
            data: props.charts.service_profitability?.map(item => item.profit_margin) || [],
            backgroundColor: [
                '#10b981', // emerald
                '#3b82f6', // blue
                '#f59e0b', // amber
                '#ef4444', // red
                '#8b5cf6', // violet
                '#06b6d4', // cyan
                '#84cc16', // lime
                '#f97316', // orange
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

// Get profitability color
const getProfitabilityColor = (margin) => {
    if (margin >= 50) return 'text-green-400';
    if (margin >= 30) return 'text-blue-400';
    if (margin >= 15) return 'text-yellow-400';
    return 'text-red-400';
};

const getProfitabilityBadgeColor = (margin) => {
    if (margin >= 50) return 'bg-green-100 text-green-800';
    if (margin >= 30) return 'bg-blue-100 text-blue-800';
    if (margin >= 15) return 'bg-yellow-100 text-yellow-800';
    return 'bg-red-100 text-red-800';
};

const getProfitabilityLabel = (margin) => {
    if (margin >= 50) return 'Excellent';
    if (margin >= 30) return 'Good';
    if (margin >= 15) return 'Average';
    return 'Poor';
};
</script>

<template>
    <Head title="Service Analytics" />

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
                        Service Analytics
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
                    <!-- Total Services -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Total Services</p>
                                <p class="text-2xl font-semibold text-white">{{ formatNumber(metrics.total_services) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Most Popular Service -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Most Popular</p>
                                <p class="text-lg font-semibold text-white truncate">{{ metrics.most_popular_service || 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Average Service Time -->
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
                                <p class="text-sm font-medium text-gray-400">Avg. Service Time</p>
                                <p class="text-2xl font-semibold text-white">{{ formatNumber(metrics.average_service_time) }} hrs</p>
                            </div>
                        </div>
                    </div>

                    <!-- Average Profit Margin -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Avg. Profit Margin</p>
                                <p class="text-2xl font-semibold text-white">{{ formatPercentage(metrics.average_profit_margin) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Service Popularity Chart -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-white mb-4">Most Popular Services</h3>
                            <Chart :config="servicePopularityChartConfig" />
                        </div>
                    </div>

                    <!-- Service Profitability Chart -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-white mb-4">Service Profitability</h3>
                            <Chart :config="serviceProfitabilityChartConfig" />
                        </div>
                    </div>
                </div>

                <!-- Service Performance Table -->
                <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-white mb-6">Service Performance Details</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-700">
                                <thead class="bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Service Name
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Times Performed
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Avg. Completion Time
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Total Revenue
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Profit Margin
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Avg. Price
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-800 divide-y divide-gray-700">
                                    <tr v-for="service in metrics.service_details" :key="service.id" class="hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-white">{{ service.name }}</div>
                                            <div class="text-sm text-gray-400">{{ service.description || 'No description' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-blue-400">{{ formatNumber(service.times_performed) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-300">{{ formatNumber(service.avg_completion_time) }} hrs</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-300">{{ formatCurrency(service.total_revenue) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getProfitabilityBadgeColor(service.profit_margin)">
                                                {{ getProfitabilityLabel(service.profit_margin) }} ({{ formatPercentage(service.profit_margin) }})
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-300">{{ formatCurrency(service.avg_price) }}</div>
                                        </td>
                                    </tr>
                                    <tr v-if="!metrics.service_details || metrics.service_details.length === 0">
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-400">
                                            No service data available for the selected period.
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
