<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';

const props = defineProps({
    isAdminView: {
        type: Boolean,
        default: false
    }
});

const showingNavigationDropdown = ref(false);
const sidebarOpen = ref(false);
const isLoading = ref(false);

const page = usePage();
const user = computed(() => page.props.auth.user);

onMounted(() => {
    // Listen for Inertia navigation events
    router.on('start', () => {
        isLoading.value = true;
    });

    router.on('finish', () => {
        isLoading.value = false;
    });
});

// Technician-specific navigation items
const navigationItems = computed(() => [
    {
        name: 'Dashboard',
        href: props.isAdminView ? 'dashboard.technician-view' : 'dashboard',
        icon: 'M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z',
        description: 'Your work dashboard'
    },
    {
        name: 'My Orders',
        href: 'technician.orders',
        icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        description: 'Assigned repair orders'
    },
    {
        name: 'Profile',
        href: 'profile.edit',
        icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        description: 'Update your profile'
    }
]);

const isCurrentRoute = (routeName) => {
    return route().current(routeName);
};
</script>

<template>
    <div class="min-h-screen bg-black">
        <!-- Mobile menu overlay -->
        <div v-if="sidebarOpen" class="fixed inset-0 z-40 lg:hidden" @click="sidebarOpen = false">
            <div class="fixed inset-0 bg-black opacity-75"></div>
        </div>

        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-black border-r border-gray-800 transform transition-transform duration-300 ease-in-out shadow-2xl lg:translate-x-0" :class="{ '-translate-x-full': !sidebarOpen }">
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-4 bg-gradient-to-r from-red-600 to-red-700 border-b border-red-500">
                <Link :href="route('dashboard')" class="flex items-center space-x-3">
                    <ApplicationLogo class="w-8 h-8 text-white" />
                    <span class="text-xl font-bold text-white">TechPanel</span>
                </Link>
                <!-- Close button for mobile -->
                <button
                    @click="sidebarOpen = false"
                    class="lg:hidden p-1 rounded-md text-white hover:bg-red-800 transition-colors duration-200"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="mt-8 px-4 space-y-2">
                <div class="mb-6">
                    <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Technician Tools
                    </h3>
                </div>

                <Link
                    v-for="item in navigationItems"
                    :key="item.name"
                    :href="route(item.href)"
                    class="group flex items-center px-3 py-3 text-sm font-medium text-gray-300 rounded-lg transition-all duration-200 hover:bg-red-600 hover:text-white hover:shadow-lg"
                    :class="{
                        'bg-red-600 text-white shadow-lg': isCurrentRoute(item.href)
                    }"
                >
                    <svg
                        class="mr-3 h-5 w-5 transition-colors duration-200"
                        :class="{
                            'text-white': isCurrentRoute(item.href),
                            'text-gray-400 group-hover:text-white': !isCurrentRoute(item.href)
                        }"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                    </svg>
                    <div class="flex-1">
                        <div class="font-medium">{{ item.name }}</div>
                        <div class="text-xs opacity-75">{{ item.description }}</div>
                    </div>
                </Link>
            </nav>

            <!-- User Info -->
            <div class="absolute bottom-0 left-0 right-0 p-3 border-t border-gray-800">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-red-600 rounded-full flex items-center justify-center">
                        <span class="text-sm font-medium text-white">{{ user?.name?.charAt(0) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ user?.name }}</p>
                        <p class="text-xs text-gray-400 truncate">Technician</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile sidebar overlay -->
        <div v-if="sidebarOpen" class="fixed inset-0 z-40 lg:hidden" @click="sidebarOpen = false">
            <div class="fixed inset-0 bg-black opacity-75 transition-opacity duration-300"></div>
        </div>

        <!-- Main content -->
        <div class="lg:pl-64">
            <!-- Top navigation -->
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-800 bg-gradient-to-r from-gray-900 to-black px-4 shadow-lg sm:gap-x-6 sm:px-6 lg:px-8">
                <!-- Mobile menu button -->
                <button
                    type="button"
                    class="-m-2.5 p-2.5 text-gray-400 hover:text-white lg:hidden transition-colors duration-200"
                    @click="sidebarOpen = !sidebarOpen"
                >
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Mobile logo -->
                <div class="flex lg:hidden items-center space-x-3">
                    <ApplicationLogo class="w-6 h-6 text-white" />
                    <span class="text-lg font-bold text-white">TechPanel</span>
                </div>

                <!-- Separator -->
                <div class="h-6 w-px bg-gray-800 lg:hidden" />

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="flex flex-1 items-center">
                        <!-- Page header will be inserted here -->
                        <header v-if="$slots.header" class="flex-1">
                            <slot name="header" />
                        </header>
                    </div>
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <!-- Profile dropdown -->
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button
                                        type="button"
                                        class="flex items-center space-x-3 p-2 rounded-lg text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200"
                                    >
                                        <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium">{{ user?.name?.charAt(0) }}</span>
                                        </div>
                                        <div class="hidden sm:block">
                                            <div class="text-sm font-medium">{{ user?.name }}</div>
                                            <div class="text-xs text-gray-400">Technician</div>
                                        </div>
                                        <svg
                                            class="ml-2 -mr-0.5 h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </button>
                                </span>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>

            <!-- Page content -->
            <main class="flex-1">
                <slot />
            </main>
        </div>

        <!-- Loading Spinner -->
        <LoadingSpinner :show="isLoading" />
    </div>
</template>
