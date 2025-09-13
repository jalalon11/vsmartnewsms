<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    financialData: {
        type: Object,
        default: () => ({
            total_revenue: 0,
            total_costs: 0,
            gross_profit: 0,
            gross_margin: 0,
        })
    },
    profitLoss: {
        type: Object,
        default: () => ({
            revenue: { labor: 0, parts: 0, total: 0 },
            costs: { parts: 0, total: 0 },
            profit: { gross: 0, labor_profit: 0, parts_profit: 0 },
        })
    },
    cashFlow: {
        type: Object,
        default: () => ({
            cash_inflows: [],
            outstanding_receivables: 0,
            daily_cash_flow: [],
        })
    },
    expenseBreakdown: {
        type: Object,
        default: () => ({
            parts_expenses: [],
            total_parts_cost: 0,
        })
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    dateRange: {
        type: Object,
        default: () => ({
            start: new Date().toISOString(),
            end: new Date().toISOString()
        })
    },
});

const formatCurrency = (amount) => {
    return '₱' + parseFloat(amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2 });
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const formatPercentage = (value) => {
    return `${value.toFixed(1)}%`;
};

// Calculate financial health indicators with null guards
const financialHealth = computed(() => {
    const grossMargin = props.financialData?.gross_margin || 0;
    const grossProfit = props.profitLoss?.profit?.gross || 0;
    const totalRevenue = props.profitLoss?.revenue?.total || 0;
    const profitMargin = grossProfit > 0 && totalRevenue > 0
        ? (grossProfit / totalRevenue) * 100
        : 0;

    let healthScore = 0;
    if (grossMargin >= 50) healthScore += 40;
    else if (grossMargin >= 30) healthScore += 30;
    else if (grossMargin >= 20) healthScore += 20;

    if (profitMargin >= 20) healthScore += 30;
    else if (profitMargin >= 15) healthScore += 25;
    else if (profitMargin >= 10) healthScore += 20;

    const outstandingReceivables = props.cashFlow?.outstanding_receivables || 0;
    if (outstandingReceivables < totalRevenue * 0.1) healthScore += 30;
    else if (outstandingReceivables < totalRevenue * 0.2) healthScore += 20;
    else healthScore += 10;

    return {
        score: Math.min(100, healthScore),
        status: healthScore >= 80 ? 'Excellent' : healthScore >= 60 ? 'Good' : healthScore >= 40 ? 'Fair' : 'Poor',
        grossMargin,
        profitMargin
    };
});

// Cash flow trend data with null guards
const cashFlowTrend = computed(() => {
    return props.cashFlow?.daily_cash_flow?.slice(-7) || [];
});
</script>

