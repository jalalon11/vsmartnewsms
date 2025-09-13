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
// Initialize sidebar collapsed state from localStorage
const sidebarCollapsed = ref(localStorage.getItem('sidebarCollapsed') === 'true');
const isLoading = ref(false);

// Watch for changes to sidebarCollapsed and save to localStorage
const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
    localStorage.setItem('sidebarCollapsed', sidebarCollapsed.value.toString());
};

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
        href: 'dashboard',
        icon: 'M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586l-2 2V6H5v12h7v2H4a1 1 0 01-1-1V4z',
        description: 'Overview and statistics'
    },
    {
        name: 'Customers',
        href: 'customers.index',
        icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z',
        description: 'Manage customer information'
    },
    {
        name: 'Devices',
        href: 'devices.index',
        icon: 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',
        description: 'Device registration and tracking'
    },
    {
        name: 'Repair Orders',
        href: 'repair-orders.index',
        icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        description: 'Track repair progress'
    },
    {
        name: 'Technicians',
        href: 'technicians.index',
        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        description: 'Manage repair technicians'
    },
    {
        name: 'Services',
        href: 'services.index',
        icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        description: 'Manage repair services'
    },

    {
        name: 'Parts',
        href: 'parts.index',
        icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
        description: 'Inventory management'
    },
    {
        name: 'Invoices',
        href: 'invoices.index',
        icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        description: 'Billing and payments'
    },
    {
        name: 'Reports',
        href: 'reports.index',
        icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        description: 'Analytics and insights'
    },
    {
        name: 'Feedback',
        href: 'feedback.index',
        icon: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
        description: 'Customer feedback management'
    },
    {
        name: 'Settings',
        href: 'settings.index',
        icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        description: 'System configuration and settings'
    },
]);

// Define isCurrentRoute as a method
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
        <div class="fixed inset-y-0 left-0 z-50 bg-black border-r border-gray-800 transform transition-all duration-300 ease-in-out shadow-2xl lg:translate-x-0 flex flex-col" :class="{ 
            '-translate-x-full': !sidebarOpen,
            'w-64': !sidebarCollapsed,
            'w-16': sidebarCollapsed
        }">
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-4 bg-gradient-to-r from-red-600 to-red-700 border-b border-red-500">
                <Link :href="route('dashboard')" class="flex items-center" :class="{ 'justify-center w-full': sidebarCollapsed, 'space-x-3': !sidebarCollapsed }">
                    <ApplicationLogo class="w-8 h-8 text-white flex-shrink-0" />
                    <span v-show="!sidebarCollapsed" class="text-xl font-bold text-white transition-all duration-300 overflow-hidden whitespace-nowrap">AdminPanel</span>
                </Link>
                <!-- Collapse toggle button for desktop -->
                <button
                    v-show="!sidebarCollapsed"
                    @click="toggleSidebar"
                    class="hidden lg:block p-1 rounded-md text-white hover:bg-red-800 transition-colors duration-200 flex-shrink-0"
                >
                    <svg class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <!-- Expand button when collapsed -->
                <button
                    v-show="sidebarCollapsed"
                    @click="toggleSidebar"
                    class="hidden lg:block absolute right-2 p-1 rounded-md text-white hover:bg-red-800 transition-colors duration-200"
                >
                    <svg class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
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
            <div class="flex-1 flex flex-col overflow-hidden">
                <nav class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-600 scrollbar-track-gray-800 mt-8 px-4 space-y-2 pb-4">
                    <div class="mb-6" v-if="!sidebarCollapsed">
                        <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider transition-opacity duration-300">
                            Technician Tools
                        </h3>
                    </div>

                    <Link
                        v-for="item in navigationItems"
                        :key="item.name"
                        :href="route(item.href)"
                        class="group flex items-center text-sm font-medium text-gray-300 rounded-lg transition-all duration-200 hover:bg-red-600 hover:text-white hover:shadow-lg"
                        :class="{
                            'bg-red-600 text-white shadow-lg': isCurrentRoute(item.href),
                            'px-3 py-3': !sidebarCollapsed,
                            'px-2 py-3 justify-center': sidebarCollapsed
                        }"
                        :title="sidebarCollapsed ? item.name : ''"
                    >
                        <svg
                            class="h-5 w-5 transition-colors duration-200 flex-shrink-0"
                            :class="{
                                'text-white': isCurrentRoute(item.href),
                                'text-gray-400 group-hover:text-white': !isCurrentRoute(item.href),
                                'mr-3': !sidebarCollapsed
                            }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                        </svg>
                        <div v-if="!sidebarCollapsed" class="flex-1 transition-opacity duration-300">
                            <div class="font-medium">{{ item.name }}</div>
                            <div class="text-xs opacity-75">{{ item.description }}</div>
                        </div>
                    </Link>
                </nav>
            </div>

            <!-- User Info -->
            <div class="flex-shrink-0 p-3 border-t border-gray-800 bg-black">
                <div class="flex items-center" :class="{ 'justify-center': sidebarCollapsed, 'space-x-3': !sidebarCollapsed }">
                    <div class="flex-shrink-0 w-8 h-8 bg-red-600 rounded-full flex items-center justify-center" :title="sidebarCollapsed ? user?.name : ''">
                        <span class="text-sm font-medium text-white">{{ user?.name?.charAt(0) }}</span>
                    </div>
                    <div v-show="!sidebarCollapsed" class="flex-1 min-w-0 transition-all duration-300 overflow-hidden">
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
        <div class="transition-all duration-300" :class="{ 'lg:pl-64': !sidebarCollapsed, 'lg:pl-16': sidebarCollapsed }">
            <!-- Top navigation -->
            <div class="fixed top-0 left-0 right-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-800 bg-gradient-to-r from-gray-900 to-black px-4 shadow-lg sm:gap-x-6 sm:px-6 lg:px-8 transition-all duration-300" :class="{ 'lg:left-64': !sidebarCollapsed, 'lg:left-16': sidebarCollapsed }">
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
                                            <div class="text-xs text-gray-400">Admin</div>
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
            <main class="flex-1 pt-16"> <!-- Reverted to pt-16 -->
                <slot />
            </main>
        </div>

        <!-- Loading Spinner -->
        <LoadingSpinner :show="isLoading" />
    </div>
</template>