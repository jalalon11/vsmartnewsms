<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    state: '',
    zip_code: '',
    notes: '',
});

const submit = () => {
    form.post(route('customers.store'));
};
</script>

<template>
    <Head title="Add New Customer" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">
                Add New Customer
            </h2>
        </template>

        <div class="p-6">
            <div class="max-w-2xl mx-auto">
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-300">First Name</label>
                                <input
                                    id="first_name"
                                    v-model="form.first_name"
                                    type="text"
                                    class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500"
                                    required
                                />
                                <div v-if="form.errors.first_name" class="mt-2 text-sm text-red-400">{{ form.errors.first_name }}</div>
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-300">Last Name</label>
                                <input
                                    id="last_name"
                                    v-model="form.last_name"
                                    type="text"
                                    class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500"
                                    required
                                />
                                <div v-if="form.errors.last_name" class="mt-2 text-sm text-red-400">{{ form.errors.last_name }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500"
                                    required
                                />
                                <div v-if="form.errors.email" class="mt-2 text-sm text-red-400">{{ form.errors.email }}</div>
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-300">Phone</label>
                                <input
                                    id="phone"
                                    v-model="form.phone"
                                    type="tel"
                                    class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500"
                                    required
                                />
                                <div v-if="form.errors.phone" class="mt-2 text-sm text-red-400">{{ form.errors.phone }}</div>
                            </div>
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-300">Address</label>
                            <input
                                id="address"
                                v-model="form.address"
                                type="text"
                                class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500"
                            />
                            <div v-if="form.errors.address" class="mt-2 text-sm text-red-400">{{ form.errors.address }}</div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-300">City</label>
                                <input
                                    id="city"
                                    v-model="form.city"
                                    type="text"
                                    class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500"
                                />
                                <div v-if="form.errors.city" class="mt-2 text-sm text-red-400">{{ form.errors.city }}</div>
                            </div>

                            <div>
                                <label for="state" class="block text-sm font-medium text-gray-300">State</label>
                                <input
                                    id="state"
                                    v-model="form.state"
                                    type="text"
                                    class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500"
                                />
                                <div v-if="form.errors.state" class="mt-2 text-sm text-red-400">{{ form.errors.state }}</div>
                            </div>

                            <div>
                                <label for="zip_code" class="block text-sm font-medium text-gray-300">ZIP Code</label>
                                <input
                                    id="zip_code"
                                    v-model="form.zip_code"
                                    type="text"
                                    class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500"
                                />
                                <div v-if="form.errors.zip_code" class="mt-2 text-sm text-red-400">{{ form.errors.zip_code }}</div>
                            </div>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-300">Notes</label>
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="3"
                                class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500"
                            ></textarea>
                            <div v-if="form.errors.notes" class="mt-2 text-sm text-red-400">{{ form.errors.notes }}</div>
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <button
                                type="button"
                                @click="$inertia.visit(route('customers.index'))"
                                class="px-4 py-2 border border-gray-600 rounded-md text-gray-300 hover:bg-gray-800 transition-colors duration-200"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50 transition-colors duration-200"
                            >
                                <span v-if="form.processing">Creating...</span>
                                <span v-else>Create Customer</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
