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

// Chart data
const revenueChartData = computed(() => {
    const chartData = props.charts.daily_trend || [];
    return {
        labels: chartData.map(item => formatDate(item.date)),
        datasets: [{
            label: 'Daily Revenue',
            data: chartData.map(item => parseFloat(item.revenue || 0)),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true,
        }]
    };
});

const serviceRevenueChartData = computed(() => {
    const chartData = props.charts.by_service || [];
    return {
        labels: chartData.map(item => item.name),
        datasets: [{
            label: 'Revenue by Service',
            data: chartData.map(item => parseFloat(item.revenue || 0)),
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(139, 92, 246, 0.8)',
                'rgba(236, 72, 153, 0.8)',
                'rgba(34, 197, 94, 0.8)',
                'rgba(249, 115, 22, 0.8)',
                'rgba(168, 85, 247, 0.8)',
                'rgba(14, 165, 233, 0.8)',
                'rgba(132, 204, 22, 0.8)',
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
                        label += '₱' + context.parsed.y.toLocaleString('en-US', { minimumFractionDigits: 2 });
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
            ticks: { 
                color: '#9CA3AF', 
                font: { family: 'Inter, system-ui, sans-serif' },
                callback: function(value) {
                    return '₱' + value.toLocaleString();
                }
            },
            grid: { color: '#374151', borderColor: '#4B5563' }
        }
    }
};

const barChartOptions = {
    ...chartOptions,
    indexAxis: 'y',
    scales: {
        x: {
            beginAtZero: true,
            ticks: { 
                color: '#9CA3AF', 
                font: { family: 'Inter, system-ui, sans-serif' },
                callback: function(value) {
                    return '₱' + value.toLocaleString();
                }
            },
            grid: { color: '#374151', borderColor: '#4B5563' }
        },
        y: {
            ticks: { color: '#9CA3AF', font: { family: 'Inter, system-ui, sans-serif' } },
            grid: { color: '#374151', borderColor: '#4B5563' }
        }
    }
};

const changePeriod = (newPeriod) => {
    window.location.href = route('reports.revenue', { period: newPeriod });
};
</script>

<template>
    <Head title="Revenue Analytics" />

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
                            Revenue Analytics
                        </h2>
                        <p class="text-sm text-gray-400 mt-1">
                            Detailed revenue analysis and trends
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
                <!-- Revenue Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Total Revenue</p>
                                <p class="text-3xl font-bold mt-1">{{ formatCurrency(data.total_revenue) }}</p>
                            </div>
                            <div class="p-3 bg-blue-500 bg-opacity-30 rounded-lg">
                                <svg class="w-8 h-8 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Average Daily</p>
                                <p class="text-3xl font-bold mt-1">{{ formatCurrency(data.average_daily) }}</p>
                            </div>
                            <div class="p-3 bg-green-500 bg-opacity-30 rounded-lg">
                                <svg class="w-8 h-8 text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Revenue Sources</p>
                                <p class="text-3xl font-bold mt-1">{{ data.revenue_sources }}</p>
                            </div>
                            <div class="p-3 bg-purple-500 bg-opacity-30 rounded-lg">
                                <svg class="w-8 h-8 text-purple-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm font-medium">Top Service Revenue</p>
                                <p class="text-2xl font-bold mt-1">{{ formatCurrency(data.top_services?.[0]?.revenue || 0) }}</p>
                                <p class="text-orange-100 text-xs mt-1">{{ data.top_services?.[0]?.name || 'N/A' }}</p>
                            </div>
                            <div class="p-3 bg-orange-500 bg-opacity-30 rounded-lg">
                                <svg class="w-8 h-8 text-orange-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Revenue Trend Chart -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
                        <h3 class="text-lg font-semibold text-white mb-6">Revenue Trend</h3>
                        <Chart
                            type="line"
                            :data="revenueChartData"
                            :options="chartOptions"
                            :height="400"
                        />
                    </div>

                    <!-- Revenue by Service Chart -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
                        <h3 class="text-lg font-semibold text-white mb-6">Revenue by Service</h3>
                        <Chart
                            type="bar"
                            :data="serviceRevenueChartData"
                            :options="barChartOptions"
                            :height="400"
                        />
                    </div>
                </div>

                <!-- Top Services Table -->
                <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-lg">
                    <div class="p-6 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-white">Top Services by Revenue</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Service</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Revenue</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Orders</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Avg. Order Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                <tr v-for="service in data.top_services" :key="service.name" class="hover:bg-gray-700 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">{{ service.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-400 font-semibold">{{ formatCurrency(service.revenue) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ service.orders }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ formatCurrency(service.revenue / service.orders) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
