<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    technician: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close', 'saved']);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    employee_id: '',
    phone: '',
    specialization: '',
    hire_date: '',
    certifications: '',
    skills: '',
    notes: '',
    is_active: true,
});

const isEditing = ref(false);

watch(() => props.show, (newValue) => {
    if (newValue) {
        if (props.technician) {
            // Editing mode
            isEditing.value = true;
            form.name = props.technician.user?.name || '';
            form.email = props.technician.user?.email || '';
            form.employee_id = props.technician.employee_id || '';
            form.phone = props.technician.phone || '';
            form.specialization = props.technician.specialization || '';
            form.hire_date = props.technician.hire_date ? new Date(props.technician.hire_date).toISOString().split('T')[0] : '';
            form.certifications = props.technician.certifications || '';
            form.skills = props.technician.skills || '';
            form.notes = props.technician.notes || '';
            form.is_active = props.technician.is_active ?? true;
            // Don't populate password fields in edit mode
            form.password = '';
            form.password_confirmation = '';
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
        form.put(route('technicians.update', props.technician.id), {
            onSuccess: () => {
                // Show success notification
                if (window.toast) {
                    window.toast.success('Technician updated successfully!');
                }
                emit('saved');
                closeModal();
            },
            onError: (errors) => {
                console.error('Technician update failed:', errors);
                // Show error notification
                if (window.toast) {
                    window.toast.error('Failed to update technician. Please try again.');
                }
            },
        });
    } else {
        form.post(route('technicians.store'), {
            onSuccess: () => {
                // Show success notification
                if (window.toast) {
                    window.toast.success('Technician created successfully!');
                }
                emit('saved');
                closeModal();
            },
            onError: (errors) => {
                console.error('Technician creation failed:', errors);
                // Show error notification
                if (window.toast) {
                    window.toast.error('Failed to create technician. Please try again.');
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

const specializations = [
    'Mobile Device Repair',
    'Computer Hardware',
    'Laptop Repair',
    'Gaming Console Repair',
    'Tablet Repair',
    'Printer Repair',
    'Network Equipment',
    'Audio Equipment',
    'General Electronics',
    'Data Recovery',
    'Software Troubleshooting',
    'Other'
];
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
                        <div class="p-2 bg-blue-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">
                                {{ isEditing ? 'Edit Technician' : 'Add New Technician' }}
                            </h3>
                            <p class="text-sm text-gray-400">
                                {{ isEditing ? 'Update technician information' : 'Create a new technician profile' }}
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
                    <!-- Personal Information -->
                    <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                        <h4 class="text-lg font-semibold text-white mb-4">Personal Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                                    Full Name *
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                    placeholder="John Doe"
                                    required
                                />
                                <div v-if="form.errors.name" class="mt-1 text-sm text-red-400">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                                    Email Address *
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                    placeholder="john@example.com"
                                    required
                                />
                                <div v-if="form.errors.email" class="mt-1 text-sm text-red-400">
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-300 mb-2">
                                    Phone Number
                                </label>
                                <input
                                    id="phone"
                                    v-model="form.phone"
                                    type="tel"
                                    class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                    placeholder="+63 9XX XXX XXXX"
                                />
                                <div v-if="form.errors.phone" class="mt-1 text-sm text-red-400">
                                    {{ form.errors.phone }}
                                </div>
                            </div>

                            <div>
                                <label for="employee_id" class="block text-sm font-medium text-gray-300 mb-2">
                                    Employee ID *
                                </label>
                                <input
                                    id="employee_id"
                                    v-model="form.employee_id"
                                    type="text"
                                    class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                    placeholder="EMP001"
                                    required
                                />
                                <div v-if="form.errors.employee_id" class="mt-1 text-sm text-red-400">
                                    {{ form.errors.employee_id }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information (only for new technicians) -->
                    <div v-if="!isEditing" class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                        <h4 class="text-lg font-semibold text-white mb-4">Account Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                                    Password *
                                </label>
                                <input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                    required
                                />
                                <div v-if="form.errors.password" class="mt-1 text-sm text-red-400">
                                    {{ form.errors.password }}
                                </div>
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">
                                    Confirm Password *
                                </label>
                                <input
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                    required
                                />
                                <div v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-400">
                                    {{ form.errors.password_confirmation }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Information -->
                    <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                        <h4 class="text-lg font-semibold text-white mb-4">Professional Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="specialization" class="block text-sm font-medium text-gray-300 mb-2">
                                    Specialization *
                                </label>
                                <select
                                    id="specialization"
                                    v-model="form.specialization"
                                    class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                    required
                                >
                                    <option value="" class="bg-gray-700 text-white">Select specialization...</option>
                                    <option 
                                        v-for="spec in specializations" 
                                        :key="spec" 
                                        :value="spec"
                                        class="bg-gray-700 text-white"
                                    >
                                        {{ spec }}
                                    </option>
                                </select>
                                <div v-if="form.errors.specialization" class="mt-1 text-sm text-red-400">
                                    {{ form.errors.specialization }}
                                </div>
                            </div>



                            <div>
                                <label for="hire_date" class="block text-sm font-medium text-gray-300 mb-2">
                                    Hire Date *
                                </label>
                                <input
                                    id="hire_date"
                                    v-model="form.hire_date"
                                    type="date"
                                    class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                    required
                                />
                                <div v-if="form.errors.hire_date" class="mt-1 text-sm text-red-400">
                                    {{ form.errors.hire_date }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="certifications" class="block text-sm font-medium text-gray-300 mb-2">
                                    Certifications
                                </label>
                                <textarea
                                    id="certifications"
                                    v-model="form.certifications"
                                    rows="3"
                                    class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                    placeholder="List relevant certifications..."
                                ></textarea>
                                <div v-if="form.errors.certifications" class="mt-1 text-sm text-red-400">
                                    {{ form.errors.certifications }}
                                </div>
                            </div>

                            <div>
                                <label for="skills" class="block text-sm font-medium text-gray-300 mb-2">
                                    Skills & Expertise
                                </label>
                                <textarea
                                    id="skills"
                                    v-model="form.skills"
                                    rows="3"
                                    class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                    placeholder="List technical skills and expertise..."
                                ></textarea>
                                <div v-if="form.errors.skills" class="mt-1 text-sm text-red-400">
                                    {{ form.errors.skills }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="notes" class="block text-sm font-medium text-gray-300 mb-2">
                                Additional Notes
                            </label>
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="3"
                                class="w-full rounded-lg bg-gray-700 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
                                placeholder="Any additional notes about the technician..."
                            ></textarea>
                            <div v-if="form.errors.notes" class="mt-1 text-sm text-red-400">
                                {{ form.errors.notes }}
                            </div>
                        </div>

                        <!-- Status Toggle (only for editing) -->
                        <div v-if="isEditing" class="mt-6">
                            <label class="flex items-center">
                                <input
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="rounded bg-gray-700 border-gray-600 text-red-600 shadow-sm focus:border-red-500 focus:ring-red-500"
                                />
                                <span class="ml-2 text-sm text-gray-300">Active Technician</span>
                            </label>
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
                                {{ isEditing ? 'Update Technician' : 'Create Technician' }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
