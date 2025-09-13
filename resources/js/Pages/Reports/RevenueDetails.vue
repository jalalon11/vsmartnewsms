<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Chart from '@/Components/Chart.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    revenueData: {
        type: Object,
        default: () => ({})
    },
    revenueBreakdown: {
        type: Array,
        default: () => []
    },
    revenueComparison: {
        type: Array,
        default: () => []
    },
    topRevenueServices: {
        type: Array,
        default: () => []
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

// Chart data for revenue breakdown
const revenueBreakdownChartData = computed(() => {
    const breakdown = props.revenueBreakdown || [];
    return {
        labels: breakdown.map(item => item.service_name || 'Unknown'),
        datasets: [{
            label: 'Revenue by Service',
            data: breakdown.map(item => parseFloat(item.revenue || 0)),
            backgroundColor: [
                'rgba(239, 68, 68, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(139, 92, 246, 0.8)',
                'rgba(236, 72, 153, 0.8)',
            ],
            borderColor: [
                'rgb(239, 68, 68)',
                'rgb(59, 130, 246)',
                'rgb(16, 185, 129)',
                'rgb(245, 158, 11)',
                'rgb(139, 92, 246)',
                'rgb(236, 72, 153)',
            ],
            borderWidth: 2,
        }]
    };
});

// Daily revenue trend chart
const dailyRevenueChartData = computed(() => {
    const dailyData = props.revenueData?.daily_revenue || [];
    return {
        labels: dailyData.map(item => item.date || ''),
        datasets: [{
            label: 'Daily Revenue',
            data: dailyData.map(item => parseFloat(item.revenue || 0)),
            borderColor: 'rgb(239, 68, 68)',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
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
            callbacks: {
                label: function(context) {
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    if (context.parsed.y !== null) {
                        label += '₱' + context.parsed.y.toLocaleString('en-US', { minimumFractionDigits: 2 });
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
                },
                callback: function(value) {
                    return '₱' + value.toLocaleString('en-US', { minimumFractionDigits: 0 });
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
    <Head title="Revenue Details" />

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
                            Revenue Details
                        </h2>
                    </div>
                    <p class="text-sm text-gray-400 mt-1">
                        Comprehensive revenue analytics and insights
                    </p>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Revenue Overview Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Revenue -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-400">Total Revenue</p>
                                <p class="text-2xl font-bold text-white">
                                    {{ formatCurrency(revenueData.total_revenue) }}
                                </p>
                            </div>
                            <div class="p-3 bg-green-500/20 rounded-lg">
                                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center">
                            <span :class="getGrowthColor(revenueData.revenue_growth)" class="text-sm font-medium">
                                {{ formatPercentage(revenueData.revenue_growth) }}
                            </span>
                            <span class="text-gray-400 text-sm ml-2">vs previous period</span>
                        </div>
                    </div>

                    <!-- Average Daily Revenue -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-400">Avg Daily Revenue</p>
                                <p class="text-2xl font-bold text-white">
                                    {{ formatCurrency((revenueData.total_revenue || 0) / 30) }}
                                </p>
                            </div>
                            <div class="p-3 bg-blue-500/20 rounded-lg">
                                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Sources -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-400">Revenue Sources</p>
                                <p class="text-2xl font-bold text-white">
                                    {{ revenueBreakdown.length }}
                                </p>
                            </div>
                            <div class="p-3 bg-purple-500/20 rounded-lg">
                                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Top Service Revenue -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-400">Top Service</p>
                                <p class="text-lg font-bold text-white truncate">
                                    {{ revenueBreakdown[0]?.service_name || 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-400">
                                    {{ formatCurrency(revenueBreakdown[0]?.revenue || 0) }}
                                </p>
                            </div>
                            <div class="p-3 bg-yellow-500/20 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Revenue Breakdown Chart -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-white">Revenue by Service</h3>
                        </div>
                        <Chart
                            type="doughnut"
                            :data="revenueBreakdownChartData"
                            :options="chartOptions"
                            :height="300"
                        />
                    </div>

                    <!-- Daily Revenue Trend -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-white">Daily Revenue Trend</h3>
                        </div>
                        <Chart
                            type="line"
                            :data="dailyRevenueChartData"
                            :options="chartOptions"
                            :height="300"
                        />
                    </div>
                </div>

                <!-- Revenue Breakdown Table -->
                <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-xl">
                    <div class="px-6 py-4 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-white">Revenue Breakdown by Service</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Service</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Revenue</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Orders</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Avg Order Value</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                <tr v-for="item in revenueBreakdown" :key="item.service_name" class="hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                                        {{ item.service_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ formatCurrency(item.revenue) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ item.count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ formatCurrency(item.revenue / item.count) }}
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
