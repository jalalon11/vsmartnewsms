<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    reportType: {
        type: String,
        required: true,
        validator: (value) => ['revenue', 'orders', 'customers', 'services', 'dashboard'].includes(value)
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    title: {
        type: String,
        default: 'Report'
    }
});

const isExporting = ref(false);
const showExportMenu = ref(false);

const exportFormats = [
    {
        key: 'pdf',
        name: 'PDF Document',
        icon: 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
        description: 'Formatted document with charts'
    },
    {
        key: 'excel',
        name: 'Excel Spreadsheet',
        icon: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2z M8 21l4-7 4 7M3 7l5 8 5-8',
        description: 'Data tables for analysis'
    },
    {
        key: 'csv',
        name: 'CSV File',
        icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        description: 'Raw data for import'
    }
];

const exportReport = async (format) => {
    if (isExporting.value) return;
    
    isExporting.value = true;
    showExportMenu.value = false;
    
    try {
        // Create export URL with filters
        const exportUrl = route(`reports.export.${format}`, {
            type: props.reportType,
            ...props.filters
        });
        
        // Create a temporary link to trigger download
        const link = document.createElement('a');
        link.href = exportUrl;
        link.download = `${props.title}_${format}_${new Date().toISOString().split('T')[0]}.${format}`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Show success notification
        if (window.toast) {
            window.toast.success(`${format.toUpperCase()} export completed successfully!`);
        }
    } catch (error) {
        console.error('Export failed:', error);
        if (window.toast) {
            window.toast.error('Export failed. Please try again.');
        }
    } finally {
        setTimeout(() => {
            isExporting.value = false;
        }, 2000);
    }
};

const toggleExportMenu = () => {
    showExportMenu.value = !showExportMenu.value;
};

// Close menu when clicking outside
const closeMenu = () => {
    showExportMenu.value = false;
};
</script>

<template>
    <div class="relative">
        <!-- Export Button -->
        <button
            @click="toggleExportMenu"
            :disabled="isExporting"
            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm"
        >
            <svg v-if="!isExporting" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <svg v-else class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isExporting ? 'Exporting...' : 'Export' }}
            <svg v-if="!isExporting" class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Export Menu -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="showExportMenu"
                class="absolute right-0 mt-2 w-64 bg-gray-800 border border-gray-700 rounded-lg shadow-xl z-50"
            >
                <div class="p-2">
                    <div class="text-xs text-gray-400 px-3 py-2 border-b border-gray-700">
                        Export {{ title }}
                    </div>
                    <div class="mt-2 space-y-1">
                        <button
                            v-for="format in exportFormats"
                            :key="format.key"
                            @click="exportReport(format.key)"
                            class="w-full text-left px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded-md transition-colors duration-200 flex items-start space-x-3"
                        >
                            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="format.icon" />
                            </svg>
                            <div class="flex-1">
                                <div class="font-medium">{{ format.name }}</div>
                                <div class="text-xs text-gray-400">{{ format.description }}</div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Backdrop -->
        <div
            v-if="showExportMenu"
            @click="closeMenu"
            class="fixed inset-0 z-40"
        ></div>
    </div>
</template>
