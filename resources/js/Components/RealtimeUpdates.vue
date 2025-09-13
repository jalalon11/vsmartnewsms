<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    enabled: {
        type: Boolean,
        default: true
    },
    interval: {
        type: Number,
        default: 30000 // 30 seconds
    },
    endpoint: {
        type: String,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['dataUpdated', 'error']);

const isUpdating = ref(false);
const lastUpdated = ref(null);
const updateInterval = ref(null);
const isEnabled = ref(props.enabled);
const connectionStatus = ref('connected');
const updateCount = ref(0);

// Computed properties
const timeSinceLastUpdate = computed(() => {
    if (!lastUpdated.value) return 'Never';
    
    const now = new Date();
    const diff = Math.floor((now - lastUpdated.value) / 1000);
    
    if (diff < 60) return `${diff}s ago`;
    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
    return `${Math.floor(diff / 3600)}h ago`;
});

const statusColor = computed(() => {
    switch (connectionStatus.value) {
        case 'connected': return 'text-green-400';
        case 'updating': return 'text-blue-400';
        case 'error': return 'text-red-400';
        case 'disconnected': return 'text-gray-400';
        default: return 'text-gray-400';
    }
});

const statusIcon = computed(() => {
    switch (connectionStatus.value) {
        case 'connected': return 'M5 13l4 4L19 7';
        case 'updating': return 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15';
        case 'error': return 'M6 18L18 6M6 6l12 12';
        case 'disconnected': return 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364';
        default: return 'M12 8v4m0 4h.01';
    }
});

// Methods
const updateData = async () => {
    if (isUpdating.value || !isEnabled.value) return;
    
    isUpdating.value = true;
    connectionStatus.value = 'updating';
    
    try {
        // Use Inertia to reload data
        await new Promise((resolve, reject) => {
            router.reload({
                only: ['overview', 'salesMetrics', 'expenseMetrics', 'profitabilityMetrics', 'topPerformers', 'recentTrends'],
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    lastUpdated.value = new Date();
                    updateCount.value++;
                    connectionStatus.value = 'connected';
                    emit('dataUpdated', {
                        timestamp: lastUpdated.value,
                        updateCount: updateCount.value
                    });
                    resolve();
                },
                onError: (errors) => {
                    connectionStatus.value = 'error';
                    emit('error', errors);
                    reject(errors);
                }
            });
        });
    } catch (error) {
        console.error('Failed to update data:', error);
        connectionStatus.value = 'error';
        emit('error', error);
    } finally {
        isUpdating.value = false;
    }
};

const startUpdates = () => {
    if (updateInterval.value) {
        clearInterval(updateInterval.value);
    }
    
    updateInterval.value = setInterval(updateData, props.interval);
    isEnabled.value = true;
    connectionStatus.value = 'connected';
};

const stopUpdates = () => {
    if (updateInterval.value) {
        clearInterval(updateInterval.value);
        updateInterval.value = null;
    }
    isEnabled.value = false;
    connectionStatus.value = 'disconnected';
};

const toggleUpdates = () => {
    if (isEnabled.value) {
        stopUpdates();
    } else {
        startUpdates();
    }
};

const forceUpdate = () => {
    updateData();
};

// Lifecycle
onMounted(() => {
    if (props.enabled) {
        startUpdates();
        // Initial update after a short delay
        setTimeout(updateData, 1000);
    }
});

onUnmounted(() => {
    stopUpdates();
});

// Watch for visibility changes to pause/resume updates
const handleVisibilityChange = () => {
    if (document.hidden) {
        stopUpdates();
    } else if (props.enabled) {
        startUpdates();
    }
};

onMounted(() => {
    document.addEventListener('visibilitychange', handleVisibilityChange);
});

onUnmounted(() => {
    document.removeEventListener('visibilitychange', handleVisibilityChange);
});
</script>

<template>
    <div class="flex items-center space-x-3 text-sm">
        <!-- Status Indicator -->
        <div class="flex items-center space-x-2">
            <div class="relative">
                <svg 
                    class="w-4 h-4 transition-colors duration-200" 
                    :class="[statusColor, { 'animate-spin': connectionStatus === 'updating' }]"
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="statusIcon" />
                </svg>
                
                <!-- Pulse animation for connected state -->
                <div 
                    v-if="connectionStatus === 'connected'" 
                    class="absolute -top-1 -right-1 w-2 h-2 bg-green-400 rounded-full animate-pulse"
                ></div>
            </div>
            
            <span class="text-gray-400">
                {{ connectionStatus === 'connected' ? 'Live' : 
                   connectionStatus === 'updating' ? 'Updating...' : 
                   connectionStatus === 'error' ? 'Error' : 'Paused' }}
            </span>
        </div>

        <!-- Last Updated -->
        <span class="text-gray-500 text-xs">
            Updated {{ timeSinceLastUpdate }}
        </span>

        <!-- Update Count -->
        <span v-if="updateCount > 0" class="text-gray-500 text-xs">
            ({{ updateCount }} updates)
        </span>

        <!-- Controls -->
        <div class="flex items-center space-x-2">
            <!-- Toggle Button -->
            <button
                @click="toggleUpdates"
                :class="[
                    'px-2 py-1 rounded text-xs font-medium transition-colors',
                    isEnabled 
                        ? 'bg-green-600 hover:bg-green-700 text-white' 
                        : 'bg-gray-600 hover:bg-gray-500 text-white'
                ]"
                :title="isEnabled ? 'Pause auto-updates' : 'Resume auto-updates'"
            >
                {{ isEnabled ? 'Pause' : 'Resume' }}
            </button>

            <!-- Manual Update Button -->
            <button
                @click="forceUpdate"
                :disabled="isUpdating"
                class="px-2 py-1 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 text-white rounded text-xs font-medium transition-colors"
                title="Update now"
            >
                <svg v-if="isUpdating" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </div>

        <!-- Settings Dropdown -->
        <div class="relative">
            <button
                @click="$emit('showSettings')"
                class="p-1 text-gray-400 hover:text-white transition-colors"
                title="Update settings"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </button>
        </div>
    </div>
</template>
