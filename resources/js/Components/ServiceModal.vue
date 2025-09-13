<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    service: {
        type: Object,
        default: null,
    },

    deviceTypes: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close', 'saved']);

const form = useForm({
    name: '',
    description: '',
    device_type_id: '',
    base_price: '',
    estimated_duration: '',
    is_active: true,
});

const isEditing = ref(false);

watch(() => props.show, (newValue) => {
    if (newValue) {
        if (props.service) {
            // Editing mode
            isEditing.value = true;
            form.name = props.service.name || '';
            form.description = props.service.description || '';
            form.device_type_id = props.service.device_type_id || '';
            form.base_price = props.service.base_price || '';
            form.estimated_duration = props.service.estimated_duration || '';
            form.is_active = props.service.is_active ?? true;
        } else {
            // Creating mode
            isEditing.value = false;
            form.reset();
            form.is_active = true;
        }
    }
});

const submit = () => {
    if (isEditing.value) {
        form.put(route('services.update', props.service.id), {
            onSuccess: () => {
                // Show success notification
                if (window.toast) {
                    window.toast.success('Service updated successfully!');
                }
                emit('saved');
                closeModal();
            },
            onError: (errors) => {
                console.error('Service update failed:', errors);
                // Show error notification
                if (window.toast) {
                    window.toast.error('Failed to update service. Please try again.');
                }
            },
        });
    } else {
        form.post(route('services.store'), {
            onSuccess: () => {
                // Show success notification
                if (window.toast) {
                    window.toast.success('Service created successfully!');
                }
                emit('saved');
                closeModal();
            },
            onError: (errors) => {
                console.error('Service creation failed:', errors);
                // Show error notification
                if (window.toast) {
                    window.toast.error('Failed to create service. Please try again.');
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


</script>

<template>
    <!-- Modal Overlay -->
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" @click="closeModal">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-black bg-opacity-75 backdrop-blur-sm"></div>

            <!-- Modal -->
            <div 
                class="inline-block w-full max-w-3xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-2xl rounded-2xl"
                @click.stop
            >
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-purple-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">
                                {{ isEditing ? 'Edit Service' : 'Add New Service' }}
                            </h3>
                            <p class="text-sm text-gray-400">
                                {{ isEditing ? 'Update service information' : 'Create a new repair service' }}
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
                    <!-- Basic Information -->
                    <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                        <h4 class="text-lg font-semibold text-white mb-4">Service Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                                    Service Name *
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                    placeholder="Screen Replacement"
                                    required
                                />
                                <div v-if="form.errors.name" class="mt-1 text-sm text-red-400">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <div>
                                <label for="device_type_id" class="block text-sm font-medium text-gray-300 mb-2">
                                    Device Category *
                                </label>
                                <select
                                    id="device_type_id"
                                    v-model="form.device_type_id"
                                    class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                    required
                                >
                                    <option value="" class="bg-gray-700 text-white">Select device category...</option>
                                    <option
                                        v-for="deviceType in deviceTypes"
                                        :key="deviceType.id"
                                        :value="deviceType.id"
                                        class="bg-gray-700 text-white"
                                    >
                                        {{ deviceType.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.device_type_id" class="mt-1 text-sm text-red-400">
                                    {{ form.errors.device_type_id }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        </div>

                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-gray-300 mb-2">
                                Description
                            </label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="Detailed description of the service..."
                            ></textarea>
                            <div v-if="form.errors.description" class="mt-1 text-sm text-red-400">
                                {{ form.errors.description }}
                            </div>
                        </div>
                    </div>

                    <!-- Pricing and Duration -->
                    <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                        <h4 class="text-lg font-semibold text-white mb-4">Pricing & Duration</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="base_price" class="block text-sm font-medium text-gray-300 mb-2">
                                    Base Price *
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-400">₱</span>
                                    <input
                                        id="base_price"
                                        v-model="form.base_price"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200 pl-8"
                                        placeholder="999.00"
                                        required
                                    />
                                </div>
                                <div v-if="form.errors.base_price" class="mt-1 text-sm text-red-400">
                                    {{ form.errors.base_price }}
                                </div>
                            </div>

                            <div>
                                <label for="estimated_duration" class="block text-sm font-medium text-gray-300 mb-2">
                                    Estimated Duration (minutes)
                                </label>
                                <input
                                    id="estimated_duration"
                                    v-model="form.estimated_duration"
                                    type="number"
                                    min="1"
                                    class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                    placeholder="60"
                                />
                                <div v-if="form.errors.estimated_duration" class="mt-1 text-sm text-red-400">
                                    {{ form.errors.estimated_duration }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Toggle (only for editing) -->
                    <div v-if="isEditing" class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                        <h4 class="text-lg font-semibold text-white mb-4">Status</h4>
                        <label class="flex items-center">
                            <input
                                v-model="form.is_active"
                                type="checkbox"
                                class="rounded bg-gray-700 border-gray-600 text-red-600 shadow-sm focus:border-red-500 focus:ring-red-500"
                            />
                            <span class="ml-2 text-sm text-gray-300">Active Service</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">Inactive services won't be available for new repair orders</p>
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
                                {{ isEditing ? 'Update Service' : 'Create Service' }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
