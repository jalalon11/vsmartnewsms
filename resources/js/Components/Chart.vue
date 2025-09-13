<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    TimeScale,
} from 'chart.js';
import 'chartjs-adapter-date-fns';

// Register Chart.js components
ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    TimeScale
);

const props = defineProps({
    type: {
        type: String,
        required: true,
        validator: (value) => ['line', 'bar', 'doughnut', 'pie'].includes(value)
    },
    data: {
        type: Object,
        required: true
    },
    options: {
        type: Object,
        default: () => ({})
    },
    height: {
        type: Number,
        default: 400
    },
    responsive: {
        type: Boolean,
        default: true
    }
});

const chartCanvas = ref(null);
let chartInstance = null;

const defaultOptions = {
    responsive: props.responsive,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: {
                color: '#D1D5DB', // gray-300
                font: {
                    family: 'Inter, system-ui, sans-serif'
                }
            }
        },
        tooltip: {
            backgroundColor: '#1F2937', // gray-800
            titleColor: '#F9FAFB', // gray-50
            bodyColor: '#D1D5DB', // gray-300
            borderColor: '#374151', // gray-700
            borderWidth: 1,
            cornerRadius: 8,
            displayColors: true,
            callbacks: {
                label: function(context) {
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    if (context.parsed.y !== null) {
                        // Format as currency if the value looks like money
                        if (context.parsed.y >= 1000 || context.dataset.label?.toLowerCase().includes('revenue') || 
                            context.dataset.label?.toLowerCase().includes('cost') || 
                            context.dataset.label?.toLowerCase().includes('profit')) {
                            label += '₱' + context.parsed.y.toLocaleString('en-US', { minimumFractionDigits: 2 });
                        } else {
                            label += context.parsed.y.toLocaleString();
                        }
                    }
                    return label;
                }
            }
        }
    },
    scales: {}
};

// Configure scales based on chart type
if (props.type === 'line' || props.type === 'bar') {
    defaultOptions.scales = {
        x: {
            ticks: {
                color: '#9CA3AF', // gray-400
                font: {
                    family: 'Inter, system-ui, sans-serif'
                }
            },
            grid: {
                color: '#374151', // gray-700
                borderColor: '#4B5563' // gray-600
            }
        },
        y: {
            ticks: {
                color: '#9CA3AF', // gray-400
                font: {
                    family: 'Inter, system-ui, sans-serif'
                },
                callback: function(value) {
                    // Format large numbers as currency
                    if (value >= 1000) {
                        return '₱' + value.toLocaleString('en-US', { minimumFractionDigits: 0 });
                    }
                    return value;
                }
            },
            grid: {
                color: '#374151', // gray-700
                borderColor: '#4B5563' // gray-600
            }
        }
    };
}

const createChart = () => {
    if (chartInstance) {
        chartInstance.destroy();
    }

    const ctx = chartCanvas.value.getContext('2d');
    const mergedOptions = {
        ...defaultOptions,
        ...props.options
    };

    chartInstance = new ChartJS(ctx, {
        type: props.type,
        data: props.data,
        options: mergedOptions
    });
};

const updateChart = () => {
    if (chartInstance) {
        chartInstance.data = props.data;
        chartInstance.update('none'); // No animation for updates
    }
};

onMounted(async () => {
    await nextTick();
    createChart();
});

onUnmounted(() => {
    if (chartInstance) {
        chartInstance.destroy();
    }
});

// Watch for data changes
watch(() => props.data, () => {
    updateChart();
}, { deep: true });

// Watch for options changes
watch(() => props.options, () => {
    createChart();
}, { deep: true });
</script>

<template>
    <div class="relative" :style="{ height: height + 'px' }">
        <canvas 
            ref="chartCanvas"
            class="w-full h-full"
        ></canvas>
    </div>
</template>

<style scoped>
/* Ensure canvas is properly sized */
canvas {
    max-width: 100%;
    max-height: 100%;
}
</style>
