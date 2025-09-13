<script setup>
import TechnicianLayout from '@/Layouts/TechnicianLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    technician: {
        type: Object,
        required: true,
    },
    isAdminView: {
        type: Boolean,
        default: false
    },
});
</script>

<template>
    <Head title="Technician Profile" />

    <TechnicianLayout :isAdminView="isAdminView">
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">
                            Technician Profile
                            <span v-if="isAdminView" class="text-sm text-blue-300 ml-2">(Admin View)</span>
                        </h2>
                        <p class="text-gray-400 text-sm">Manage your technician account settings</p>
                    </div>
                </div>
            </div>
        </template>

        <div class="p-6">
            <div class="max-w-4xl mx-auto space-y-6">
                <!-- Profile Information Card -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-700">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-red-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Profile Information</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="max-w-xl"
                        />
                    </div>
                </div>

                <!-- Technician Details Card -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden" v-if="technician">
                    <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-700">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-blue-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Technician Details</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-gray-800 rounded-lg border border-gray-700">
                                    <div>
                                        <h4 class="text-white font-medium">Employee ID</h4>
                                        <p class="text-gray-400 text-sm">{{ technician.employee_id }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-gray-800 rounded-lg border border-gray-700">
                                    <div>
                                        <h4 class="text-white font-medium">Specialization</h4>
                                        <p class="text-gray-400 text-sm">{{ technician.specialization }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-gray-800 rounded-lg border border-gray-700">
                                    <div>
                                        <h4 class="text-white font-medium">Status</h4>
                                        <p class="text-gray-400 text-sm">{{ technician.is_active ? 'Active' : 'Inactive' }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full" 
                                          :class="technician.is_active ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'">
                                        {{ technician.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-gray-800 rounded-lg border border-gray-700">
                                    <div>
                                        <h4 class="text-white font-medium">Role</h4>
                                        <p class="text-gray-400 text-sm">Technician</p>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                        Technician
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Password Update Card -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-700">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-yellow-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Update Password</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <UpdatePasswordForm class="max-w-xl" />
                    </div>
                </div>

                <!-- Danger Zone Card -->
                <div class="bg-gradient-to-br from-red-900 to-red-800 border border-red-700 rounded-xl shadow-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-red-700 bg-gradient-to-r from-red-800 to-red-700">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-red-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Danger Zone</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <DeleteUserForm class="max-w-xl" />
                    </div>
                </div>
            </div>
        </div>
    </TechnicianLayout>
</template>
