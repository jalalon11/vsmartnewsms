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
    devices: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close', 'saved']);

const form = useForm({
    part_number: '',
    name: '',
    description: '',
    category: '',
    device_id: null,
    compatible_devices: [],
    cost_price: '',
    selling_price: '',
    quantity_in_stock: '',
    minimum_stock_level: '',
    supplier: '',
    is_active: true,
    status: 'ordered',
    order_date: '',
    expected_arrival_date: '',
    received_date: '',
});

const isEditing = ref(false);

watch(() => props.show, (newValue) => {
    if (newValue) {
        if (props.part) {
            // Editing mode
            isEditing.value = true;
            form.part_number = props.part.part_number || '';
            form.name = props.part.name || '';
            form.description = props.part.description || '';
            form.category = props.part.category || '';
            form.device_id = props.part.device_id || null;
            form.compatible_devices = props.part.compatible_devices || [];
            form.cost_price = props.part.cost_price || '';
            form.selling_price = props.part.selling_price || '';
            form.quantity_in_stock = props.part.quantity_in_stock || '';
            form.minimum_stock_level = props.part.minimum_stock_level || '';
            form.supplier = props.part.supplier || '';
            form.is_active = props.part.is_active ?? true;
            form.status = props.part.status || 'ordered';
            form.order_date = props.part.order_date ? new Date(props.part.order_date).toISOString().split('T')[0] : '';
            form.expected_arrival_date = props.part.expected_arrival_date ? new Date(props.part.expected_arrival_date).toISOString().split('T')[0] : '';
            form.received_date = props.part.received_date ? new Date(props.part.received_date).toISOString().split('T')[0] : '';
        } else {
            // Creating mode
            isEditing.value = false;
            form.reset();
        }
    }
});

const submit = () => {
    if (isEditing.value) {
        form.put(route('parts.update', props.part.id), {
            onSuccess: () => {
                // Show success notification
                if (window.toast) {
                    window.toast.success('Part updated successfully!');
                }
                emit('saved');
                closeModal();
            },
            onError: (errors) => {
                console.error('Part update failed:', errors);
                // Show error notification
                if (window.toast) {
                    window.toast.error('Failed to update part. Please try again.');
                }
            },
        });
    } else {
        form.post(route('parts.store'), {
            onSuccess: () => {
                // Show success notification
                if (window.toast) {
                    window.toast.success('Part created successfully!');
                }
                emit('saved');
                closeModal();
            },
            onError: (errors) => {
                console.error('Part creation failed:', errors);
                // Show error notification
                if (window.toast) {
                    window.toast.error('Failed to create part. Please try again.');
                }
            },
        });
    }
};

const closeModal = () => {
    form.reset();
    form.clearErrors();
    emit('close');
};

const categories = [
    'Screen Components',
    'Battery',
    'Charging Components',
    'Audio Components',
    'Camera Components',
    'Internal Components',
    'External Components',
    'Tools & Equipment',
    'Adhesives & Tapes',
    'Other'
];
</script>

