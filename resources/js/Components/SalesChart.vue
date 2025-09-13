<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Line } from 'vue-chartjs';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
} from 'chart.js';

// Register Chart.js components
ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
);

const props = defineProps({
    salesData: Object,
});

const activeTab = ref('week');
const chartRef = ref(null);

// Chart data computed based on active tab
const chartData = computed(() => {
    const data = props.salesData?.charts || {};
    
    let labels = [];
    let values = [];
    
    switch (activeTab.value) {
        case 'week':
            labels = data.weekly?.map(item => item.date) || [];
            values = data.weekly?.map(item => item.sales) || [];
            break;
        case 'month':
            labels = data.monthly?.map(item => item.month) || [];
            values = data.monthly?.map(item => item.sales) || [];
            break;
        case 'year':
            labels = data.yearly?.map(item => item.year) || [];
            values = data.yearly?.map(item => item.sales) || [];
            break;
    }

    return {
        labels,
        datasets: [
            {
                label: 'Sales (₱)',
                data: values,
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#ef4444',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8,
            }
        ]
    };
});

// Chart options
const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            borderColor: '#ef4444',
            borderWidth: 1,
            cornerRadius: 8,
            displayColors: false,
            callbacks: {
                label: function(context) {
                    return `Sales: ₱${context.parsed.y.toLocaleString()}`;
                }
            }
        }
    },
    scales: {
        x: {
            grid: {
                color: 'rgba(255, 255, 255, 0.1)',
                borderColor: 'rgba(255, 255, 255, 0.2)',
            },
            ticks: {
                color: '#9ca3af',
                font: {
                    size: 12
                }
            }
        },
        y: {
            grid: {
                color: 'rgba(255, 255, 255, 0.1)',
                borderColor: 'rgba(255, 255, 255, 0.2)',
            },
            ticks: {
                color: '#9ca3af',
                font: {
                    size: 12
                },
                callback: function(value) {
                    return '₱' + value.toLocaleString();
                }
            }
        }
    },
    elements: {
        point: {
            hoverBackgroundColor: '#ef4444',
        }
    }
}));

// Format currency
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount || 0);
};

// Get total for active period
const currentTotal = computed(() => {
    const totals = props.salesData?.totals || {};
    return totals[activeTab.value] || 0;
});

// Tab configuration
const tabs = [
    { key: 'week', label: 'This Week', icon: '📅' },
    { key: 'month', label: 'This Month', icon: '📊' },
    { key: 'year', label: 'This Year', icon: '📈' }
];
</script>

<template>
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-white mb-1">Sales Analytics</h3>
                <p class="text-sm text-gray-400">Track your revenue performance</p>
            </div>
            <div class="flex items-center space-x-1 bg-gray-800 rounded-lg p-1">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="activeTab = tab.key"
                    :class="[
                        'px-3 py-2 text-xs font-medium rounded-md transition-all duration-200',
                        activeTab === tab.key
                            ? 'bg-red-600 text-white shadow-lg'
                            : 'text-gray-400 hover:text-white hover:bg-gray-700'
                    ]"
                >
                    {{ tab.icon }} {{ tab.label }}
                </button>
            </div>
        </div>

        <!-- Current Total -->
        <div class="mb-6">
            <div class="text-center">
                <p class="text-sm text-gray-400 mb-1">{{ tabs.find(t => t.key === activeTab)?.label }} Total</p>
                <p class="text-3xl font-bold text-white">{{ formatCurrency(currentTotal) }}</p>
            </div>
        </div>

        <!-- Chart -->
        <div class="h-64">
            <Line
                ref="chartRef"
                :data="chartData"
                :options="chartOptions"
            />
        </div>
    </div>
</template>
