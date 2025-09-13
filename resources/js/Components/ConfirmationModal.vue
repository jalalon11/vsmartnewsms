<script setup>
import { ref } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Confirm Action',
    },
    message: {
        type: String,
        default: 'Are you sure you want to proceed?',
    },
    confirmText: {
        type: String,
        default: 'Confirm',
    },
    cancelText: {
        type: String,
        default: 'Cancel',
    },
    type: {
        type: String,
        default: 'danger', // danger, warning, info
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['confirm', 'cancel']);

const getIconColor = () => {
    switch (props.type) {
        case 'danger':
            return 'text-red-500';
        case 'warning':
            return 'text-yellow-500';
        case 'info':
            return 'text-blue-500';
        default:
            return 'text-red-500';
    }
};

const getButtonColor = () => {
    switch (props.type) {
        case 'danger':
            return 'bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800';
        case 'warning':
            return 'bg-gradient-to-r from-yellow-600 to-yellow-700 hover:from-yellow-700 hover:to-yellow-800';
        case 'info':
            return 'bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800';
        default:
            return 'bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800';
    }
};

const handleConfirm = () => {
    emit('confirm');
};

const handleCancel = () => {
    emit('cancel');
};
</script>

<template>
    <!-- Modal Overlay -->
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" @click="handleCancel">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-black bg-opacity-75 backdrop-blur-sm"></div>

            <!-- Modal -->
            <div 
                class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-2xl rounded-2xl"
                @click.stop
            >
                <!-- Icon -->
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full bg-gray-800">
                    <svg v-if="type === 'danger'" class="w-6 h-6" :class="getIconColor()" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <svg v-else-if="type === 'warning'" class="w-6 h-6" :class="getIconColor()" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg v-else class="w-6 h-6" :class="getIconColor()" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <!-- Content -->
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-white mb-2">
                        {{ title }}
                    </h3>
                    <p class="text-sm text-gray-400 mb-6">
                        {{ message }}
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-center space-x-4">
                    <button
                        type="button"
                        @click="handleCancel"
                        :disabled="processing"
                        class="px-6 py-2 border border-gray-600 rounded-lg text-gray-300 hover:bg-gray-700 disabled:opacity-50 transition-colors duration-200 font-medium"
                    >
                        {{ cancelText }}
                    </button>
                    <button
                        type="button"
                        @click="handleConfirm"
                        :disabled="processing"
                        class="px-6 py-2 text-white rounded-lg disabled:opacity-50 transition-all duration-200 shadow-lg hover:shadow-xl font-medium"
                        :class="getButtonColor()"
                    >
                        <span v-if="processing" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                        <span v-else>
                            {{ confirmText }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
