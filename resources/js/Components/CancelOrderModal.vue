<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    repairOrder: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close', 'cancel']);

const form = useForm({
    cancellation_reason: '',
    restore_parts: true,
});

const isSubmitting = ref(false);

watch(() => props.show, (newValue) => {
    if (newValue) {
        form.reset();
        form.clearErrors();
    }
});

const submit = () => {
    if (!form.cancellation_reason.trim()) {
        form.setError('cancellation_reason', 'Cancellation reason is required.');
        return;
    }

    isSubmitting.value = true;
    emit('cancel', {
        reason: form.cancellation_reason,
        restore_parts: form.restore_parts
    });
    
    // Reset submitting state after a delay
    setTimeout(() => {
        isSubmitting.value = false;
    }, 1000);
};

const closeModal = () => {
    form.reset();
    form.clearErrors();
    emit('close');
};

const commonReasons = [
    'Customer requested cancellation',
    'Parts not available',
    'Customer not responding',
    'Device beyond repair',
    'Customer found alternative solution',
    'Cost exceeds customer budget',
    'Technical issues preventing repair',
    'Other'
];

const selectReason = (reason) => {
    form.cancellation_reason = reason;
};
</script>

<template>
    <!-- Modal Backdrop -->
    <div
        v-if="show"
        class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        @click.self="closeModal"
    >
        <!-- Modal Content -->
        <div class="bg-gray-900 border border-gray-700 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-red-900 to-red-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-red-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Cancel Repair Order</h2>
                            <p class="text-red-200 text-sm">Order #{{ repairOrder?.order_number }}</p>
                        </div>
                    </div>
                    <button
                        @click="closeModal"
                        class="text-red-200 hover:text-white transition-colors duration-200 p-2 hover:bg-red-700 rounded-lg"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-6 max-h-[60vh] overflow-y-auto">
                <!-- Warning Message -->
                <div class="bg-red-900 border border-red-700 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-red-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <div>
                            <h4 class="text-red-200 font-medium">Warning: This action cannot be undone</h4>
                            <p class="text-red-300 text-sm mt-1">
                                Cancelling this repair order will permanently change its status. 
                                Make sure this is the correct action to take.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Quick Reason Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-3">
                            Common Cancellation Reasons
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <button
                                v-for="reason in commonReasons"
                                :key="reason"
                                type="button"
                                @click="selectReason(reason)"
                                :class="[
                                    'text-left px-3 py-2 rounded-lg border transition-colors duration-200 text-sm',
                                    form.cancellation_reason === reason
                                        ? 'bg-red-600 border-red-500 text-white'
                                        : 'bg-gray-800 border-gray-600 text-gray-300 hover:bg-gray-700 hover:border-gray-500'
                                ]"
                            >
                                {{ reason }}
                            </button>
                        </div>
                    </div>

                    <!-- Custom Reason -->
                    <div>
                        <label for="cancellation_reason" class="block text-sm font-medium text-gray-300 mb-2">
                            Cancellation Reason *
                        </label>
                        <textarea
                            id="cancellation_reason"
                            v-model="form.cancellation_reason"
                            rows="4"
                            class="w-full rounded-lg bg-gray-800 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                            placeholder="Please provide a detailed reason for cancelling this repair order..."
                            required
                        ></textarea>
                        <div v-if="form.errors.cancellation_reason" class="mt-1 text-sm text-red-400">
                            {{ form.errors.cancellation_reason }}
                        </div>
                    </div>

                    <!-- Parts Restoration Option -->
                    <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                        <div class="flex items-start space-x-3">
                            <input
                                id="restore_parts"
                                v-model="form.restore_parts"
                                type="checkbox"
                                class="mt-1 rounded border-gray-600 bg-gray-700 text-red-600 focus:ring-red-500 focus:ring-offset-gray-900"
                            />
                            <div class="flex-1">
                                <label for="restore_parts" class="text-sm font-medium text-gray-300 cursor-pointer">
                                    Restore parts to inventory
                                </label>
                                <p class="text-xs text-gray-400 mt-1">
                                    If checked, any parts allocated to this order will be returned to available inventory.
                                    Uncheck only if parts have been damaged or cannot be reused.
                                </p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-700 bg-gray-800 flex justify-end space-x-3">
                <button
                    type="button"
                    @click="closeModal"
                    class="px-6 py-2 border border-gray-600 rounded-lg text-gray-300 hover:bg-gray-700 transition-colors duration-200 font-medium"
                >
                    Keep Order
                </button>
                <button
                    type="button"
                    @click="submit"
                    :disabled="isSubmitting || !form.cancellation_reason.trim()"
                    :class="[
                        'px-6 py-2 rounded-lg font-medium transition-all duration-200 flex items-center space-x-2',
                        isSubmitting || !form.cancellation_reason.trim()
                            ? 'bg-gray-600 text-gray-400 cursor-not-allowed'
                            : 'bg-red-600 hover:bg-red-700 text-white shadow-lg hover:shadow-xl'
                    ]"
                >
                    <svg v-if="isSubmitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>{{ isSubmitting ? 'Cancelling...' : 'Cancel Order' }}</span>
                </button>
            </div>
        </div>
    </div>
</template>
