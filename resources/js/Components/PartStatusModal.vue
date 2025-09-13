<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    part: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close', 'saved']);

const form = useForm({
    status: '',
    order_date: '',
    expected_arrival_date: '',
    received_date: '',
});

watch(() => props.show, (newValue) => {
    if (newValue && props.part) {
        form.status = props.part.status || 'ordered';
        form.order_date = props.part.order_date ? new Date(props.part.order_date).toISOString().split('T')[0] : '';
        form.expected_arrival_date = props.part.expected_arrival_date ? new Date(props.part.expected_arrival_date).toISOString().split('T')[0] : '';
        form.received_date = props.part.received_date ? new Date(props.part.received_date).toISOString().split('T')[0] : '';
    }
});

const submit = () => {
    form.patch(route('parts.update-status', props.part.id), {
        onSuccess: () => {
            if (window.toast) {
                window.toast.success('Part status updated successfully!');
            }
            emit('saved');
            closeModal();
        },
        onError: (errors) => {
            console.error('Part status update failed:', errors);
            if (window.toast) {
                window.toast.error('Failed to update part status. Please try again.');
            }
        },
    });
};

const closeModal = () => {
    form.reset();
    form.clearErrors();
    emit('close');
};

const getStatusColor = (status) => {
    switch(status) {
        case 'ordered': return 'bg-yellow-900 text-yellow-300 border-yellow-600';
        case 'in_transit': return 'bg-blue-900 text-blue-300 border-blue-600';
        case 'in_stock': return 'bg-green-900 text-green-300 border-green-600';
        default: return 'bg-gray-900 text-gray-300 border-gray-600';
    }
};

const canTransitionTo = (newStatus) => {
    if (!props.part) return false;
    
    const transitions = {
        'ordered': ['in_transit', 'in_stock'],
        'in_transit': ['in_stock'],
        'in_stock': ['ordered'], // Can reorder
    };
    
    return transitions[props.part.status]?.includes(newStatus) || false;
};
</script>

<template>
    <!-- Modal Overlay -->
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeModal"></div>

            <!-- Modal panel -->
            <div class="relative inline-block align-bottom bg-gradient-to-br from-gray-900 to-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-700">
                <!-- Header -->
                <div class="bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-4 border-b border-gray-600">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-blue-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white" id="modal-title">
                                Update Part Status
                            </h3>
                        </div>
                        <button @click="closeModal" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Part Info -->
                    <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                        <h4 class="text-white font-medium mb-2">{{ part?.name }}</h4>
                        <p class="text-gray-400 text-sm">Part Number: {{ part?.part_number }}</p>
                        <div class="mt-2">
                            <span class="text-xs px-2 py-1 rounded border" :class="getStatusColor(part?.status)">
                                Current: {{ part?.status?.replace('_', ' ').toUpperCase() }}
                            </span>
                        </div>
                    </div>

                    <!-- Status Selection -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-300 mb-2">
                            New Status *
                        </label>
                        <select
                            id="status"
                            v-model="form.status"
                            class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors duration-200"
                            required
                        >
                            <option value="ordered" :disabled="!canTransitionTo('ordered')" class="bg-gray-700 text-white">Ordered</option>
                            <option value="in_transit" :disabled="!canTransitionTo('in_transit')" class="bg-gray-700 text-white">In Transit</option>
                            <option value="in_stock" :disabled="!canTransitionTo('in_stock')" class="bg-gray-700 text-white">In Stock</option>
                        </select>
                        <div v-if="form.errors.status" class="mt-1 text-sm text-red-400">
                            {{ form.errors.status }}
                        </div>
                    </div>

                    <!-- Date Fields -->
                    <div v-if="form.status === 'ordered'">
                        <label for="order_date" class="block text-sm font-medium text-gray-300 mb-2">
                            Order Date
                        </label>
                        <input
                            id="order_date"
                            v-model="form.order_date"
                            type="date"
                            class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors duration-200"
                        />
                        <div v-if="form.errors.order_date" class="mt-1 text-sm text-red-400">
                            {{ form.errors.order_date }}
                        </div>
                    </div>

                    <div v-if="form.status === 'ordered' || form.status === 'in_transit'">
                        <label for="expected_arrival_date" class="block text-sm font-medium text-gray-300 mb-2">
                            Expected Arrival Date
                        </label>
                        <input
                            id="expected_arrival_date"
                            v-model="form.expected_arrival_date"
                            type="date"
                            class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors duration-200"
                        />
                        <div v-if="form.errors.expected_arrival_date" class="mt-1 text-sm text-red-400">
                            {{ form.errors.expected_arrival_date }}
                        </div>
                    </div>

                    <div v-if="form.status === 'in_stock'">
                        <label for="received_date" class="block text-sm font-medium text-gray-300 mb-2">
                            Received Date
                        </label>
                        <input
                            id="received_date"
                            v-model="form.received_date"
                            type="date"
                            class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors duration-200"
                        />
                        <div v-if="form.errors.received_date" class="mt-1 text-sm text-red-400">
                            {{ form.errors.received_date }}
                        </div>
                    </div>



                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-700">
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-4 py-2 border border-gray-600 rounded-lg text-gray-300 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 flex items-center space-x-2"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Update Status</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