<template>
    <Head title="Financial Summary" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-white">
                        Financial Summary
                    </h2>
                    <p class="text-sm text-gray-400 mt-1">
                        {{ formatDate(dateRange.start) }} - {{ formatDate(dateRange.end) }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('reports.index')" 
                          class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        Back to Dashboard
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <!-- Key Financial Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-gradient-to-br from-green-900 to-green-800 border border-green-700 rounded-xl p-6 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-green-200">Total Revenue</p>
                                <p class="text-3xl font-bold text-white">{{ formatCurrency(financialData.total_revenue) }}</p>
                            </div>
                            <div class="p-3 bg-green-800 rounded-lg">
                                <svg class="w-8 h-8 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-red-900 to-red-800 border border-red-700 rounded-xl p-6 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-red-200">Total Costs</p>
                                <p class="text-3xl font-bold text-white">{{ formatCurrency(financialData.total_costs) }}</p>
                            </div>
                            <div class="p-3 bg-red-800 rounded-lg">
                                <svg class="w-8 h-8 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-blue-900 to-blue-800 border border-blue-700 rounded-xl p-6 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-blue-200">Gross Profit</p>
                                <p class="text-3xl font-bold text-white">{{ formatCurrency(financialData.gross_profit) }}</p>
                            </div>
                            <div class="p-3 bg-blue-800 rounded-lg">
                                <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-900 to-purple-800 border border-purple-700 rounded-xl p-6 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-purple-200">Gross Margin</p>
                                <p class="text-3xl font-bold text-white">{{ formatPercentage(financialData.gross_margin) }}</p>
                            </div>
                            <div class="p-3 bg-purple-800 rounded-lg">
                                <svg class="w-8 h-8 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Health Score -->
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                    <h3 class="text-lg font-semibold text-white mb-6">Financial Health Score</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                        <div class="lg:col-span-1">
                            <div class="text-center">
                                <div class="relative w-32 h-32 mx-auto mb-4">
                                    <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 36 36">
                                        <path class="text-gray-600" stroke="currentColor" stroke-width="3" fill="none"
                                              d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                        <path :class="financialHealth.score >= 80 ? 'text-green-400' : financialHealth.score >= 60 ? 'text-yellow-400' : 'text-red-400'" 
                                              stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round"
                                              :stroke-dasharray="`${financialHealth.score}, 100`"
                                              d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    </svg>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-white">{{ financialHealth.score.toFixed(0) }}</div>
                                            <div class="text-xs text-gray-400">Score</div>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-lg font-semibold" :class="financialHealth.score >= 80 ? 'text-green-400' : financialHealth.score >= 60 ? 'text-yellow-400' : 'text-red-400'">
                                    {{ financialHealth.status }}
                                </p>
                            </div>
                        </div>
                        <div class="lg:col-span-3 space-y-4">
                            <div class="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
                                <span class="text-white">Gross Margin</span>
                                <span class="text-green-400 font-semibold">{{ formatPercentage(financialHealth.grossMargin) }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
                                <span class="text-white">Profit Margin</span>
                                <span class="text-blue-400 font-semibold">{{ formatPercentage(financialHealth.profitMargin) }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
                                <span class="text-white">Outstanding Receivables</span>
                                <span class="text-yellow-400 font-semibold">{{ formatCurrency(cashFlow.outstanding_receivables) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profit & Loss Analysis -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Revenue Breakdown -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <h3 class="text-lg font-semibold text-white mb-6">Revenue Breakdown</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-blue-500 rounded mr-3"></div>
                                    <span class="text-white">Labor Revenue</span>
                                </div>
                                <span class="text-white font-semibold">{{ formatCurrency(profitLoss.revenue.labor) }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-green-500 rounded mr-3"></div>
                                    <span class="text-white">Parts Revenue</span>
                                </div>
                                <span class="text-white font-semibold">{{ formatCurrency(profitLoss.revenue.parts) }}</span>
                            </div>
                            <div class="pt-4 border-t border-gray-600">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-400">Total Revenue</span>
                                    <span class="text-white font-bold text-lg">{{ formatCurrency(profitLoss.revenue.total) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cost & Profit Analysis -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                        <h3 class="text-lg font-semibold text-white mb-6">Cost & Profit Analysis</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
                                <span class="text-white">Parts Cost</span>
                                <span class="text-red-400 font-semibold">{{ formatCurrency(profitLoss.costs.parts) }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
                                <span class="text-white">Labor Profit</span>
                                <span class="text-green-400 font-semibold">{{ formatCurrency(profitLoss.profit.labor_profit) }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
                                <span class="text-white">Parts Profit</span>
                                <span class="text-green-400 font-semibold">{{ formatCurrency(profitLoss.profit.parts_profit) }}</span>
                            </div>
                            <div class="pt-4 border-t border-gray-600">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-400">Gross Profit</span>
                                    <span class="text-green-400 font-bold text-lg">{{ formatCurrency(profitLoss.profit.gross) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cash Flow Analysis -->
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                    <h3 class="text-lg font-semibold text-white mb-6">Cash Flow Analysis</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Payment Methods -->
                        <div>
                            <h4 class="text-md font-semibold text-white mb-4">Cash Inflows by Method</h4>
                            <div class="space-y-3">
                                <div v-for="inflow in cashFlow.cash_inflows" :key="inflow.payment_method" 
                                     class="flex justify-between items-center p-3 bg-gray-700 rounded-lg">
                                    <div>
                                        <p class="text-white font-medium">{{ inflow.payment_method.replace('_', ' ').toUpperCase() }}</p>
                                        <p class="text-gray-400 text-sm">{{ inflow.transaction_count }} transactions</p>
                                    </div>
                                    <span class="text-green-400 font-semibold">{{ formatCurrency(inflow.total_amount) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Outstanding Receivables -->
                        <div>
                            <h4 class="text-md font-semibold text-white mb-4">Outstanding Receivables</h4>
                            <div class="text-center p-6 bg-gray-700 rounded-lg">
                                <p class="text-3xl font-bold text-yellow-400">{{ formatCurrency(cashFlow.outstanding_receivables) }}</p>
                                <p class="text-gray-400 text-sm mt-2">Amount pending collection</p>
                            </div>
                        </div>

                        <!-- Cash Flow Trend -->
                        <div>
                            <h4 class="text-md font-semibold text-white mb-4">Recent Cash Flow (7 Days)</h4>
                            <div class="space-y-2">
                                <div v-for="day in cashFlowTrend" :key="day.date" 
                                     class="flex justify-between items-center p-2 bg-gray-700 rounded">
                                    <span class="text-gray-400 text-sm">{{ new Date(day.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }}</span>
                                    <span class="text-green-400 font-medium text-sm">{{ formatCurrency(day.inflow) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expense Breakdown -->
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-xl">
                    <h3 class="text-lg font-semibold text-white mb-6">Expense Breakdown by Category</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="expense in expenseBreakdown.parts_expenses" :key="expense.category" 
                             class="bg-gray-700 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-white font-medium">{{ expense.category }}</h4>
                                <span class="text-sm text-gray-400">{{ expense.total_quantity }} units</span>
                            </div>
                            <p class="text-2xl font-bold text-red-400">{{ formatCurrency(expense.total_cost) }}</p>
                            <div class="mt-2 bg-gray-600 rounded-full h-2">
                                <div class="bg-red-500 h-2 rounded-full" 
                                     :style="{ width: expenseBreakdown.total_parts_cost > 0 ? (expense.total_cost / expenseBreakdown.total_parts_cost * 100) + '%' : '0%' }"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-700">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Total Parts Expenses</span>
                            <span class="text-red-400 font-bold text-xl">{{ formatCurrency(expenseBreakdown.total_parts_cost) }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Custom styles for financial summary */
</style>
