<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    repairOrder: {
        type: Object,
        default: null,
    },
    customers: {
        type: Array,
        default: () => [],
    },
    devices: {
        type: Array,
        default: () => [],
    },
    services: {
        type: Array,
        default: () => [],
    },
    technicians: {
        type: Array,
        default: () => [],
    },
    preselectedCustomer: {
        type: [String, Number],
        default: null,
    },
    parts: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close', 'saved']);

const form = useForm({
    customer_id: '',
    device_id: '',
    service_id: '',
    services: [{ service_id: '', service_price: '', service_notes: '' }],
    technician_id: '',
    priority: 'medium',
    issue_description: '',
    diagnosis: '',
    solution: '',
    status: 'pending',
    labor_cost: 0,
    parts_cost: 0,
    customer_notes: '',
    internal_notes: '',
    estimated_completion: '',
    selected_parts: [], // Array of {part_id, quantity, unit_price}
});

const isEditing = ref(false);
const selectedCustomer = ref(null);

// Computed property for filtered devices based on selected customer
const filteredDevices = computed(() => {
    if (!form.customer_id) return [];
    return props.devices.filter(device => device.customer_id == form.customer_id);
});

// Computed property for filtered services based on selected device's category
const filteredServices = computed(() => {
    if (!form.device_id) return props.services;

    const device = props.devices.find(d => d.id == form.device_id);
    if (!device || !device.device_type_id) return props.services;

    return props.services.filter(service => service.device_type_id == device.device_type_id);
});

// Computed property for filtered parts based on selected device
const filteredParts = computed(() => {
    if (!form.device_id) return props.parts;

    const device = props.devices.find(d => d.id == form.device_id);
    if (!device) return props.parts;

    return props.parts.filter(part => {
        // Check if part is compatible with the device
        // Part is compatible if:
        // 1. It has the same device_id as the selected device, OR
        // 2. The device's ID is in the part's compatible_devices array, OR
        // 3. The device's device_type_id is in the part's compatible_devices array
        return part.device_id == device.id ||
               (part.compatible_devices && part.compatible_devices.includes(device.id)) ||
               (part.compatible_devices && device.device_type_id && part.compatible_devices.includes(device.device_type_id));
    });
});