<template>
    <!-- Modal Overlay -->
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeModal"></div>

            <!-- Modal panel -->
            <div class="relative inline-block align-bottom bg-gradient-to-br from-gray-900 to-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-700">
                <!-- Header -->
                <div class="bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-4 border-b border-gray-600">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-red-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white" id="modal-title">
                                {{ isEditing ? 'Edit Part' : 'Add New Part' }}
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
                    <!-- Part Number (only show when editing) -->
                    <div v-if="isEditing">
                        <label for="part_number" class="block text-sm font-medium text-gray-300 mb-2">
                            Part Number *
                        </label>
                        <input
                            id="part_number"
                            v-model="form.part_number"
                            type="text"
                            class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                            placeholder="PN-2024-000001"
                            required
                        />
                        <div v-if="form.errors.part_number" class="mt-1 text-sm text-red-400">
                            {{ form.errors.part_number }}
                        </div>
                    </div>

                    <!-- Part Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                            Part Name *
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                            placeholder="iPhone 12 Screen"
                            required
                        />
                        <div v-if="form.errors.name" class="mt-1 text-sm text-red-400">
                            {{ form.errors.name }}
                        </div>
                        <p v-if="!isEditing" class="mt-1 text-xs text-gray-400">Part number will be auto-generated</p>
                    </div>

                    <!-- Category and Supplier -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-300 mb-2">
                                Category *
                            </label>
                            <select
                                id="category"
                                v-model="form.category"
                                class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                required
                            >
                                <option value="" class="bg-gray-700 text-white">Select category...</option>
                                <option 
                                    v-for="cat in categories" 
                                    :key="cat" 
                                    :value="cat"
                                    class="bg-gray-700 text-white"
                                >
                                    {{ cat }}
                                </option>
                            </select>
                            <div v-if="form.errors.category" class="mt-1 text-sm text-red-400">
                                {{ form.errors.category }}
                            </div>
                        </div>

                        <div>
                            <label for="supplier" class="block text-sm font-medium text-gray-300 mb-2">
                                Supplier
                            </label>
                            <input
                                id="supplier"
                                v-model="form.supplier"
                                type="text"
                                class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="Supplier name"
                            />
                            <div v-if="form.errors.supplier" class="mt-1 text-sm text-red-400">
                                {{ form.errors.supplier }}
                            </div>
                        </div>
                    </div>

                    <!-- Device Selection (Optional) -->
                    <div>
                        <label for="device_id" class="block text-sm font-medium text-gray-300 mb-2">
                            Associated Device (Optional)
                        </label>
                        <select
                            id="device_id"
                            v-model="form.device_id"
                            class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                        >
                            <option value="" class="bg-gray-700 text-white">No specific device</option>
                            <option
                                v-for="device in devices"
                                :key="device.id"
                                :value="device.id"
                                class="bg-gray-700 text-white"
                            >
                                {{ device.label }}
                            </option>
                        </select>
                        <div v-if="form.errors.device_id" class="mt-1 text-sm text-red-400">
                            {{ form.errors.device_id }}
                        </div>
                        <p class="mt-1 text-xs text-gray-400">
                            Link this part to a specific registered device. This will help auto-suggest parts when creating repair orders for this device.
                        </p>
                    </div>

                    <!-- Pricing -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="cost_price" class="block text-sm font-medium text-gray-300 mb-2">
                                Cost Price *
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-400">₱</span>
                                <input
                                    id="cost_price"
                                    v-model="form.cost_price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200 pl-8"
                                    placeholder="500.00"
                                    required
                                />
                            </div>
                            <div v-if="form.errors.cost_price" class="mt-1 text-sm text-red-400">
                                {{ form.errors.cost_price }}
                            </div>
                        </div>

                        <div>
                            <label for="selling_price" class="block text-sm font-medium text-gray-300 mb-2">
                                Selling Price *
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-400">₱</span>
                                <input
                                    id="selling_price"
                                    v-model="form.selling_price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200 pl-8"
                                    placeholder="750.00"
                                    required
                                />
                            </div>
                            <div v-if="form.errors.selling_price" class="mt-1 text-sm text-red-400">
                                {{ form.errors.selling_price }}
                            </div>
                        </div>
                    </div>

                    <!-- Stock Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="quantity_in_stock" class="block text-sm font-medium text-gray-300 mb-2">
                                Quantity in Stock *
                            </label>
                            <input
                                id="quantity_in_stock"
                                v-model="form.quantity_in_stock"
                                type="number"
                                min="0"
                                class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="10"
                                required
                            />
                            <div v-if="form.errors.quantity_in_stock" class="mt-1 text-sm text-red-400">
                                {{ form.errors.quantity_in_stock }}
                            </div>
                        </div>

                        <div>
                            <label for="minimum_stock_level" class="block text-sm font-medium text-gray-300 mb-2">
                                Minimum Stock Level *
                            </label>
                            <input
                                id="minimum_stock_level"
                                v-model="form.minimum_stock_level"
                                type="number"
                                min="0"
                                class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="5"
                                required
                            />
                            <div v-if="form.errors.minimum_stock_level" class="mt-1 text-sm text-red-400">
                                {{ form.errors.minimum_stock_level }}
                            </div>
                        </div>
                    </div>

                    <!-- Status Information -->
                    <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                        <h4 class="text-lg font-semibold text-white mb-4">Status Information</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-300 mb-2">
                                    Status *
                                </label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                    required
                                >
                                    <option value="ordered" class="bg-gray-700 text-white" selected>Ordered</option>
                                    <option value="in_transit" class="bg-gray-700 text-white">In Transit</option>
                                    <option value="in_stock" class="bg-gray-700 text-white">In Stock</option>
                                </select>
                                <div v-if="form.errors.status" class="mt-1 text-sm text-red-400">
                                    {{ form.errors.status }}
                                </div>
                            </div>

                            <div v-if="form.status === 'ordered'">
                                <label for="order_date" class="block text-sm font-medium text-gray-300 mb-2">
                                    Order Date
                                </label>
                                <input
                                    id="order_date"
                                    v-model="form.order_date"
                                    type="date"
                                    class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
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
                                    class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
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
                                    class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                />
                                <div v-if="form.errors.received_date" class="mt-1 text-sm text-red-400">
                                    {{ form.errors.received_date }}
                                </div>
                            </div>
                        </div>


                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                            placeholder="Part description and specifications..."
                        ></textarea>
                        <div v-if="form.errors.description" class="mt-1 text-sm text-red-400">
                            {{ form.errors.description }}
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center">
                        <input
                            id="is_active"
                            v-model="form.is_active"
                            type="checkbox"
                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-600 rounded bg-gray-700"
                        />
                        <label for="is_active" class="ml-2 block text-sm text-gray-300">
                            Active (available for use)
                        </label>
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
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 flex items-center space-x-2"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ isEditing ? 'Update Part' : 'Create Part' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
