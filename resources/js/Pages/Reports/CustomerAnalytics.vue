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

const formatPhone = (phone) => {
    if (!phone) return 'N/A';
    // Format Philippine phone numbers
    if (phone.startsWith('+63')) return phone;
    if (phone.startsWith('63')) return '+' + phone;
    if (phone.startsWith('09')) return '+63' + phone.substring(1);
    return phone;
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
    window.location.href = route('reports.customer-analytics', { period: selectedPeriod.value });
};

// Chart configurations
const customerGrowthChartConfig = computed(() => ({
    type: 'line',
    data: {
        labels: props.charts.customer_growth?.map(item => new Date(item.date).toLocaleDateString()) || [],
        datasets: [{
            label: 'New Customers',
            data: props.charts.customer_growth?.map(item => item.count) || [],
            borderColor: 'rgb(147, 51, 234)',
            backgroundColor: 'rgba(147, 51, 234, 0.1)',
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

const customersByCityChartConfig = computed(() => ({
    type: 'doughnut',
    data: {
        labels: props.charts.customers_by_city?.map(item => item.city || 'Unknown') || [],
        datasets: [{
            data: props.charts.customers_by_city?.map(item => item.count) || [],
            backgroundColor: [
                '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444',
                '#6366f1', '#ec4899', '#84cc16', '#f97316', '#64748b'
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
    <Head title="Customer Analytics" />

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
                        Customer Analytics
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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- New Customers -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">New Customers</p>
                                <p class="text-2xl font-semibold text-white">{{ formatNumber(metrics.new_customers) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Retention -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Customer Retention</p>
                                <p class="text-2xl font-semibold text-white">{{ formatPercentage(metrics.customer_retention) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Top Customers Count -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Active Customers</p>
                                <p class="text-2xl font-semibold text-white">{{ formatNumber(metrics.top_customers?.length || 0) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Customer Growth Chart -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-white mb-4">Customer Growth Trend</h3>
                            <Chart :config="customerGrowthChartConfig" />
                        </div>
                    </div>

                    <!-- Customers by City Chart -->
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-white mb-4">Customers by City</h3>
                            <Chart :config="customersByCityChartConfig" />
                        </div>
                    </div>
                </div>

                <!-- Top Customers Table -->
                <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-white mb-6">Top Customers by Revenue</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-700">
                                <thead class="bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Customer
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Phone
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Total Orders
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Total Revenue
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Avg. Order Value
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-800 divide-y divide-gray-700">
                                    <tr v-for="customer in metrics.top_customers" :key="customer.id" class="hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-white">{{ customer.name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-300">{{ formatPhone(customer.phone) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-300">{{ formatNumber(customer.total_orders) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-green-400">{{ formatCurrency(customer.total_spent) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-300">{{ formatCurrency(customer.total_spent / customer.total_orders) }}</div>
                                        </td>
                                    </tr>
                                    <tr v-if="!metrics.top_customers || metrics.top_customers.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-400">
                                            No customer data available for the selected period.
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