watch(() => props.show, (newValue) => {
    if (newValue) {
        if (props.repairOrder) {
            // Editing mode
            isEditing.value = true;
            form.customer_id = props.repairOrder.customer_id || '';
            form.device_id = props.repairOrder.device_id || '';
            form.service_id = props.repairOrder.service_id || '';

            // For editing, populate services array with all current services
            if (props.repairOrder.services && props.repairOrder.services.length > 0) {
                form.services = props.repairOrder.services.map(service => ({
                    service_id: service.id,
                    service_price: service.pivot?.service_price || service.base_price || '',
                    service_notes: service.pivot?.service_notes || ''
                }));
            } else if (props.repairOrder.service_id) {
                // Fallback for legacy single service
                form.services = [{
                    service_id: props.repairOrder.service_id,
                    service_price: props.repairOrder.labor_cost || '',
                    service_notes: ''
                }];
            }

            // Use the technician selection ID that handles both regular technicians and admin users
            form.technician_id = props.repairOrder.technician_selection_id || '';
            form.priority = props.repairOrder.priority || 'medium';
            form.issue_description = props.repairOrder.issue_description || '';
            form.diagnosis = props.repairOrder.diagnosis || '';
            form.solution = props.repairOrder.solution || '';
            form.status = props.repairOrder.status || 'pending';
            form.labor_cost = props.repairOrder.labor_cost || 0;
            form.parts_cost = props.repairOrder.parts_cost || 0;
            form.customer_notes = props.repairOrder.customer_notes || '';
            form.internal_notes = props.repairOrder.internal_notes || '';
            form.estimated_completion = props.repairOrder.estimated_completion || '';
            form.selected_parts = props.repairOrder.parts?.map(part => ({
                part_id: part.id,
                quantity: part.pivot.quantity_used,
                unit_price: part.pivot.unit_price
            })) || [];
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

// Watch customer selection to reset device selection
watch(() => form.customer_id, (newCustomerId) => {
    if (newCustomerId) {
        selectedCustomer.value = props.customers.find(c => c.id == newCustomerId);
        // Reset device selection when customer changes
        if (!isEditing.value) {
            form.device_id = '';
        }
    } else {
        selectedCustomer.value = null;
        form.device_id = '';
    }
});

// Watch for device changes to reset services and fetch device-specific parts
const suggestedParts = ref([]);
const isLoadingParts = ref(false);

watch(() => form.device_id, async (newDeviceId) => {
    if (newDeviceId) {
        // Reset services when device changes
        form.services = [{ service_id: '', service_price: '', service_notes: '' }];

        // Clear selected parts when device changes (unless editing)
        if (!isEditing.value) {
            form.selected_parts = [];
        }

        // Fetch device-specific parts
        isLoadingParts.value = true;
        try {
            const response = await fetch(`/devices/${newDeviceId}/parts`);
            const data = await response.json();

            // Combine device-specific parts and compatible parts
            suggestedParts.value = [
                ...data.device_parts.map(part => ({ ...part, suggested: true, type: 'device-specific' })),
                ...data.compatible_parts.map(part => ({ ...part, suggested: true, type: 'compatible' }))
            ];
        } catch (error) {
            console.error('Error fetching device parts:', error);
            suggestedParts.value = [];
        } finally {
            isLoadingParts.value = false;
        }
    } else {
        suggestedParts.value = [];
        // Clear selected parts when no device is selected
        if (!isEditing.value) {
            form.selected_parts = [];
        }
    }
});

// Functions for managing multiple services
const addService = () => {
    form.services.push({ service_id: '', service_price: '', service_notes: '' });
};

const removeService = (index) => {
    if (form.services.length > 1) {
        form.services.splice(index, 1);
    }
};

const getServicePrice = (serviceId) => {
    const service = filteredServices.value.find(s => s.id == serviceId);
    return service ? service.base_price : 0;
};

const updateServicePrice = (index, serviceId) => {
    if (serviceId) {
        // Always set the price to the service's base price when service is selected
        form.services[index].service_price = getServicePrice(serviceId);
        // Update the main service_id and labor_cost for editing mode
        if (index === 0) {
            form.service_id = serviceId;
            form.labor_cost = getServicePrice(serviceId);
        }
    }
};

// Function to sync service data before submission
const syncServiceData = () => {
    if (form.services.length > 0 && form.services[0].service_id) {
        form.service_id = form.services[0].service_id;
        form.labor_cost = parseFloat(form.services[0].service_price) || 0;
    }

    // Calculate parts cost
    form.parts_cost = form.selected_parts.reduce((total, part) => {
        return total + (parseFloat(part.unit_price) || 0) * (parseInt(part.quantity) || 0);
    }, 0);
};

// Watch for service changes to sync with main form fields
watch(() => form.services, (newServices) => {
    if (newServices.length > 0 && newServices[0].service_id) {
        form.service_id = newServices[0].service_id;
        form.labor_cost = parseFloat(newServices[0].service_price) || 0;
    }
}, { deep: true });

const submit = () => {
    // Guard against null form object
    if (!form) {
        console.error('Form object is not initialized');
        return;
    }

    // Sync service data before submission
    syncServiceData();

    // Validate parts stock before submission
    const invalidParts = (form.selected_parts || []).filter(part => {
        return part.part_id && !isQuantityValid(part.part_id, part.quantity);
    });

    if (invalidParts.length > 0) {
        if (window.toast) {
            window.toast.error('Please fix part quantity issues before submitting.');
        } else {
            alert('Please fix part quantity issues before submitting.');
        }
        return;
    }

    // Check for out of stock parts
    const outOfStockParts = (form.selected_parts || []).filter(part => {
        return part.part_id && getPartStock(part.part_id) === 0;
    });

    if (outOfStockParts.length > 0) {
        if (window.toast) {
            window.toast.error('Cannot add parts that are out of stock.');
        } else {
            alert('Cannot add parts that are out of stock.');
        }
        return;
    }

    if (isEditing.value) {
        form.put(route('repair-orders.update', props.repairOrder.id), {
            onSuccess: () => {
                // Show success notification
                if (window.toast) {
                    window.toast.success('Repair order updated successfully!');
                } else {
                    alert('Repair order updated successfully!');
                }
                emit('saved');
                closeModal();
            },
            onError: (errors) => {
                console.error('Repair order update failed:', errors);
                // Show error notification
                if (window.toast) {
                    window.toast.error('Failed to update repair order. Please try again.');
                } else {
                    alert('Failed to update repair order. Please try again.');
                }
            },
        });
    } else {
        form.post(route('repair-orders.store'), {
            onSuccess: () => {
                // Show success notification
                if (window.toast) {
                    window.toast.success('Repair order created successfully!');
                } else {
                    alert('Repair order created successfully!');
                }
                emit('saved');
                closeModal();
            },
            onError: (errors) => {
                console.error('Repair order creation failed:', errors);
                // Show error notification
                if (window.toast) {
                    window.toast.error('Failed to create repair order. Please try again.');
                } else {
                    alert('Failed to create repair order. Please try again.');
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
    selectedCustomer.value = null;
    emit('close');
};

// Parts management functions
const addPart = () => {
    form.selected_parts.push({
        part_id: '',
        quantity: 1,
        unit_price: 0
    });
};

const addSuggestedPart = (part) => {
    // Check if part is already added
    const existingPart = form.selected_parts.find(p => p.part_id == part.id);
    if (existingPart) {
        // Increase quantity if already added
        existingPart.quantity = parseInt(existingPart.quantity) + 1;
    } else {
        // Add new part
        form.selected_parts.push({
            part_id: part.id,
            quantity: 1,
            unit_price: part.selling_price
        });
    }
};

const addAllSuggestedParts = () => {
    suggestedParts.value.forEach(part => {
        const existingPart = form.selected_parts.find(p => p.part_id == part.id);
        if (!existingPart) {
            form.selected_parts.push({
                part_id: part.id,
                quantity: 1,
                unit_price: part.selling_price
            });
        }
    });
};

const removePart = (index) => {
    form.selected_parts.splice(index, 1);
};

const updatePartPrice = (index) => {
    const selectedPart = props.parts.find(p => p.id == form.selected_parts[index].part_id);
    if (selectedPart) {
        form.selected_parts[index].unit_price = selectedPart.selling_price;
    }
};

const getTotalPartsPrice = () => {
    return form.selected_parts.reduce((total, part) => {
        return total + (parseFloat(part.unit_price || 0) * parseInt(part.quantity || 0));
    }, 0);
};

const getTotalServicesPrice = () => {
    return form.services.reduce((total, service) => {
        return total + parseFloat(service.service_price || 0);
    }, 0);
};

const getTotalCost = () => {
    return getTotalServicesPrice() + getTotalPartsPrice();
};

const getPartStock = (partId) => {
    const part = props.parts.find(p => p.id == partId);
    return part ? part.quantity_in_stock : 0;
};

const isQuantityValid = (partId, quantity) => {
    const availableStock = getPartStock(partId);
    return quantity <= availableStock;
};

const getPriorityColor = (priority) => {
    const colors = {
        'low': 'bg-green-100 text-green-800 border-green-200',
        'medium': 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'high': 'bg-orange-100 text-orange-800 border-orange-200',
        'urgent': 'bg-red-100 text-red-800 border-red-200'
    };
    return colors[priority] || colors.medium;
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
                class="inline-block w-full max-w-5xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-2xl rounded-2xl"
                @click.stop
            >
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-green-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">
                                {{ isEditing ? 'Edit Repair Order' : 'Create New Repair Order' }}
                            </h3>
                            <p class="text-sm text-gray-400">
                                {{ isEditing ? 'Update repair order information' : 'Create a new service request' }}
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
                <div class="p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Customer and Device Selection -->
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
                                <label for="device_id" class="block text-sm font-medium text-gray-300 mb-2">
                                    Device *
                                </label>
                                <select
                                    id="device_id"
                                    v-model="form.device_id"
                                    class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                    :disabled="!form.customer_id"
                                    required
                                >
                                    <option value="" class="bg-gray-800 text-white">
                                        {{ form.customer_id ? 'Select device...' : 'Select customer first' }}
                                    </option>
                                    <option
                                        v-for="device in filteredDevices"
                                        :key="device.id"
                                        :value="device.id"
                                        class="bg-gray-800 text-white"
                                    >
                                        {{ device.brand }} {{ device.model }} ({{ device.device_type?.name }})
                                    </option>
                                </select>
                                <div v-if="form.errors.device_id" class="mt-1 text-sm text-red-400">
                                    {{ form.errors.device_id }}
                                </div>
                            </div>
                        </div>

                    <!-- Multiple Services Section -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <label class="block text-sm font-medium text-gray-300">Services Required *</label>
                            <button
                                type="button"
                                @click="addService"
                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-sm transition-colors duration-200 flex items-center space-x-1"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <span>Add Service</span>
                            </button>
                        </div>

                        <div v-for="(service, index) in form.services" :key="index" class="mb-4 p-4 bg-gray-800 rounded-lg border border-gray-700">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-white font-medium">Service {{ index + 1 }}</h4>
                                <button
                                    v-if="form.services.length > 1"
                                    type="button"
                                    @click="removeService(index)"
                                    class="text-red-400 hover:text-red-300 transition-colors duration-200"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Service Type *</label>
                                    <select
                                        v-model="service.service_id"
                                        @change="updateServicePrice(index, service.service_id)"
                                        class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500"
                                        required
                                    >
                                        <option value="">Select service...</option>
                                        <option v-for="availableService in filteredServices" :key="availableService.id" :value="availableService.id">
                                            {{ availableService.name }} - ₱{{ availableService.base_price }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors[`services.${index}.service_id`]" class="mt-2 text-sm text-red-400">
                                        {{ form.errors[`services.${index}.service_id`] }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Service Price</label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        v-model="service.service_price"
                                        @input="index === 0 && (form.labor_cost = parseFloat(service.service_price) || 0)"
                                        class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500"
                                        placeholder="Custom price (optional)"
                                    />
                                    <div v-if="form.errors[`services.${index}.service_price`]" class="mt-2 text-sm text-red-400">
                                        {{ form.errors[`services.${index}.service_price`] }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-300 mb-2">Service Notes</label>
                                <textarea
                                    v-model="service.service_notes"
                                    rows="2"
                                    class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="Any specific notes for this service..."
                                ></textarea>
                            </div>
                        </div>
                    </div>

                        <!-- Technician Assignment -->
                        <div>
                            <label for="technician_id" class="block text-sm font-medium text-gray-300 mb-2">
                                Assigned Technician
                            </label>
                            <select
                                id="technician_id"
                                v-model="form.technician_id"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                            >
                                <option value="" class="bg-gray-800 text-white">Assign later...</option>
                                <option
                                    v-for="technician in technicians"
                                    :key="technician.id"
                                    :value="technician.id"
                                    class="bg-gray-800 text-white"
                                >
                                    {{ technician.user?.name }} ({{ technician.specialization }})
                                </option>
                            </select>
                            <div v-if="form.errors.technician_id" class="mt-1 text-sm text-red-400">
                                {{ form.errors.technician_id }}
                            </div>
                        </div>



                    <!-- Priority and Estimates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-300 mb-2">
                                Priority Level *
                            </label>
                            <select
                                id="priority"
                                v-model="form.priority"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                required
                            >
                                <option value="low" class="bg-gray-800 text-white">Low Priority</option>
                                <option value="medium" class="bg-gray-800 text-white">Medium Priority</option>
                                <option value="high" class="bg-gray-800 text-white">High Priority</option>
                                <option value="urgent" class="bg-gray-800 text-white">Urgent</option>
                            </select>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border" :class="getPriorityColor(form.priority)">
                                    {{ form.priority.toUpperCase() }}
                                </span>
                            </div>
                        </div>



                        <div>
                            <label for="estimated_completion" class="block text-sm font-medium text-gray-300 mb-2">
                                Estimated Completion
                            </label>
                            <input
                                id="estimated_completion"
                                v-model="form.estimated_completion"
                                type="date"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                            />
                            <div v-if="form.errors.estimated_completion" class="mt-1 text-sm text-red-400">
                                {{ form.errors.estimated_completion }}
                            </div>
                        </div>
                    </div>

                    <!-- Issue Description -->
                    <div>
                        <label for="issue_description" class="block text-sm font-medium text-gray-300 mb-2">
                            Issue Description *
                        </label>
                        <textarea
                            id="issue_description"
                            v-model="form.issue_description"
                            rows="4"
                            class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                            placeholder="Describe the issue or problem with the device..."
                            required
                        ></textarea>
                        <div v-if="form.errors.issue_description" class="mt-1 text-sm text-red-400">
                            {{ form.errors.issue_description }}
                        </div>
                    </div>

                    <!-- Parts Selection (Optional) -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <label class="block text-sm font-medium text-gray-300">
                                Parts Required (Optional)
                            </label>
                            <button
                                type="button"
                                @click="addPart"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors duration-200 flex items-center space-x-1"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <span>Add Part</span>
                            </button>
                        </div>

                        <!-- Suggested Parts for Selected Device -->
                        <div v-if="form.device_id && (suggestedParts.length > 0 || isLoadingParts)" class="mb-6">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-medium text-gray-300 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                    </svg>
                                    Suggested Parts for This Device
                                </h4>
                                <button
                                    v-if="suggestedParts.length > 0"
                                    type="button"
                                    @click="addAllSuggestedParts"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs transition-colors duration-200"
                                >
                                    Add All
                                </button>
                            </div>

                            <div v-if="isLoadingParts" class="text-center py-4">
                                <div class="inline-flex items-center text-gray-400">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Loading suggested parts...
                                </div>
                            </div>

                            <div v-else-if="suggestedParts.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div
                                    v-for="part in suggestedParts"
                                    :key="part.id"
                                    class="bg-gray-800 border border-gray-700 rounded-lg p-3 hover:border-green-500 transition-colors duration-200"
                                >
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-white">{{ part.name }}</span>
                                            <span v-if="part.type === 'device-specific'" class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-900 text-green-300 border border-green-600">
                                                Device Specific
                                            </span>
                                            <span v-else class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-900 text-blue-300 border border-blue-600">
                                                Compatible
                                            </span>
                                        </div>
                                        <button
                                            type="button"
                                            @click="addSuggestedPart(part)"
                                            class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs transition-colors duration-200"
                                        >
                                            Add
                                        </button>
                                    </div>
                                    <div class="text-xs text-gray-400 mb-1">{{ part.part_number }}</div>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-400">Stock: {{ part.quantity_in_stock }}</span>
                                        <span class="text-green-400 font-medium">₱{{ parseFloat(part.selling_price || 0).toFixed(2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="text-center py-4 text-gray-400 text-sm">
                                No specific parts found for this device. You can still add parts manually below.
                            </div>
                        </div>

                        <div v-if="form.selected_parts.length === 0" class="text-sm text-gray-400 italic text-center py-4 border border-gray-700 rounded-lg">
                            No parts selected. Click "Add Part" to include parts in this repair order.
                        </div>

                        <div v-else class="space-y-3">
                            <div
                                v-for="(part, index) in form.selected_parts"
                                :key="index"
                                class="grid grid-cols-1 md:grid-cols-4 gap-3 p-3 bg-gray-800 rounded-lg border border-gray-700"
                            >
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-1">Part</label>
                                    <select
                                        v-model="part.part_id"
                                        @change="updatePartPrice(index)"
                                        class="w-full rounded bg-gray-700 border-gray-600 text-white text-sm focus:border-red-500 focus:ring-red-500"
                                        required
                                    >
                                        <option value="">Select part...</option>
                                        <option
                                            v-for="availablePart in filteredParts"
                                            :key="availablePart.id"
                                            :value="availablePart.id"
                                        >
                                            {{ availablePart.name }} ({{ availablePart.part_number }})
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-1">
                                        Quantity
                                        <span v-if="part.part_id" class="text-xs text-gray-500">
                                            (Stock: {{ getPartStock(part.part_id) }})
                                        </span>
                                    </label>
                                    <input
                                        v-model="part.quantity"
                                        type="number"
                                        min="1"
                                        :max="part.part_id ? getPartStock(part.part_id) : undefined"
                                        :class="[
                                            'w-full rounded bg-gray-700 border text-white text-sm focus:ring-red-500 transition-colors duration-200',
                                            part.part_id && !isQuantityValid(part.part_id, part.quantity)
                                                ? 'border-red-500 focus:border-red-500'
                                                : 'border-gray-600 focus:border-red-500'
                                        ]"
                                        required
                                    />
                                    <div v-if="part.part_id && !isQuantityValid(part.part_id, part.quantity)" class="mt-1 text-xs text-red-400">
                                        Insufficient stock! Available: {{ getPartStock(part.part_id) }}
                                    </div>
                                    <div v-if="part.part_id && getPartStock(part.part_id) === 0" class="mt-1 text-xs text-red-400">
                                        ⚠️ This part is out of stock!
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-1">Unit Price (₱)</label>
                                    <input
                                        v-model="part.unit_price"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="w-full rounded bg-gray-700 border-gray-600 text-white text-sm focus:border-red-500 focus:ring-red-500"
                                        required
                                    />
                                </div>
                                <div class="flex items-end">
                                    <button
                                        type="button"
                                        @click="removePart(index)"
                                        class="w-full bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-sm transition-colors duration-200"
                                    >
                                        Remove
                                    </button>
                                </div>
                            </div>

                            <div class="text-right space-y-1">
                                <div class="text-sm font-medium text-gray-300">
                                    Services Cost: <span class="text-blue-400">₱{{ getTotalServicesPrice().toFixed(2) }}</span>
                                </div>
                                <div class="text-sm font-medium text-gray-300">
                                    Parts Cost: <span class="text-green-400">₱{{ getTotalPartsPrice().toFixed(2) }}</span>
                                </div>
                                <div class="text-lg font-bold text-gray-200 border-t border-gray-600 pt-1">
                                    Total Cost: <span class="text-yellow-400">₱{{ getTotalCost().toFixed(2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Notes -->
                    <div>
                        <label for="customer_notes" class="block text-sm font-medium text-gray-300 mb-2">
                            Customer Notes
                        </label>
                        <textarea
                            id="customer_notes"
                            v-model="form.customer_notes"
                            rows="3"
                            class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                            placeholder="Additional notes from the customer..."
                        ></textarea>
                        <div v-if="form.errors.customer_notes" class="mt-1 text-sm text-red-400">
                            {{ form.errors.customer_notes }}
                        </div>
                    </div>

                    <!-- Edit Mode Fields -->
                    <div v-if="isEditing" class="space-y-6 border-t border-gray-700 pt-6">
                        <h4 class="text-lg font-medium text-white mb-4">Repair Details</h4>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-300 mb-2">
                                Status *
                            </label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                required
                            >
                                <option value="pending" class="bg-gray-800 text-white">Pending</option>
                                <option value="in_progress" class="bg-gray-800 text-white">In Progress</option>
                                <option value="waiting_parts" class="bg-gray-800 text-white">Waiting for Parts</option>
                                <option value="completed" class="bg-gray-800 text-white">Completed</option>
                                <option value="cancelled" class="bg-gray-800 text-white">Cancelled</option>
                                <option value="delivered" class="bg-gray-800 text-white">Delivered</option>
                            </select>
                            <div v-if="form.errors.status" class="mt-1 text-sm text-red-400">
                                {{ form.errors.status }}
                            </div>
                        </div>

                        <!-- Diagnosis -->
                        <!-- <div>
                            <label for="diagnosis" class="block text-sm font-medium text-gray-300 mb-2">
                                Diagnosis
                            </label>
                            <textarea
                                id="diagnosis"
                                v-model="form.diagnosis"
                                rows="3"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="Technical diagnosis of the issue..."
                            ></textarea>
                            <div v-if="form.errors.diagnosis" class="mt-1 text-sm text-red-400">
                                {{ form.errors.diagnosis }}
                            </div>
                        </div> -->

                        <!-- Solution -->
                        <!-- <div>
                            <label for="solution" class="block text-sm font-medium text-gray-300 mb-2">
                                Solution
                            </label>
                            <textarea
                                id="solution"
                                v-model="form.solution"
                                rows="3"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="Solution or repair steps taken..."
                            ></textarea>
                            <div v-if="form.errors.solution" class="mt-1 text-sm text-red-400">
                                {{ form.errors.solution }}
                            </div>
                        </div> -->

                        <!-- Internal Notes -->
                        <!-- <div>
                            <label for="internal_notes" class="block text-sm font-medium text-gray-300 mb-2">
                                Internal Notes
                            </label>
                            <textarea
                                id="internal_notes"
                                v-model="form.internal_notes"
                                rows="3"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="Internal notes for staff only..."
                            ></textarea>
                            <div v-if="form.errors.internal_notes" class="mt-1 text-sm text-red-400">
                                {{ form.errors.internal_notes }}
                            </div>
                        </div> -->
                    </div>

                    <!-- Selected Customer Info -->
                    <div v-if="selectedCustomer" class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                        <h4 class="text-sm font-medium text-gray-300 mb-2">Customer Information</h4>
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-lg">
                                <span class="text-sm font-bold text-white">{{ selectedCustomer.first_name?.charAt(0) }}{{ selectedCustomer.last_name?.charAt(0) }}</span>
                            </div>
                            <div>
                                <p class="text-white font-medium">{{ selectedCustomer.full_name }}</p>
                                <p class="text-sm text-gray-400">{{ selectedCustomer.email }}</p>
                                <p class="text-sm text-gray-400">{{ selectedCustomer.phone }}</p>
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
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                </svg>
                                {{ isEditing ? 'Updating...' : 'Creating...' }}
                            </span>
                            <span v-else>
                                {{ isEditing ? 'Update Order' : 'Create Repair Order' }}
                            </span>
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
