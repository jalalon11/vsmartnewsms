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
    quantity: '',
    type: 'add',
    notes: '',
});

watch(() => props.show, (newValue) => {
    if (newValue) {
        form.reset();
    }
});

const submit = () => {
    if (!props.part) return;

    form.patch(route('parts.update-stock', props.part.id), {
        onSuccess: () => {
            // Show success notification
            if (window.toast) {
                window.toast.success('Stock updated successfully!');
            }
            emit('saved');
            closeModal();
        },
        onError: (errors) => {
            console.error('Stock update failed:', errors);
            // Show error notification
            if (window.toast) {
                window.toast.error('Failed to update stock. Please try again.');
            }
        },
    });
};

const closeModal = () => {
    form.reset();
    form.clearErrors();
    emit('close');
};

const getNewQuantity = () => {
    if (!props.part || !form.quantity) return props.part?.quantity_in_stock || 0;
    
    const currentStock = props.part.quantity_in_stock;
    const quantity = parseInt(form.quantity);
    
    switch (form.type) {
        case 'add':
            return currentStock + quantity;
        case 'subtract':
            return Math.max(0, currentStock - quantity);
        case 'set':
            return quantity;
        default:
            return currentStock;
    }
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
                            <div class="p-2 bg-green-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-10 0a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2M9 12l2 2 4-4" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white" id="modal-title">
                                Update Stock
                            </h3>
                        </div>
                        <button @click="closeModal" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Part Info -->
                <div v-if="part" class="px-6 py-4 bg-gray-800 border-b border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-lg font-medium text-white">{{ part.name }}</h4>
                            <p class="text-sm text-gray-400">{{ part.part_number }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-400">Current Stock</p>
                            <p class="text-2xl font-bold text-white">{{ part.quantity_in_stock }}</p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Update Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-3">
                            Update Type
                        </label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="relative">
                                <input
                                    v-model="form.type"
                                    type="radio"
                                    value="add"
                                    class="sr-only"
                                />
                                <div class="flex items-center justify-center p-3 border rounded-lg cursor-pointer transition-colors duration-200"
                                     :class="form.type === 'add' ? 'border-green-500 bg-green-500 bg-opacity-20 text-green-400' : 'border-gray-600 text-gray-400 hover:border-gray-500'">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Add
                                </div>
                            </label>
                            <label class="relative">
                                <input
                                    v-model="form.type"
                                    type="radio"
                                    value="subtract"
                                    class="sr-only"
                                />
                                <div class="flex items-center justify-center p-3 border rounded-lg cursor-pointer transition-colors duration-200"
                                     :class="form.type === 'subtract' ? 'border-red-500 bg-red-500 bg-opacity-20 text-red-400' : 'border-gray-600 text-gray-400 hover:border-gray-500'">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                                    </svg>
                                    Remove
                                </div>
                            </label>
                            <label class="relative">
                                <input
                                    v-model="form.type"
                                    type="radio"
                                    value="set"
                                    class="sr-only"
                                />
                                <div class="flex items-center justify-center p-3 border rounded-lg cursor-pointer transition-colors duration-200"
                                     :class="form.type === 'set' ? 'border-blue-500 bg-blue-500 bg-opacity-20 text-blue-400' : 'border-gray-600 text-gray-400 hover:border-gray-500'">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Set
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-300 mb-2">
                            Quantity *
                        </label>
                        <input
                            id="quantity"
                            v-model="form.quantity"
                            type="number"
                            min="0"
                            class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors duration-200"
                            :placeholder="form.type === 'set' ? 'New total quantity' : 'Quantity to ' + form.type"
                            required
                        />
                        <div v-if="form.errors.quantity" class="mt-1 text-sm text-red-400">
                            {{ form.errors.quantity }}
                        </div>
                    </div>

                    <!-- Preview -->
                    <div v-if="form.quantity" class="p-4 bg-gray-800 rounded-lg border border-gray-700">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-400">New stock level:</span>
                            <span class="text-lg font-semibold text-white">{{ getNewQuantity() }}</span>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-sm text-gray-400">Change:</span>
                            <span class="text-sm font-medium" 
                                  :class="getNewQuantity() > part?.quantity_in_stock ? 'text-green-400' : getNewQuantity() < part?.quantity_in_stock ? 'text-red-400' : 'text-gray-400'">
                                {{ getNewQuantity() > part?.quantity_in_stock ? '+' : '' }}{{ getNewQuantity() - (part?.quantity_in_stock || 0) }}
                            </span>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-300 mb-2">
                            Notes (Optional)
                        </label>
                        <textarea
                            id="notes"
                            v-model="form.notes"
                            rows="3"
                            class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors duration-200"
                            placeholder="Reason for stock update..."
                        ></textarea>
                        <div v-if="form.errors.notes" class="mt-1 text-sm text-red-400">
                            {{ form.errors.notes }}
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
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 flex items-center space-x-2"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Update Stock</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
