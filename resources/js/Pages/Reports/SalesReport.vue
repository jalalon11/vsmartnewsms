<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
    reportData: Object,
    period: String,
    year: Number,
    month: Number,
});

const chartRef = ref(null);
let chartInstance = null;

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

const getMonthName = (month) => {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return months[month - 1];
};

const createChart = () => {
    if (chartInstance) {
        chartInstance.destroy();
    }

    const ctx = chartRef.value.getContext('2d');
    
    let labels, data;
    
    if (props.period === 'monthly') {
        labels = props.reportData.dailySales.map(item => item.day);
        data = props.reportData.dailySales.map(item => item.revenue);
    } else {
        labels = props.reportData.monthlySales.map(item => getMonthName(item.month));
        data = props.reportData.monthlySales.map(item => item.revenue);
    }

    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue',
                data: data,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: '#ffffff'
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: '#9ca3af'
                    },
                    grid: {
                        color: 'rgba(156, 163, 175, 0.1)'
                    }
                },
                y: {
                    ticks: {
                        color: '#9ca3af',
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    },
                    grid: {
                        color: 'rgba(156, 163, 175, 0.1)'
                    }
                }
            }
        }
    });
};

onMounted(() => {
    createChart();
});
</script>

<template>
    <Head :title="`Sales Report - ${period === 'monthly' ? getMonthName(month) + ' ' + year : year}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-white">
                        Sales Report
                    </h2>
                    <p class="text-sm text-gray-400 mt-1">
                        {{ period === 'monthly' ? `${getMonthName(month)} ${year}` : `Year ${year}` }}
                    </p>
                </div>
                <Link :href="route('reports.index')" 
                      class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    Back to Reports
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-900/50 rounded-lg">
                                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Total Revenue</p>
                                <p class="text-2xl font-bold text-white">{{ formatCurrency(reportData.totalRevenue) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-900/50 rounded-lg">
                                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Total Orders</p>
                                <p class="text-2xl font-bold text-white">{{ reportData.totalOrders }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Chart -->
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-4">
                        Revenue Trend - {{ period === 'monthly' ? 'Daily' : 'Monthly' }}
                    </h3>
                    <div class="h-96">
                        <canvas ref="chartRef"></canvas>
                    </div>
                </div>

                <!-- Service Breakdown -->
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-4">Service Revenue Breakdown</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Service</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Orders</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Revenue</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Avg. Order Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                <tr v-for="service in reportData.serviceBreakdown" :key="service.name" class="hover:bg-gray-750">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-white">{{ service.name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-300">{{ service.orders_count }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-green-400">{{ formatCurrency(service.revenue) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-300">{{ formatCurrency(service.revenue / service.orders_count) }}</div>
                                    </td>
                                </tr>
                                <tr v-if="reportData.serviceBreakdown.length === 0">
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-400">
                                        No service data available for this period
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Period Navigation -->
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-4">View Other Periods</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-400 mb-2">Monthly Reports</h4>
                            <div class="space-y-2">
                                <Link v-for="monthNum in 12" :key="monthNum"
                                      :href="route('reports.revenue')"
                                      :class="[
                                          'block px-3 py-2 rounded text-sm transition-colors duration-200',
                                          period === 'monthly' && month === monthNum
                                              ? 'bg-red-600 text-white'
                                              : 'bg-gray-700 hover:bg-gray-600 text-gray-300'
                                      ]">
                                    {{ getMonthName(monthNum) }} {{ year }}
                                </Link>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-400 mb-2">Yearly Reports</h4>
                            <div class="space-y-2">
                                <Link v-for="yearNum in [year - 2, year - 1, year, year + 1]" :key="yearNum"
                                      :href="route('reports.orders')"
                                      :class="[
                                          'block px-3 py-2 rounded text-sm transition-colors duration-200',
                                          period === 'yearly' && year === yearNum
                                              ? 'bg-red-600 text-white'
                                              : 'bg-gray-700 hover:bg-gray-600 text-gray-300'
                                      ]">
                                    Year {{ yearNum }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
