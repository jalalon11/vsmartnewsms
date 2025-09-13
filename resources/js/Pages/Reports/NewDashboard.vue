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
    trends: {
        type: Object,
        default: () => ({})
    }
});

// Utility functions
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

const getGrowthIcon = (value) => {
    return value >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6';
};

// Chart data
const revenueChartData = computed(() => {
    const data = props.charts.daily_revenue || [];
    return {
        labels: data.map(item => new Date(item.date).toLocaleDateString()),
        datasets: [{
            label: 'Daily Revenue',
            data: data.map(item => parseFloat(item.revenue || 0)),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true,
        }]
    };
});

const ordersStatusChartData = computed(() => {
    const data = props.charts.orders_by_status || [];
    const statusColors = {
        'pending': 'rgba(245, 158, 11, 0.8)',
        'in_progress': 'rgba(59, 130, 246, 0.8)',
        'waiting_parts': 'rgba(249, 115, 22, 0.8)',
        'completed': 'rgba(16, 185, 129, 0.8)',
        'delivered': 'rgba(34, 197, 94, 0.8)',
        'cancelled': 'rgba(239, 68, 68, 0.8)',
    };

    return {
        labels: data.map(item => item.status.replace('_', ' ').toUpperCase()),
        datasets: [{
            data: data.map(item => item.count),
            backgroundColor: data.map(item => statusColors[item.status] || 'rgba(156, 163, 175, 0.8)'),
            borderWidth: 2,
        }]
    };
});

const topServicesChartData = computed(() => {
    const data = props.charts.top_services || [];
    return {
        labels: data.map(item => item.name),
        datasets: [{
            label: 'Revenue',
            data: data.map(item => parseFloat(item.revenue || 0)),
            backgroundColor: 'rgba(139, 92, 246, 0.8)',
            borderColor: 'rgb(139, 92, 246)',
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
            callbacks: {
                label: function(context) {
                    let label = context.dataset.label || '';
                    if (label) label += ': ';
                    if (context.parsed.y !== null) {
                        if (context.dataset.label === 'Revenue' || context.dataset.label === 'Daily Revenue') {
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
</script>

<template>
    <Head title="Reports Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-white">
                        Reports Dashboard
                    </h2>
                    <p class="text-sm text-gray-400 mt-1">
                        Comprehensive business analytics and insights
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <select class="bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="30days">Last 30 Days</option>
                        <option value="7days">Last 7 Days</option>
                        <option value="90days">Last 90 Days</option>
                        <option value="thisyear">This Year</option>
                    </select>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Key Metrics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Revenue Card -->
                    <Link :href="route('reports.revenue')" 
                          class="group bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Total Revenue</p>
                                <p class="text-3xl font-bold mt-1">{{ formatCurrency(metrics.total_revenue) }}</p>
                                <div class="flex items-center mt-3">
                                    <svg class="w-4 h-4 mr-1" :class="getGrowthColor(metrics.revenue_growth)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getGrowthIcon(metrics.revenue_growth)" />
                                    </svg>
                                    <span class="text-sm" :class="getGrowthColor(metrics.revenue_growth)">
                                        {{ formatPercentage(metrics.revenue_growth) }}
                                    </span>
                                    <span class="text-blue-100 text-sm ml-1">vs last month</span>
                                </div>
                            </div>
                            <div class="p-3 bg-blue-500 bg-opacity-30 rounded-lg group-hover:bg-opacity-40 transition-all duration-200">
                                <svg class="w-8 h-8 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                        </div>
                    </Link>

                    <!-- Total Orders Card -->
                    <Link :href="route('reports.orders')" 
                          class="group bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Total Orders</p>
                                <p class="text-3xl font-bold mt-1">{{ metrics.total_orders?.toLocaleString() }}</p>
                                <div class="flex items-center mt-3">
                                    <svg class="w-4 h-4 mr-1" :class="getGrowthColor(metrics.orders_growth)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getGrowthIcon(metrics.orders_growth)" />
                                    </svg>
                                    <span class="text-sm" :class="getGrowthColor(metrics.orders_growth)">
                                        {{ formatPercentage(metrics.orders_growth) }}
                                    </span>
                                    <span class="text-green-100 text-sm ml-1">vs last month</span>
                                </div>
                            </div>
                            <div class="p-3 bg-green-500 bg-opacity-30 rounded-lg group-hover:bg-opacity-40 transition-all duration-200">
                                <svg class="w-8 h-8 text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                    </Link>

                    <!-- Total Customers Card -->
                    <Link :href="route('reports.customers')" 
                          class="group bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Total Customers</p>
                                <p class="text-3xl font-bold mt-1">{{ metrics.total_customers?.toLocaleString() }}</p>
                                <div class="flex items-center mt-3">
                                    <svg class="w-4 h-4 mr-1" :class="getGrowthColor(metrics.customers_growth)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getGrowthIcon(metrics.customers_growth)" />
                                    </svg>
                                    <span class="text-sm" :class="getGrowthColor(metrics.customers_growth)">
                                        {{ formatPercentage(metrics.customers_growth) }}
                                    </span>
                                    <span class="text-purple-100 text-sm ml-1">vs last month</span>
                                </div>
                            </div>
                            <div class="p-3 bg-purple-500 bg-opacity-30 rounded-lg group-hover:bg-opacity-40 transition-all duration-200">
                                <svg class="w-8 h-8 text-purple-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                        </div>
                    </Link>

                    <!-- Active Repairs Card -->
                    <Link :href="route('reports.services')" 
                          class="group bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm font-medium">Active Repairs</p>
                                <p class="text-3xl font-bold mt-1">{{ metrics.active_repairs?.toLocaleString() }}</p>
                                <div class="flex items-center mt-3">
                                    <span class="text-orange-100 text-sm">
                                        {{ Math.round(metrics.completion_rate) }}% completion rate
                                    </span>
                                </div>
                            </div>
                            <div class="p-3 bg-orange-500 bg-opacity-30 rounded-lg group-hover:bg-opacity-40 transition-all duration-200">
                                <svg class="w-8 h-8 text-orange-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                </svg>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                    <!-- Revenue Trend Chart -->
                    <div class="lg:col-span-2 bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-white">Revenue Trend (Last 30 Days)</h3>
                            <Link :href="route('reports.revenue')" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                                View Details →
                            </Link>
                        </div>
                        <Chart
                            type="line"
                            :data="revenueChartData"
                            :options="chartOptions"
                            :height="300"
                        />
                    </div>

                    <!-- Orders Status Chart -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-white">Orders by Status</h3>
                            <Link :href="route('reports.orders')" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                                View Details →
                            </Link>
                        </div>
                        <Chart
                            type="doughnut"
                            :data="ordersStatusChartData"
                            :options="doughnutOptions"
                            :height="300"
                        />
                    </div>
                </div>

                <!-- Top Services Chart -->
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-white">Top Services by Revenue</h3>
                        <Link :href="route('reports.services')" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                            View All Services →
                        </Link>
                    </div>
                    <Chart
                        type="bar"
                        :data="topServicesChartData"
                        :options="chartOptions"
                        :height="300"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
