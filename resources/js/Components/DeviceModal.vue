<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    device: {
        type: Object,
        default: null,
    },
    customers: {
        type: Array,
        default: () => [],
    },
    deviceTypes: {
        type: Array,
        default: () => [],
    },
    preselectedCustomer: {
        type: [String, Number],
        default: null,
    },
});

const emit = defineEmits(['close', 'saved']);

const form = useForm({
    customer_id: '',
    device_type_id: '',
    brand: '',
    model: '',
    serial_number: '',
    imei: '',
    year: '',
    color: '',
    specifications: '',
    accessories: '',
    condition_notes: '',
});

const isEditing = ref(false);

watch(() => props.show, (newValue) => {
    if (newValue) {
        if (props.device) {
            // Editing mode
            isEditing.value = true;
            form.customer_id = props.device.customer_id || '';
            form.device_type_id = props.device.device_type_id || '';
            form.brand = props.device.brand || '';
            form.model = props.device.model || '';
            form.serial_number = props.device.serial_number || '';
            form.imei = props.device.imei || '';
            form.year = props.device.year || '';
            form.color = props.device.color || '';
            form.specifications = props.device.specifications || '';
            form.accessories = props.device.accessories || '';
            form.condition_notes = props.device.condition_notes || '';
        } else {
            // Creating mode
            isEditing.value = false;
            form.reset();
            // Set preselected customer if provided
            if (props.preselectedCustomer) {
                form.customer_id = props.preselectedCustomer;
            }
        }
    }
});

const submit = () => {
    // Guard against null form object
    if (!form) {
        console.error('Form object is not initialized');
        return;
    }

    if (isEditing.value) {
        form.put(route('devices.update', props.device.id), {
            onSuccess: () => {
                // Show success notification
                if (window.toast) {
                    window.toast.success('Device updated successfully!');
                }
                emit('saved');
                closeModal();
            },
            onError: (errors) => {
                console.error('Device update failed:', errors);
                // Show error notification
                if (window.toast) {
                    window.toast.error('Failed to update device. Please try again.');
                }
            },
        });
    } else {
        form.post(route('devices.store'), {
            onSuccess: () => {
                // Show success notification
                if (window.toast) {
                    window.toast.success('Device created successfully!');
                }
                emit('saved');
                closeModal();
            },
            onError: (errors) => {
                console.error('Device creation failed:', errors);
                // Show error notification
                if (window.toast) {
                    window.toast.error('Failed to create device. Please try again.');
                }
            },
        });
    }
};

const closeModal = () => {
    if (form) {
        form.reset();
        form.clearErrors();
    }
    emit('close');
};
</script>

