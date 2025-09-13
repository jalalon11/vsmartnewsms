<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    salesSummary: Object,
    expensesSummary: Object,
    topServices: Array,
    topCustomers: Array,
});

const currentYear = new Date().getFullYear();
const currentMonth = new Date().getMonth() + 1;

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
</script>

<template>
    <Head title="Reports" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-white">
                    Reports & Analytics
                </h2>
                <div class="text-sm text-gray-400">
                    Business insights and financial reports
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- This Month Sales -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-400">This Month Sales</p>
                                <p class="text-2xl font-bold text-white">{{ formatCurrency(salesSummary.thisMonth) }}</p>
                                <p class="text-sm mt-1" :class="getGrowthColor(salesSummary.monthlyGrowth)">
                                    {{ formatPercentage(salesSummary.monthlyGrowth) }} from last month
                                </p>
                            </div>
                            <div class="p-3 bg-green-900/50 rounded-lg">
                                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- This Year Sales -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-400">This Year Sales</p>
                                <p class="text-2xl font-bold text-white">{{ formatCurrency(salesSummary.thisYear) }}</p>
                                <p class="text-sm mt-1" :class="getGrowthColor(salesSummary.yearlyGrowth)">
                                    {{ formatPercentage(salesSummary.yearlyGrowth) }} from last year
                                </p>
                            </div>
                            <div class="p-3 bg-blue-900/50 rounded-lg">
                                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- This Month Expenses -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-400">This Month Expenses</p>
                                <p class="text-2xl font-bold text-white">{{ formatCurrency(expensesSummary.thisMonth) }}</p>
                                <p class="text-sm mt-1" :class="getGrowthColor(-expensesSummary.monthlyChange)">
                                    {{ formatPercentage(expensesSummary.monthlyChange) }} from last month
                                </p>
                            </div>
                            <div class="p-3 bg-red-900/50 rounded-lg">
                                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- This Year Expenses -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-400">This Year Expenses</p>
                                <p class="text-2xl font-bold text-white">{{ formatCurrency(expensesSummary.thisYear) }}</p>
                                <p class="text-sm mt-1" :class="getGrowthColor(-expensesSummary.yearlyChange)">
                                    {{ formatPercentage(expensesSummary.yearlyChange) }} from last year
                                </p>
                            </div>
                            <div class="p-3 bg-orange-900/50 rounded-lg">
                                <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Actions -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Sales Reports -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <div class="flex items-center mb-4">
                            <div class="p-2 bg-green-900/50 rounded-lg mr-3">
                                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">Sales Reports</h3>
                                <p class="text-sm text-gray-400">Revenue analysis and trends</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <Link :href="route('reports.revenue')"
                                  class="block w-full bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-center transition-colors duration-200">
                                Revenue Report
                            </Link>
                            <Link :href="route('reports.orders')"
                                  class="block w-full bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-center transition-colors duration-200">
                                Orders Report
                            </Link>
                        </div>
                    </div>

                    <!-- Expenses Reports -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <div class="flex items-center mb-4">
                            <div class="p-2 bg-red-900/50 rounded-lg mr-3">
                                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">Expenses Reports</h3>
                                <p class="text-sm text-gray-400">Parts costs and expenses</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <Link :href="route('reports.customers')"
                                  class="block w-full bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-center transition-colors duration-200">
                                Customers Report
                            </Link>
                            <Link :href="route('reports.services')"
                                  class="block w-full bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-center transition-colors duration-200">
                                Services Report
                            </Link>
                        </div>
                    </div>

                    <!-- Profit Analysis -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <div class="flex items-center mb-4">
                            <div class="p-2 bg-blue-900/50 rounded-lg mr-3">
                                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">Profit Analysis</h3>
                                <p class="text-sm text-gray-400">Revenue vs expenses</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <Link :href="route('reports.profit-analysis', { period: 'monthly', year: currentYear, month: currentMonth })" 
                                  class="block w-full bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-center transition-colors duration-200">
                                Current Month Analysis
                            </Link>
                            <Link :href="route('reports.profit-analysis', { period: 'yearly', year: currentYear })" 
                                  class="block w-full bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-center transition-colors duration-200">
                                Current Year Analysis
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Top Services and Customers -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Top Services -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <h3 class="text-lg font-semibold text-white mb-4">Top Services by Revenue</h3>
                        <div class="space-y-3">
                            <div v-for="service in topServices" :key="service.name" class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                                <div>
                                    <p class="font-medium text-white">{{ service.name }}</p>
                                    <p class="text-sm text-gray-400">{{ service.orders_count }} orders</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-green-400">{{ formatCurrency(service.total_revenue) }}</p>
                                </div>
                            </div>
                            <div v-if="topServices.length === 0" class="text-center text-gray-400 py-4">
                                No service data available
                            </div>
                        </div>
                    </div>

                    <!-- Top Customers -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <h3 class="text-lg font-semibold text-white mb-4">Top Customers by Spending</h3>
                        <div class="space-y-3">
                            <div v-for="customer in topCustomers" :key="customer.id" class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                                <div>
                                    <p class="font-medium text-white">{{ customer.first_name }} {{ customer.last_name }}</p>
                                    <p class="text-sm text-gray-400">{{ customer.orders_count }} orders</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-green-400">{{ formatCurrency(customer.total_spent) }}</p>
                                </div>
                            </div>
                            <div v-if="topCustomers.length === 0" class="text-center text-gray-400 py-4">
                                No customer data available
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
