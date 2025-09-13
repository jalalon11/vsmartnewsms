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

const formatPercentage = (value) => {
    return `${value.toFixed(1)}%`;
};

// Chart data
const servicePopularityChartData = computed(() => {
    const chartData = props.charts.popularity || [];
    return {
        labels: chartData.map(item => item.name),
        datasets: [{
            label: 'Orders Count',
            data: chartData.map(item => parseInt(item.count || 0)),
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

const changePeriod = (newPeriod) => {
    window.location.href = route('reports.services', { period: newPeriod });
};

const getPerformanceColor = (revenue, basePrice) => {
    const ratio = revenue / (basePrice || 1);
    if (ratio >= 10) return 'text-green-400';
    if (ratio >= 5) return 'text-blue-400';
    if (ratio >= 2) return 'text-yellow-400';
    return 'text-gray-400';
};

const getPerformanceBadge = (revenue, basePrice) => {
    const ratio = revenue / (basePrice || 1);
    if (ratio >= 10) return { text: 'Excellent', class: 'bg-green-100 text-green-800' };
    if (ratio >= 5) return { text: 'Good', class: 'bg-blue-100 text-blue-800' };
    if (ratio >= 2) return { text: 'Average', class: 'bg-yellow-100 text-yellow-800' };
    return { text: 'Low', class: 'bg-gray-100 text-gray-800' };
};
</script>

<template>
    <Head title="Services Analytics" />

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
                            Services Analytics
                        </h2>
                        <p class="text-sm text-gray-400 mt-1">
                            Service performance and popularity metrics
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
                <!-- Services Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm font-medium">Total Services</p>
                                <p class="text-3xl font-bold mt-1">{{ data.total_services?.toLocaleString() }}</p>
                            </div>
                            <div class="p-3 bg-orange-500 bg-opacity-30 rounded-lg">
                                <svg class="w-8 h-8 text-orange-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Active Services</p>
                                <p class="text-3xl font-bold mt-1">{{ data.active_services?.toLocaleString() }}</p>
                            </div>
                            <div class="p-3 bg-green-500 bg-opacity-30 rounded-lg">
                                <svg class="w-8 h-8 text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Service Utilization</p>
                                <p class="text-3xl font-bold mt-1">{{ formatPercentage((data.active_services / data.total_services) * 100) }}</p>
                            </div>
                            <div class="p-3 bg-blue-500 bg-opacity-30 rounded-lg">
                                <svg class="w-8 h-8 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Top Service Revenue</p>
                                <p class="text-2xl font-bold mt-1">{{ formatCurrency(data.service_performance?.[0]?.revenue || 0) }}</p>
                                <p class="text-purple-100 text-xs mt-1">{{ data.service_performance?.[0]?.name || 'N/A' }}</p>
                            </div>
                            <div class="p-3 bg-purple-500 bg-opacity-30 rounded-lg">
                                <svg class="w-8 h-8 text-purple-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 gap-6 mb-8">
                    <!-- Service Popularity Chart -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
                        <h3 class="text-lg font-semibold text-white mb-6">Most Popular Services</h3>
                        <Chart
                            type="bar"
                            :data="servicePopularityChartData"
                            :options="chartOptions"
                            :height="400"
                        />
                    </div>
                </div>

                <!-- Service Performance Table -->
                <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-lg">
                    <div class="p-6 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-white">Service Performance Analysis</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Service</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Base Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total Revenue</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Orders</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Avg. Revenue/Order</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Performance</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                <tr v-for="service in data.service_performance" :key="service.name" class="hover:bg-gray-700 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-orange-600 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-white">{{ service.name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ formatCurrency(service.price) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold" :class="getPerformanceColor(service.revenue, service.price)">
                                        {{ formatCurrency(service.revenue) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ service.orders }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ service.orders > 0 ? formatCurrency(service.revenue / service.orders) : '₱0.00' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                              :class="getPerformanceBadge(service.revenue, service.price).class">
                                            {{ getPerformanceBadge(service.revenue, service.price).text }}
                                        </span>
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
