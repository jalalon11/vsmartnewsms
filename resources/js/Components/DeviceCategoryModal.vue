<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    deviceTypes: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close', 'saved']);

const showCreateForm = ref(false);
const editingCategory = ref(null);

const form = useForm({
    name: '',
    category: '',
    description: '',
    is_active: true,
});

const isEditing = computed(() => !!editingCategory.value);

const openCreateForm = () => {
    editingCategory.value = null;
    form.reset();
    showCreateForm.value = true;
};

const openEditForm = (deviceType) => {
    editingCategory.value = deviceType;
    form.name = deviceType.name || '';
    form.category = deviceType.category || '';
    form.description = deviceType.description || '';
    form.is_active = deviceType.is_active ?? true;
    showCreateForm.value = true;
};

const closeCreateForm = () => {
    showCreateForm.value = false;
    editingCategory.value = null;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (isEditing.value) {
        form.put(route('device-types.update', editingCategory.value.id), {
            onSuccess: () => {
                if (window.toast) {
                    window.toast.success('Device category updated successfully!');
                }
                closeCreateForm();
                emit('saved');
            },
            onError: (errors) => {
                if (window.toast) {
                    window.toast.error(errors.message || 'Failed to update device category');
                }
            }
        });
    } else {
        form.post(route('device-types.store'), {
            onSuccess: () => {
                if (window.toast) {
                    window.toast.success('Device category created successfully!');
                }
                closeCreateForm();
                emit('saved');
            },
            onError: (errors) => {
                if (window.toast) {
                    window.toast.error(errors.message || 'Failed to create device category');
                }
            }
        });
    }
};

const deleteCategory = (deviceType) => {
    if (confirm('Are you sure you want to delete this device category?')) {
        router.delete(route('device-types.destroy', deviceType.id), {
            onSuccess: () => {
                if (window.toast) {
                    window.toast.success('Device category deleted successfully!');
                }
                emit('saved');
            },
            onError: (errors) => {
                if (window.toast) {
                    window.toast.error(errors.message || 'Failed to delete device category');
                }
            }
        });
    }
};

const getStatusColor = (isActive) => {
    return isActive
        ? 'bg-green-900 text-green-300 border-green-600'
        : 'bg-red-900 text-red-300 border-red-600';
};

const closeModal = () => {
    closeCreateForm();
    emit('close');
};
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
    >
        <!-- Background overlay -->
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div
                class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"
                aria-hidden="true"
                @click="closeModal"
            ></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-gray-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-700">
                <!-- Header -->
                <div class="bg-gradient-to-r from-gray-800 to-gray-900 px-6 py-4 border-b border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-white">
                            Device Categories Management
                        </h3>
                        <button
                            @click="closeModal"
                            class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors duration-200"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex h-96">
                    <!-- Categories List -->
                    <div class="w-2/3 border-r border-gray-700">
                        <div class="p-4 border-b border-gray-700">
                            <div class="flex items-center justify-between">
                                <h4 class="text-lg font-medium text-white">Existing Categories</h4>
                                <button
                                    @click="openCreateForm"
                                    class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-3 py-1.5 rounded-lg transition-all duration-200 text-sm flex items-center space-x-1"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    <span>Add New</span>
                                </button>
                            </div>
                        </div>

                        <div class="overflow-y-auto h-80 p-4">
                            <div v-if="deviceTypes.length === 0" class="text-center text-gray-400 py-8">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                <p>No device categories found</p>
                                <p class="text-sm mt-1">Click "Add New" to create your first category</p>
                            </div>

                            <div v-else class="space-y-2">
                                <div
                                    v-for="deviceType in deviceTypes"
                                    :key="deviceType.id"
                                    class="bg-gray-800 border border-gray-700 rounded-lg p-3 hover:bg-gray-750 transition-colors duration-200"
                                >
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <h5 class="text-white font-medium">{{ deviceType.name }}</h5>
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full border" :class="getStatusColor(deviceType.is_active)">
                                                    {{ deviceType.is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                            <p v-if="deviceType.category" class="text-sm text-gray-400 mt-1">{{ deviceType.category }}</p>
                                            <p v-if="deviceType.description" class="text-sm text-gray-300 mt-1">{{ deviceType.description }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ deviceType.services_count || 0 }} services</p>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            <button
                                                @click="openEditForm(deviceType)"
                                                class="p-1.5 text-blue-400 hover:text-blue-300 hover:bg-gray-700 rounded transition-colors duration-200"
                                                title="Edit"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button
                                                @click="deleteCategory(deviceType)"
                                                class="p-1.5 text-red-400 hover:text-red-300 hover:bg-gray-700 rounded transition-colors duration-200"
                                                title="Delete"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Create/Edit Form -->
                    <div class="w-1/3">
                        <div v-if="!showCreateForm" class="p-6 text-center text-gray-400 h-full flex items-center justify-center">
                            <div>
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <p class="text-lg font-medium">Add New Category</p>
                                <p class="text-sm mt-2">Click "Add New" to create a device category</p>
                            </div>
                        </div>

                        <form v-else @submit.prevent="submit" class="p-4 h-full overflow-y-auto">
                            <div class="mb-4">
                                <h4 class="text-lg font-medium text-white mb-4">
                                    {{ isEditing ? 'Edit Category' : 'Create New Category' }}
                                </h4>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                                        Category Name *
                                    </label>
                                    <input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                        placeholder="iOS Device, Android Device, etc."
                                        required
                                    />
                                    <div v-if="form.errors.name" class="mt-1 text-sm text-red-400">
                                        {{ form.errors.name }}
                                    </div>
                                </div>

                                <div>
                                    <label for="category" class="block text-sm font-medium text-gray-300 mb-2">
                                        Category Type
                                    </label>
                                    <input
                                        id="category"
                                        v-model="form.category"
                                        type="text"
                                        class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                        placeholder="Mobile, Computer, etc."
                                    />
                                    <div v-if="form.errors.category" class="mt-1 text-sm text-red-400">
                                        {{ form.errors.category }}
                                    </div>
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-300 mb-2">
                                        Description
                                    </label>
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        rows="3"
                                        class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                        placeholder="Brief description..."
                                    ></textarea>
                                    <div v-if="form.errors.description" class="mt-1 text-sm text-red-400">
                                        {{ form.errors.description }}
                                    </div>
                                </div>

                                <div class="flex items-center">
                                    <input
                                        id="is_active"
                                        v-model="form.is_active"
                                        type="checkbox"
                                        class="w-4 h-4 text-red-600 bg-gray-700 border-gray-600 rounded focus:ring-red-500 focus:ring-2"
                                    />
                                    <label for="is_active" class="ml-2 text-sm text-gray-300">
                                        Active (available for use)
                                    </label>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex items-center justify-end space-x-2 pt-4 mt-6 border-t border-gray-700">
                                <button
                                    type="button"
                                    @click="closeCreateForm"
                                    class="px-3 py-2 text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors duration-200"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
                                >
                                    <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>{{ form.processing ? 'Saving...' : (isEditing ? 'Update' : 'Create') }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
