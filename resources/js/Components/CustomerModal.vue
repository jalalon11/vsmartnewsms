<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    customer: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close', 'saved']);

const form = useForm({
    first_name: '',
    last_name: '',
    facebook_link: '',
    phone: '',
    address: '',
    city: '',
    state: '',
    zip_code: '',
    notes: '',
});

const isEditing = ref(false);

watch(() => props.show, (newValue) => {
    if (newValue) {
        if (props.customer) {
            // Editing mode
            isEditing.value = true;
            form.first_name = props.customer.first_name || '';
            form.last_name = props.customer.last_name || '';
            form.facebook_link = props.customer.facebook_link || '';
            form.phone = props.customer.phone || '';
            form.address = props.customer.address || '';
            form.city = props.customer.city || '';
            form.state = props.customer.state || '';
            form.zip_code = props.customer.zip_code || '';
            form.notes = props.customer.notes || '';
        } else {
            // Creating mode
            isEditing.value = false;
            form.reset();
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
        form.put(route('customers.update', props.customer.id), {
            onSuccess: () => {
                // Show success notification
                if (window.toast) {
                    window.toast.success('Customer updated successfully!');
                }
                emit('saved');
                closeModal();
            },
            onError: (errors) => {
                console.error('Customer update failed:', errors);
                // Show error notification
                if (window.toast) {
                    window.toast.error('Failed to update customer. Please try again.');
                }
            },
        });
    } else {
        form.post(route('customers.store'), {
            onSuccess: () => {
                // Show success notification
                if (window.toast) {
                    window.toast.success('Customer created successfully!');
                }
                emit('saved');
                closeModal();
            },
            onError: (errors) => {
                console.error('Customer creation failed:', errors);
                // Show error notification
                if (window.toast) {
                    window.toast.error('Failed to create customer. Please try again.');
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

// Phone number formatting function
const formatPhoneNumber = (value) => {
    // Remove all non-digits
    let cleaned = value.replace(/\D/g, '');

    // If it starts with 63, keep it as is
    if (cleaned.startsWith('63')) {
        return '+' + cleaned;
    }

    // If it starts with 0, replace with 63
    if (cleaned.startsWith('0')) {
        return '+63' + cleaned.substring(1);
    }

    // If it's just the local number (9 digits), add +63
    if (cleaned.length === 10 && cleaned.startsWith('9')) {
        return '+63' + cleaned;
    }

    // If it's 9 digits starting with 9, add +63
    if (cleaned.length === 9 && cleaned.startsWith('9')) {
        return '+639' + cleaned.substring(1);
    }

    // Otherwise, add +63 prefix
    return '+63' + cleaned;
};

const handlePhoneInput = (event) => {
    const formatted = formatPhoneNumber(event.target.value);
    form.phone = formatted;
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
                class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-2xl rounded-2xl"
                @click.stop
            >
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-red-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">
                                {{ isEditing ? 'Edit Customer' : 'Add New Customer' }}
                            </h3>
                            <p class="text-sm text-gray-400">
                                {{ isEditing ? 'Update customer information' : 'Enter customer details below' }}
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
                    <!-- Name Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-300 mb-2">
                                First Name *
                            </label>
                            <input
                                id="first_name"
                                v-model="form.first_name"
                                type="text"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="Enter first name"
                                required
                            />
                            <div v-if="form.errors.first_name" class="mt-1 text-sm text-red-400">
                                {{ form.errors.first_name }}
                            </div>
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-300 mb-2">
                                Last Name *
                            </label>
                            <input
                                id="last_name"
                                v-model="form.last_name"
                                type="text"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="Enter last name"
                                required
                            />
                            <div v-if="form.errors.last_name" class="mt-1 text-sm text-red-400">
                                {{ form.errors.last_name }}
                            </div>
                        </div>
                    </div>

                    <!-- Contact Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="facebook_link" class="block text-sm font-medium text-gray-300 mb-2">
                                Facebook Profile Link *
                            </label>
                            <input
                                id="facebook_link"
                                v-model="form.facebook_link"
                                type="url"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="https://facebook.com/username"
                                required
                            />
                            <div v-if="form.errors.facebook_link" class="mt-1 text-sm text-red-400">
                                {{ form.errors.facebook_link }}
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-300 mb-2">
                                Phone Number *
                            </label>
                            <input
                                id="phone"
                                v-model="form.phone"
                                @input="handlePhoneInput"
                                type="tel"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="+63 9XX XXX XXXX"
                                required
                            />
                            <div class="text-xs text-gray-400 mt-1">Format: +63 9XX XXX XXXX</div>
                            <div v-if="form.errors.phone" class="mt-1 text-sm text-red-400">
                                {{ form.errors.phone }}
                            </div>
                        </div>
                    </div>

                    <!-- Address Field -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-300 mb-2">
                            Street Address
                        </label>
                        <input
                            id="address"
                            v-model="form.address"
                            type="text"
                            class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                            placeholder="123 Main Street"
                        />
                        <div v-if="form.errors.address" class="mt-1 text-sm text-red-400">
                            {{ form.errors.address }}
                        </div>
                    </div>

                    <!-- Location Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-300 mb-2">
                                City
                            </label>
                            <input
                                id="city"
                                v-model="form.city"
                                type="text"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="City"
                            />
                            <div v-if="form.errors.city" class="mt-1 text-sm text-red-400">
                                {{ form.errors.city }}
                            </div>
                        </div>

                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-300 mb-2">
                                State
                            </label>
                            <input
                                id="state"
                                v-model="form.state"
                                type="text"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="State"
                            />
                            <div v-if="form.errors.state" class="mt-1 text-sm text-red-400">
                                {{ form.errors.state }}
                            </div>
                        </div>

                        <div>
                            <label for="zip_code" class="block text-sm font-medium text-gray-300 mb-2">
                                ZIP Code
                            </label>
                            <input
                                id="zip_code"
                                v-model="form.zip_code"
                                type="text"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="12345"
                            />
                            <div v-if="form.errors.zip_code" class="mt-1 text-sm text-red-400">
                                {{ form.errors.zip_code }}
                            </div>
                        </div>
                    </div>

                    <!-- Notes Field -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-300 mb-2">
                            Notes
                        </label>
                        <textarea
                            id="notes"
                            v-model="form.notes"
                            rows="3"
                            class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                            placeholder="Additional notes about the customer..."
                        ></textarea>
                        <div v-if="form.errors.notes" class="mt-1 text-sm text-red-400">
                            {{ form.errors.notes }}
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
                                {{ isEditing ? 'Update Customer' : 'Create Customer' }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
