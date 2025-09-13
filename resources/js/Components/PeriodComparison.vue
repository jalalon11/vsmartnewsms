<script setup>
import { ref, computed, watch } from 'vue';
import Chart from '@/Components/Chart.vue';

const props = defineProps({
    currentPeriodData: {
        type: Object,
        required: true
    },
    previousPeriodData: {
        type: Object,
        required: true
    },
    currentPeriodLabel: {
        type: String,
        default: 'Current Period'
    },
    previousPeriodLabel: {
        type: String,
        default: 'Previous Period'
    },
    metrics: {
        type: Array,
        default: () => ['revenue', 'orders', 'customers']
    },
    chartType: {
        type: String,
        default: 'bar',
        validator: (value) => ['bar', 'line'].includes(value)
    }
});

// Computed comparison data
const comparisonData = computed(() => {
    return props.metrics.map(metric => {
        const currentValue = props.currentPeriodData[metric] || 0;
        const previousValue = props.previousPeriodData[metric] || 0;
        const change = currentValue - previousValue;
        const percentageChange = previousValue !== 0 ? ((change / previousValue) * 100) : 0;

        return {
            metric,
            current: currentValue,
            previous: previousValue,
            change,
            percentageChange,
            trend: change >= 0 ? 'up' : 'down'
        };
    });
});

// Chart data for comparison
const chartData = computed(() => {
    const labels = props.metrics.map(metric => 
        metric.charAt(0).toUpperCase() + metric.slice(1).replace('_', ' ')
    );
    
    return {
        labels,
        datasets: [
            {
                label: props.currentPeriodLabel,
                data: props.metrics.map(metric => props.currentPeriodData[metric] || 0),
                backgroundColor: 'rgba(239, 68, 68, 0.8)', // red-500
                borderColor: 'rgba(239, 68, 68, 1)',
                borderWidth: 2
            },
            {
                label: props.previousPeriodLabel,
                data: props.metrics.map(metric => props.previousPeriodData[metric] || 0),
                backgroundColor: 'rgba(107, 114, 128, 0.8)', // gray-500
                borderColor: 'rgba(107, 114, 128, 1)',
                borderWidth: 2
            }
        ]
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
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
            cornerRadius: 8
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
                color: '#374151'
            }
        },
        y: {
            ticks: {
                color: '#9CA3AF',
                font: {
                    family: 'Inter, system-ui, sans-serif'
                },
                callback: function(value) {
                    if (value >= 1000) {
                        return '₱' + value.toLocaleString('en-US', { minimumFractionDigits: 0 });
                    }
                    return value;
                }
            },
            grid: {
                color: '#374151'
            }
        }
    }
};

// Helper functions
const formatValue = (value, metric) => {
    if (metric.includes('revenue') || metric.includes('cost') || metric.includes('profit')) {
        return '₱' + parseFloat(value || 0).toLocaleString('en-US', { minimumFractionDigits: 2 });
    }
    return parseFloat(value || 0).toLocaleString();
};

const formatPercentage = (value) => {
    const sign = value >= 0 ? '+' : '';
    return `${sign}${value.toFixed(1)}%`;
};

const getTrendColor = (trend) => {
    return trend === 'up' ? 'text-green-400' : 'text-red-400';
};

const getTrendIcon = (trend) => {
    return trend === 'up' 
        ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' 
        : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6';
};

const getMetricLabel = (metric) => {
    return metric.charAt(0).toUpperCase() + metric.slice(1).replace('_', ' ');
};
</script>

<template>
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white">Period Comparison</h3>
            <div class="text-sm text-gray-400">
                {{ currentPeriodLabel }} vs {{ previousPeriodLabel }}
            </div>
        </div>

        <!-- Comparison Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
                v-for="item in comparisonData"
                :key="item.metric"
                class="bg-gray-900 border border-gray-700 rounded-lg p-4"
            >
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-sm font-medium text-gray-300">
                        {{ getMetricLabel(item.metric) }}
                    </h4>
                    <div class="flex items-center space-x-1">
                        <svg 
                            class="w-4 h-4" 
                            :class="getTrendColor(item.trend)"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path 
                                stroke-linecap="round" 
                                stroke-linejoin="round" 
                                stroke-width="2" 
                                :d="getTrendIcon(item.trend)" 
                            />
                        </svg>
                        <span 
                            class="text-sm font-medium" 
                            :class="getTrendColor(item.trend)"
                        >
                            {{ formatPercentage(item.percentageChange) }}
                        </span>
                    </div>
                </div>

                <div class="space-y-2">
                    <!-- Current Period -->
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-400">{{ currentPeriodLabel }}:</span>
                        <span class="text-sm font-semibold text-white">
                            {{ formatValue(item.current, item.metric) }}
                        </span>
                    </div>

                    <!-- Previous Period -->
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-400">{{ previousPeriodLabel }}:</span>
                        <span class="text-sm text-gray-300">
                            {{ formatValue(item.previous, item.metric) }}
                        </span>
                    </div>

                    <!-- Change -->
                    <div class="flex justify-between items-center pt-2 border-t border-gray-700">
                        <span class="text-xs text-gray-400">Change:</span>
                        <span 
                            class="text-sm font-medium" 
                            :class="getTrendColor(item.trend)"
                        >
                            {{ item.change >= 0 ? '+' : '' }}{{ formatValue(item.change, item.metric) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparison Chart -->
        <div class="bg-gray-900 border border-gray-700 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-300 mb-4">Visual Comparison</h4>
            <div class="h-64">
                <Chart
                    :type="chartType"
                    :data="chartData"
                    :options="chartOptions"
                    :height="256"
                />
            </div>
        </div>

        <!-- Summary -->
        <div class="bg-gray-900 border border-gray-700 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-300 mb-3">Summary</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-400">
                        {{ comparisonData.filter(item => item.trend === 'up').length }}
                    </div>
                    <div class="text-gray-400">Metrics Improved</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-400">
                        {{ comparisonData.filter(item => item.trend === 'down').length }}
                    </div>
                    <div class="text-gray-400">Metrics Declined</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-400">
                        {{ comparisonData.reduce((sum, item) => sum + Math.abs(item.percentageChange), 0).toFixed(1) }}%
                    </div>
                    <div class="text-gray-400">Avg Change</div>
                </div>
            </div>
        </div>
    </div>
</template>