<template>
    <!-- Modal Overlay -->
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" @click="closeModal">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-black bg-opacity-75 backdrop-blur-sm"></div>

            <!-- Modal -->
            <div 
                class="inline-block w-full max-w-4xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-2xl rounded-2xl"
                @click.stop
            >
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-purple-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">
                                {{ isEditing ? 'Edit Device' : 'Register New Device' }}
                            </h3>
                            <p class="text-sm text-gray-400">
                                {{ isEditing ? 'Update device information' : 'Add a new device to the system' }}
                            </p>
                        </div>
                    </div>
                    <button
                        @click="closeModal"
                        class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors duration-200"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Customer and Device Type -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="customer_id" class="block text-sm font-medium text-gray-300 mb-2">
                                Customer *
                            </label>
                            <select
                                id="customer_id"
                                v-model="form.customer_id"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                required
                            >
                                <option value="" class="bg-gray-800 text-white">Select customer...</option>
                                <option
                                    v-for="customer in customers"
                                    :key="customer.id"
                                    :value="customer.id"
                                    class="bg-gray-800 text-white"
                                >
                                    {{ customer.full_name }}
                                </option>
                            </select>
                            <div v-if="form.errors.customer_id" class="mt-1 text-sm text-red-400">
                                {{ form.errors.customer_id }}
                            </div>
                        </div>

                        <div>
                            <label for="device_type_id" class="block text-sm font-medium text-gray-300 mb-2">
                                Device Category *
                            </label>
                            <select
                                id="device_type_id"
                                v-model="form.device_type_id"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                required
                            >
                                <option value="" class="bg-gray-800 text-white">Select device category...</option>
                                <option
                                    v-for="type in deviceTypes"
                                    :key="type.id"
                                    :value="type.id"
                                    class="bg-gray-800 text-white"
                                >
                                    {{ type.name }} ({{ type.category }})
                                </option>
                            </select>
                            <div v-if="form.errors.device_type_id" class="mt-1 text-sm text-red-400">
                                {{ form.errors.device_type_id }}
                            </div>
                        </div>
                    </div>

                    <!-- Brand and Model -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-300 mb-2">
                                Brand *
                            </label>
                            <input
                                id="brand"
                                v-model="form.brand"
                                type="text"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="e.g., Apple, Samsung, HP"
                                required
                            />
                            <div v-if="form.errors.brand" class="mt-1 text-sm text-red-400">
                                {{ form.errors.brand }}
                            </div>
                        </div>

                        <div>
                            <label for="model" class="block text-sm font-medium text-gray-300 mb-2">
                                Model *
                            </label>
                            <input
                                id="model"
                                v-model="form.model"
                                type="text"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="e.g., iPhone 14, Galaxy S23"
                                required
                            />
                            <div v-if="form.errors.model" class="mt-1 text-sm text-red-400">
                                {{ form.errors.model }}
                            </div>
                        </div>
                    </div>

                    <!-- Serial Number and IMEI -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="serial_number" class="block text-sm font-medium text-gray-300 mb-2">
                                Serial Number
                            </label>
                            <input
                                id="serial_number"
                                v-model="form.serial_number"
                                type="text"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="Device serial number"
                            />
                            <div v-if="form.errors.serial_number" class="mt-1 text-sm text-red-400">
                                {{ form.errors.serial_number }}
                            </div>
                        </div>

                        <div>
                            <label for="imei" class="block text-sm font-medium text-gray-300 mb-2">
                                IMEI (for phones)
                            </label>
                            <input
                                id="imei"
                                v-model="form.imei"
                                type="text"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="15-digit IMEI number"
                            />
                            <div v-if="form.errors.imei" class="mt-1 text-sm text-red-400">
                                {{ form.errors.imei }}
                            </div>
                        </div>
                    </div>

                    <!-- Year and Color -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-300 mb-2">
                                Year
                            </label>
                            <input
                                id="year"
                                v-model="form.year"
                                type="number"
                                min="1990"
                                :max="new Date().getFullYear() + 1"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="2023"
                            />
                            <div v-if="form.errors.year" class="mt-1 text-sm text-red-400">
                                {{ form.errors.year }}
                            </div>
                        </div>

                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-300 mb-2">
                                Color
                            </label>
                            <input
                                id="color"
                                v-model="form.color"
                                type="text"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="e.g., Space Gray, White"
                            />
                            <div v-if="form.errors.color" class="mt-1 text-sm text-red-400">
                                {{ form.errors.color }}
                            </div>
                        </div>
                    </div>

                    <!-- Specifications -->
                    <div>
                        <label for="specifications" class="block text-sm font-medium text-gray-300 mb-2">
                            Specifications
                        </label>
                        <textarea
                            id="specifications"
                            v-model="form.specifications"
                            rows="3"
                            class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                            placeholder="Device specifications, storage, RAM, etc."
                        ></textarea>
                        <div v-if="form.errors.specifications" class="mt-1 text-sm text-red-400">
                            {{ form.errors.specifications }}
                        </div>
                    </div>

                    <!-- Accessories and Condition -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="accessories" class="block text-sm font-medium text-gray-300 mb-2">
                                Accessories
                            </label>
                            <textarea
                                id="accessories"
                                v-model="form.accessories"
                                rows="3"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="Charger, case, headphones, etc."
                            ></textarea>
                            <div v-if="form.errors.accessories" class="mt-1 text-sm text-red-400">
                                {{ form.errors.accessories }}
                            </div>
                        </div>

                        <div>
                            <label for="condition_notes" class="block text-sm font-medium text-gray-300 mb-2">
                                Condition Notes
                            </label>
                            <textarea
                                id="condition_notes"
                                v-model="form.condition_notes"
                                rows="3"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="Current condition, existing damage, etc."
                            ></textarea>
                            <div v-if="form.errors.condition_notes" class="mt-1 text-sm text-red-400">
                                {{ form.errors.condition_notes }}
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-700">
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-6 py-2 border border-gray-600 rounded-lg text-gray-300 hover:bg-gray-700 transition-colors duration-200 font-medium"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 disabled:opacity-50 transition-all duration-200 shadow-lg hover:shadow-xl font-medium"
                        >
                            <span v-if="form.processing" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ isEditing ? 'Updating...' : 'Creating...' }}
                            </span>
                            <span v-else>
                                {{ isEditing ? 'Update Device' : 'Register Device' }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
