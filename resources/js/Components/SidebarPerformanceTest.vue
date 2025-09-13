<script setup>
import { ref, onMounted } from 'vue';

const performanceMetrics = ref({
    toggleTime: 0,
    renderTime: 0,
    memoryUsage: 0,
    fps: 0
});

const isVisible = ref(false);

// FPS counter
let frameCount = 0;
let lastTime = performance.now();

const measureFPS = () => {
    frameCount++;
    const currentTime = performance.now();
    
    if (currentTime - lastTime >= 1000) {
        performanceMetrics.value.fps = Math.round((frameCount * 1000) / (currentTime - lastTime));
        frameCount = 0;
        lastTime = currentTime;
    }
    
    if (isVisible.value) {
        requestAnimationFrame(measureFPS);
    }
};

// Measure sidebar toggle performance
const measureTogglePerformance = () => {
    const startTime = performance.now();
    
    // Simulate sidebar toggle
    const sidebar = document.querySelector('.sidebar-optimized');
    if (sidebar) {
        sidebar.classList.toggle('w-16');
        sidebar.classList.toggle('w-64');
    }
    
    // Measure after next frame
    requestAnimationFrame(() => {
        const endTime = performance.now();
        performanceMetrics.value.toggleTime = Math.round((endTime - startTime) * 100) / 100;
    });
};

// Measure memory usage
const measureMemory = () => {
    if (performance.memory) {
        performanceMetrics.value.memoryUsage = Math.round(performance.memory.usedJSHeapSize / 1024 / 1024 * 100) / 100;
    }
};

// Run performance tests
const runTests = () => {
    measureTogglePerformance();
    measureMemory();
    measureFPS();
};

onMounted(() => {
    isVisible.value = true;
    measureFPS();
    
    // Update metrics every 2 seconds
    setInterval(() => {
        measureMemory();
    }, 2000);
});

const toggleVisibility = () => {
    isVisible.value = !isVisible.value;
    if (isVisible.value) {
        measureFPS();
    }
};
</script>

<template>
    <div v-if="isVisible" class="fixed bottom-4 right-4 bg-gray-900 border border-gray-700 rounded-lg p-4 text-white text-xs z-50 max-w-xs">
        <div class="flex items-center justify-between mb-2">
            <h3 class="font-semibold">Sidebar Performance</h3>
            <button @click="toggleVisibility" class="text-gray-400 hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="space-y-1">
            <div class="flex justify-between">
                <span>Toggle Time:</span>
                <span class="font-mono">{{ performanceMetrics.toggleTime }}ms</span>
            </div>
            <div class="flex justify-between">
                <span>Memory:</span>
                <span class="font-mono">{{ performanceMetrics.memoryUsage }}MB</span>
            </div>
            <div class="flex justify-between">
                <span>FPS:</span>
                <span class="font-mono" :class="performanceMetrics.fps >= 60 ? 'text-green-400' : performanceMetrics.fps >= 30 ? 'text-yellow-400' : 'text-red-400'">
                    {{ performanceMetrics.fps }}
                </span>
            </div>
        </div>
        
        <button 
            @click="runTests" 
            class="mt-2 w-full bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs transition-colors"
        >
            Run Tests
        </button>
    </div>
    
    <!-- Toggle button when hidden -->
    <button 
        v-else
        @click="toggleVisibility"
        class="fixed bottom-4 right-4 bg-gray-900 border border-gray-700 rounded-full p-2 text-white hover:bg-gray-800 transition-colors z-50"
        title="Show Performance Metrics"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
    </button>
</template>
