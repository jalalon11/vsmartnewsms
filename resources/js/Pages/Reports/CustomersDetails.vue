<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Chart from '@/Components/Chart.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    customersData: {
        type: Object,
        default: () => ({})
    },
    customerGrowth: {
        type: Array,
        default: () => []
    },
    topCustomers: {
        type: Array,
        default: () => []
    },
    customerRetention: {
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

// Customer growth chart
const customerGrowthChartData = computed(() => {
    const growthData = props.customerGrowth || [];
    return {
        labels: growthData.map(item => item.date || ''),
        datasets: [{
            label: 'New Customers',
            data: growthData.map(item => parseInt(item.count || 0)),
            borderColor: 'rgb(16, 185, 129)',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true,
        }]
    };
});

// Top customers chart
const topCustomersChartData = computed(() => {
    const topCustomersData = props.topCustomers.slice(0, 5) || [];
    return {
        labels: topCustomersData.map(item => item.customer?.name || 'Unknown'),
        datasets: [{
            label: 'Customer Revenue',
            data: topCustomersData.map(item => parseFloat(item.revenue || 0)),
            backgroundColor: [
                'rgba(239, 68, 68, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(139, 92, 246, 0.8)',
            ],
            borderColor: [
                'rgb(239, 68, 68)',
                'rgb(59, 130, 246)',
                'rgb(16, 185, 129)',
                'rgb(245, 158, 11)',
                'rgb(139, 92, 246)',
            ],
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
            callbacks: {
                label: function(context) {
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    if (context.parsed.y !== null) {
                        if (context.dataset.label === 'Customer Revenue') {
                            label += '₱' + context.parsed.y.toLocaleString('en-US', { minimumFractionDigits: 2 });
                        } else {
                            label += context.parsed.y;
                        }
                    }
                    return label;
                }
            }
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
    <Head title="Customers Details" />

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
                            Customers Details
                        </h2>
                    </div>
                    <p class="text-sm text-gray-400 mt-1">
                        Comprehensive customer analytics and insights
                    </p>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Customer Overview Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Customers -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-400">Total Customers</p>
                                <p class="text-2xl font-bold text-white">
                                    {{ customersData.total_customers || 0 }}
                                </p>
                            </div>
                            <div class="p-3 bg-blue-500/20 rounded-lg">
                                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center">
                            <span :class="getGrowthColor(customersData.customer_growth)" class="text-sm font-medium">
                                {{ formatPercentage(customersData.customer_growth || 0) }}
                            </span>
                            <span class="text-gray-400 text-sm ml-2">vs previous period</span>
                        </div>
                    </div>

                    <!-- New Customers -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-400">New Customers</p>
                                <p class="text-2xl font-bold text-white">
                                    {{ customersData.new_customers || 0 }}
                                </p>
                            </div>
                            <div class="p-3 bg-green-500/20 rounded-lg">
                                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Retention -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-400">Retention Rate</p>
                                <p class="text-2xl font-bold text-white">
                                    {{ Math.round(customerRetention) }}%
                                </p>
                            </div>
                            <div class="p-3 bg-purple-500/20 rounded-lg">
                                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Lifetime Value -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-400">Avg Lifetime Value</p>
                                <p class="text-2xl font-bold text-white">
                                    {{ formatCurrency(customersData.customer_lifetime_value || 0) }}
                                </p>
                            </div>
                            <div class="p-3 bg-yellow-500/20 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Customer Growth Chart -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-white">Customer Growth</h3>
                        </div>
                        <Chart
                            type="line"
                            :data="customerGrowthChartData"
                            :options="chartOptions"
                            :height="300"
                        />
                    </div>

                    <!-- Top Customers by Revenue -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-white">Top Customers by Revenue</h3>
                        </div>
                        <Chart
                            type="bar"
                            :data="topCustomersChartData"
                            :options="chartOptions"
                            :height="300"
                        />
                    </div>
                </div>

                <!-- Top Customers Table -->
                <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-xl">
                    <div class="px-6 py-4 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-white">Top Customers</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Revenue</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Orders</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Avg Order Value</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Contact</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                <tr v-for="(customer, index) in topCustomers" :key="index" class="hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-600 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-white">
                                                        {{ (customer.customer?.name || 'U').charAt(0).toUpperCase() }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-white">
                                                    {{ customer.customer?.name || 'Unknown' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ formatCurrency(customer.revenue) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ customer.orders_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ formatCurrency(customer.revenue / (customer.orders_count || 1)) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ customer.customer?.phone || 'N/A' }}
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
