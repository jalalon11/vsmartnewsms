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

const formatPercentage = (value) => {
    return `${value.toFixed(1)}%`;
};

const formatPhone = (phone) => {
    if (!phone) return 'N/A';
    // Format Philippine phone numbers
    if (phone.startsWith('+63')) {
        return phone;
    } else if (phone.startsWith('63')) {
        return '+' + phone;
    } else if (phone.startsWith('09')) {
        return '+63' + phone.substring(1);
    }
    return phone;
};

// Chart data
const customersChartData = computed(() => {
    const chartData = props.charts.daily_growth || [];
    return {
        labels: chartData.map(item => formatDate(item.date)),
        datasets: [{
            label: 'New Customers',
            data: chartData.map(item => parseInt(item.count || 0)),
            borderColor: 'rgb(139, 92, 246)',
            backgroundColor: 'rgba(139, 92, 246, 0.1)',
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
    window.location.href = route('reports.customers', { period: newPeriod });
};
</script>

<template>
    <Head title="Customers Analytics" />

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
                            Customers Analytics
                        </h2>
                        <p class="text-sm text-gray-400 mt-1">
                            Customer insights and relationship metrics
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
                <!-- Customer Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Total Customers</p>
                                <p class="text-3xl font-bold mt-1">{{ data.total_customers?.toLocaleString() }}</p>
                            </div>
                            <div class="p-3 bg-purple-500 bg-opacity-30 rounded-lg">
                                <svg class="w-8 h-8 text-purple-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">New Customers</p>
                                <p class="text-3xl font-bold mt-1">{{ data.new_customers?.toLocaleString() }}</p>
                            </div>
                            <div class="p-3 bg-green-500 bg-opacity-30 rounded-lg">
                                <svg class="w-8 h-8 text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Customer Retention</p>
                                <p class="text-3xl font-bold mt-1">{{ formatPercentage(data.customer_retention) }}</p>
                            </div>
                            <div class="p-3 bg-blue-500 bg-opacity-30 rounded-lg">
                                <svg class="w-8 h-8 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm font-medium">Top Customer Value</p>
                                <p class="text-2xl font-bold mt-1">{{ formatCurrency(data.top_customers?.[0]?.revenue || 0) }}</p>
                                <p class="text-orange-100 text-xs mt-1">{{ data.top_customers?.[0]?.name || 'N/A' }}</p>
                            </div>
                            <div class="p-3 bg-orange-500 bg-opacity-30 rounded-lg">
                                <svg class="w-8 h-8 text-orange-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 gap-6 mb-8">
                    <!-- Customer Growth Chart -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
                        <h3 class="text-lg font-semibold text-white mb-6">Customer Growth Trend</h3>
                        <Chart
                            type="line"
                            :data="customersChartData"
                            :options="chartOptions"
                            :height="400"
                        />
                    </div>
                </div>

                <!-- Top Customers Table -->
                <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-lg">
                    <div class="p-6 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-white">Top Customers by Revenue</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Phone</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total Revenue</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total Orders</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Avg. Order Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                <tr v-for="customer in data.top_customers" :key="customer.id" class="hover:bg-gray-700 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-purple-600 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-white">
                                                        {{ customer.name?.charAt(0)?.toUpperCase() || 'N' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-white">{{ customer.name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ formatPhone(customer.phone) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-400 font-semibold">{{ formatCurrency(customer.revenue) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ customer.orders }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ formatCurrency(customer.revenue / customer.orders) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
