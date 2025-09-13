<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    customers: Array,
    services: Array,
    technicians: Array,
});

const form = useForm({
    customer_id: '',
    device_id: '',
    services: [{ service_id: '', service_price: '', service_notes: '' }],
    technician_id: '',
    issue_description: '',
    priority: 'medium',
    customer_notes: '',
    estimated_completion: '',
    selected_parts: [],
});

const customerDevices = ref([]);
const selectedCustomer = ref(null);
const filteredServices = ref([]);

const fetchCustomerDevices = async (customerId) => {
    if (!customerId) {
        customerDevices.value = [];
        return;
    }
    
    try {
        const response = await fetch(`/customers/${customerId}/devices`);
        customerDevices.value = await response.json();
    } catch (error) {
        console.error('Error fetching customer devices:', error);
        customerDevices.value = [];
    }
};

watch(() => form.customer_id, (newCustomerId) => {
    form.device_id = '';
    selectedCustomer.value = props.customers.find(c => c.id == newCustomerId);
    fetchCustomerDevices(newCustomerId);
});

watch(() => form.device_id, (newDeviceId) => {
    if (newDeviceId) {
        const device = customerDevices.value.find(d => d.id == newDeviceId);
        if (device && device.device_type) {
            filteredServices.value = props.services.filter(s => s.device_type_id == device.device_type.id);
        }
    } else {
        filteredServices.value = [];
    }
    // Reset services when device changes
    form.services = [{ service_id: '', service_price: '', service_notes: '' }];
});

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
    if (serviceId && !form.services[index].service_price) {
        form.services[index].service_price = getServicePrice(serviceId);
    }
};

const submit = () => {
    form.post(route('repair-orders.store'));
};

const getPriorityColor = (priority) => {
    const colors = {
        'low': 'bg-gray-100 text-gray-800 border-gray-300',
        'medium': 'bg-blue-100 text-blue-800 border-blue-300',
        'high': 'bg-orange-100 text-orange-800 border-orange-300',
        'urgent': 'bg-red-100 text-red-800 border-red-300'
    };
    return colors[priority] || colors.medium;
};
</script>

<template>
    <Head title="Create Repair Order" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <div class="p-2 bg-red-600 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white">Create Repair Order</h2>
                    <p class="text-gray-400 text-sm">Start a new repair service request</p>
                </div>
            </div>
        </template>

        <div class="p-6">
            <div class="max-w-4xl mx-auto">
                <form @submit.prevent="submit" class="space-y-8">
                    <!-- Customer Selection -->
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-blue-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Customer Information</h3>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <label for="customer_id" class="block text-sm font-medium text-gray-300 mb-2">Select Customer *</label>
                                <select
                                    id="customer_id"
                                    v-model="form.customer_id"
                                    class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500"
                                    required
                                >
                                    <option value="">Choose a customer...</option>
                                    <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                        {{ customer.full_name }} - {{ customer.phone }}
                                    </option>
                                </select>
                                <div v-if="form.errors.customer_id" class="mt-2 text-sm text-red-400">{{ form.errors.customer_id }}</div>
                            </div>

                            <div v-if="selectedCustomer" class="bg-gray-800 rounded-lg p-4 border border-gray-600">
                                <h4 class="text-sm font-medium text-gray-300 mb-2">Customer Details</h4>
                                <div class="space-y-1 text-sm text-gray-400">
                                    <p><span class="text-gray-300">Email:</span> {{ selectedCustomer.email }}</p>
                                    <p><span class="text-gray-300">Phone:</span> {{ selectedCustomer.phone }}</p>
                                    <p v-if="selectedCustomer.address"><span class="text-gray-300">Address:</span> {{ selectedCustomer.address }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-if="form.customer_id" class="mt-6">
                            <label for="device_id" class="block text-sm font-medium text-gray-300 mb-2">Select Device *</label>
                            <select
                                id="device_id"
                                v-model="form.device_id"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500"
                                required
                            >
                                <option value="">Choose a device...</option>
                                <option v-for="device in customerDevices" :key="device.id" :value="device.id">
                                    {{ device.brand }} {{ device.model }} ({{ device.device_type?.name }})
                                </option>
                            </select>
                            <div v-if="form.errors.device_id" class="mt-2 text-sm text-red-400">{{ form.errors.device_id }}</div>
                            
                            <div v-if="customerDevices.length === 0 && form.customer_id" class="mt-2 p-3 bg-yellow-900 border border-yellow-700 rounded-lg">
                                <p class="text-yellow-300 text-sm">No devices found for this customer. You may need to register a device first.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Service Details -->
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-green-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Service Details</h3>
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
                                                {{ availableService.name }} - ${{ availableService.base_price }}
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

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                            <div>
                                <label for="technician_id" class="block text-sm font-medium text-gray-300 mb-2">Assign Technician</label>
                                <select
                                    id="technician_id"
                                    v-model="form.technician_id"
                                    class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500"
                                >
                                    <option value="">Assign later...</option>
                                    <option v-for="technician in technicians" :key="technician.id" :value="technician.id">
                                        {{ technician.user?.name }} - {{ technician.specialization }}
                                    </option>
                                </select>
                                <div v-if="form.errors.technician_id" class="mt-2 text-sm text-red-400">{{ form.errors.technician_id }}</div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="priority" class="block text-sm font-medium text-gray-300 mb-2">Priority Level</label>
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                                <label v-for="priority in ['low', 'medium', 'high', 'urgent']" :key="priority" 
                                       class="relative cursor-pointer">
                                    <input
                                        type="radio"
                                        :value="priority"
                                        v-model="form.priority"
                                        class="sr-only"
                                    />
                                    <div class="p-3 rounded-lg border-2 text-center transition-all duration-200"
                                         :class="form.priority === priority ? getPriorityColor(priority) : 'bg-gray-800 border-gray-600 text-gray-400 hover:border-gray-500'">
                                        <div class="font-medium capitalize">{{ priority }}</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Problem Description -->
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-orange-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Problem Description</h3>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label for="issue_description" class="block text-sm font-medium text-gray-300 mb-2">Describe the Problem *</label>
                                <textarea
                                    id="issue_description"
                                    v-model="form.issue_description"
                                    rows="4"
                                    class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="Please describe the issue with the device in detail..."
                                    required
                                ></textarea>
                                <div v-if="form.errors.issue_description" class="mt-2 text-sm text-red-400">{{ form.errors.issue_description }}</div>
                            </div>

                            <div>
                                <label for="customer_notes" class="block text-sm font-medium text-gray-300 mb-2">Customer Notes</label>
                                <textarea
                                    id="customer_notes"
                                    v-model="form.customer_notes"
                                    rows="3"
                                    class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="Any additional notes from the customer..."
                                ></textarea>
                                <div v-if="form.errors.customer_notes" class="mt-2 text-sm text-red-400">{{ form.errors.customer_notes }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-4 pt-6">
                        <button
                            type="button"
                            @click="$inertia.visit(route('repair-orders.index'))"
                            class="px-6 py-3 border border-gray-600 rounded-lg text-gray-300 hover:bg-gray-800 transition-colors duration-200 font-medium"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 disabled:opacity-50 transition-all duration-200 shadow-lg hover:shadow-xl font-medium"
                        >
                            <span v-if="form.processing" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                </svg>
                                Creating Order...
                            </span>
                            <span v-else>Create Repair Order</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
