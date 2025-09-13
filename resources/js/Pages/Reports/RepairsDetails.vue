<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Chart from '@/Components/Chart.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    repairsData: {
        type: Object,
        default: () => ({})
    },
    repairsByStatus: {
        type: Object,
        default: () => ({})
    },
    repairsTrend: {
        type: Array,
        default: () => []
    },
    averageRepairTime: {
        type: Number,
        default: 0
    },
    technicianPerformance: {
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

const formatPercentage = (value) => {
    const sign = value >= 0 ? '+' : '';
    return `${sign}${value.toFixed(1)}%`;
};

const getGrowthColor = (value) => {
    return value >= 0 ? 'text-green-400' : 'text-red-400';
};

const getStatusColor = (status) => {
    const colors = {
        'pending': 'text-yellow-400 bg-yellow-400/20',
        'in_progress': 'text-blue-400 bg-blue-400/20',
        'waiting_parts': 'text-orange-400 bg-orange-400/20',
        'completed': 'text-green-400 bg-green-400/20',
        'delivered': 'text-green-400 bg-green-400/20',
        'cancelled': 'text-red-400 bg-red-400/20',
    };
    return colors[status] || 'text-gray-400 bg-gray-400/20';
};

// Chart data for repairs by status
const repairsByStatusChartData = computed(() => {
    const statusData = props.repairsByStatus || {};
    const statuses = Object.keys(statusData);
    const counts = Object.values(statusData);
    
    return {
        labels: statuses.map(status => status.replace('_', ' ').toUpperCase()),
        datasets: [{
            label: 'Repairs by Status',
            data: counts,
            backgroundColor: [
                'rgba(245, 158, 11, 0.8)', // pending - yellow
                'rgba(59, 130, 246, 0.8)', // in_progress - blue
                'rgba(249, 115, 22, 0.8)', // waiting_parts - orange
                'rgba(16, 185, 129, 0.8)', // completed - green
                'rgba(34, 197, 94, 0.8)', // delivered - green
                'rgba(239, 68, 68, 0.8)', // cancelled - red
            ],
            borderColor: [
                'rgb(245, 158, 11)',
                'rgb(59, 130, 246)',
                'rgb(249, 115, 22)',
                'rgb(16, 185, 129)',
                'rgb(34, 197, 94)',
                'rgb(239, 68, 68)',
            ],
            borderWidth: 2,
        }]
    };
});

// Repairs trend chart
const repairsTrendChartData = computed(() => {
    const trendData = props.repairsTrend || [];
    return {
        labels: trendData.map(item => item.date || ''),
        datasets: [{
            label: 'Daily Repairs',
            data: trendData.map(item => parseInt(item.count || 0)),
            borderColor: 'rgb(139, 92, 246)',
            backgroundColor: 'rgba(139, 92, 246, 0.1)',
            tension: 0.4,
            fill: true,
        }]
    };
});

// Technician performance chart
const technicianPerformanceChartData = computed(() => {
    const performanceData = props.technicianPerformance.slice(0, 5) || [];
    return {
        labels: performanceData.map(item => item.technician?.user?.name || 'Unknown'),
        datasets: [{
            label: 'Completed Repairs',
            data: performanceData.map(item => parseInt(item.completed_orders || 0)),
            backgroundColor: 'rgba(16, 185, 129, 0.8)',
            borderColor: 'rgb(16, 185, 129)',
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
    <Head title="Repairs Details" />

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
                            Repairs Details
                        </h2>
                    </div>
                    <p class="text-sm text-gray-400 mt-1">
                        Comprehensive repair analytics and insights
                    </p>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Repairs Overview Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Repairs -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-400">Total Repairs</p>
                                <p class="text-2xl font-bold text-white">
                                    {{ repairsData.total_repairs || 0 }}
                                </p>
                            </div>
                            <div class="p-3 bg-purple-500/20 rounded-lg">
                                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Active Repairs -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-400">Active Repairs</p>
                                <p class="text-2xl font-bold text-white">
                                    {{ repairsData.active_repairs || 0 }}
                                </p>
                            </div>
                            <div class="p-3 bg-blue-500/20 rounded-lg">
                                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Repairs -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-400">Completed Repairs</p>
                                <p class="text-2xl font-bold text-white">
                                    {{ repairsData.completed_repairs || 0 }}
                                </p>
                            </div>
                            <div class="p-3 bg-green-500/20 rounded-lg">
                                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Average Repair Time -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-400">Avg Repair Time</p>
                                <p class="text-2xl font-bold text-white">
                                    {{ Math.round(averageRepairTime) }} days
                                </p>
                            </div>
                            <div class="p-3 bg-yellow-500/20 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Repairs by Status Chart -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-white">Repairs by Status</h3>
                        </div>
                        <Chart
                            type="doughnut"
                            :data="repairsByStatusChartData"
                            :options="chartOptions"
                            :height="300"
                        />
                    </div>

                    <!-- Repairs Trend -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-white">Repairs Trend</h3>
                        </div>
                        <Chart
                            type="line"
                            :data="repairsTrendChartData"
                            :options="chartOptions"
                            :height="300"
                        />
                    </div>
                </div>

                <!-- Technician Performance Chart -->
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-white">Top Technician Performance</h3>
                    </div>
                    <Chart
                        type="bar"
                        :data="technicianPerformanceChartData"
                        :options="chartOptions"
                        :height="300"
                    />
                </div>

                <!-- Technician Performance Table -->
                <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-xl">
                    <div class="px-6 py-4 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-white">Technician Performance</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Technician</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total Orders</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Completed</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Completion Rate</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Avg Time</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                <tr v-for="(technician, index) in technicianPerformance" :key="index" class="hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-600 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-white">
                                                        {{ (technician.technician?.user?.name || 'U').charAt(0).toUpperCase() }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-white">
                                                    {{ technician.technician?.user?.name || 'Unknown' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ technician.total_orders }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ technician.completed_orders }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ Math.round((technician.completed_orders / (technician.total_orders || 1)) * 100) }}%
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ Math.round(technician.avg_completion_time) }} days
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
