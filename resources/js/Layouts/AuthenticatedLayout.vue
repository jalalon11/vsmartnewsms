<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const sidebarOpen = ref(false);
const sidebarCollapsed = ref(false);
const isLoading = ref(false);
const isTransitioning = ref(false);

// Get current route for active state checking
const page = usePage();
const currentRoute = computed(() => page.url);

// Load sidebar state from localStorage on mount
const initializeSidebar = () => {
    const savedCollapsedState = localStorage.getItem('sidebarCollapsed');
    if (savedCollapsedState !== null) {
        sidebarCollapsed.value = JSON.parse(savedCollapsedState);
    }

    // Listen for Inertia navigation events
    router.on('start', () => {
        isLoading.value = true;
    });

    router.on('finish', () => {
        isLoading.value = false;
    });
};



const navigationItems = [
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
];

// Optimized toggle functions with debouncing
let toggleTimeout = null;

const toggleSidebar = () => {
    if (isTransitioning.value) return;

    isTransitioning.value = true;
    sidebarOpen.value = !sidebarOpen.value;

    // Reset transition flag after animation completes
    setTimeout(() => {
        isTransitioning.value = false;
    }, 300);
};

const toggleCollapse = () => {
    if (isTransitioning.value) return;

    isTransitioning.value = true;
    sidebarCollapsed.value = !sidebarCollapsed.value;

    // Batch localStorage update
    if (toggleTimeout) clearTimeout(toggleTimeout);
    toggleTimeout = setTimeout(() => {
        localStorage.setItem('sidebarCollapsed', JSON.stringify(sidebarCollapsed.value));
    }, 100);

    if (sidebarCollapsed.value) {
        sidebarOpen.value = false;
    }

    // Reset transition flag after animation completes
    setTimeout(() => {
        isTransitioning.value = false;
    }, 300);
};

// Debounced resize handler
let resizeTimeout = null;
const handleResize = () => {
    if (resizeTimeout) clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
        if (window.innerWidth >= 1024) {
            sidebarOpen.value = false;
        }
    }, 150);
};

// Optimized route checking
const isActiveRoute = (href) => {
    return currentRoute.value.startsWith(route(href));
};

// Watch for route changes and preserve sidebar state
watch(currentRoute, () => {
    // Preserve sidebar state during navigation
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState !== null) {
        const parsedState = JSON.parse(savedState);
        if (sidebarCollapsed.value !== parsedState) {
            sidebarCollapsed.value = parsedState;
        }
    }
});

// Performance monitoring (development only)
const logPerformance = (action) => {
    if (process.env.NODE_ENV === 'development') {
        console.log(`Sidebar ${action} - Performance:`, {
            timestamp: performance.now(),
            memory: performance.memory?.usedJSHeapSize || 'N/A'
        });
    }
};

onMounted(() => {
    // Initialize sidebar state
    initializeSidebar();

    // Add resize listener
    window.addEventListener('resize', handleResize, { passive: true });

    // Log performance
    logPerformance('mounted');
});

onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
    if (toggleTimeout) clearTimeout(toggleTimeout);
    if (resizeTimeout) clearTimeout(resizeTimeout);
    logPerformance('unmounted');
});
</script>

<template>
    <div class="min-h-screen bg-black">
        <!-- Sidebar -->
        <div
            class="fixed inset-y-0 left-0 z-50 bg-black border-r border-gray-800 shadow-2xl sidebar-optimized sidebar-transition reduce-paint flex flex-col"
            :class="[
                sidebarCollapsed ? 'w-16' : 'w-64',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
            ]"
        >
            <!-- Header -->
            <div class="flex items-center justify-between h-16 bg-gradient-to-r from-red-600 to-red-700 border-b border-red-500">
                <Link :href="route('dashboard')" class="flex items-center px-4" v-if="!sidebarCollapsed">
                    <ApplicationLogo class="block h-8 w-auto fill-current text-white" />
                    <span class="ml-2 text-xl font-bold text-white">VSMART SMS</span>
                </Link>
                <Link :href="route('dashboard')" class="flex items-center justify-center w-full" v-else>
                    <ApplicationLogo class="block h-8 w-auto fill-current text-white" />
                </Link>

                <!-- Collapse button (desktop only) -->
                <button
                    @click="toggleCollapse"
                    class="hidden lg:flex items-center justify-center w-8 h-8 mr-2 text-white hover:bg-red-800 rounded-lg collapse-btn"
                    :disabled="isTransitioning"
                >
                    <svg class="w-4 h-4 transform transition-transform duration-200" :class="{ 'rotate-180': sidebarCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 px-3 sidebar-scroll flex-1 overflow-y-auto pb-20">
                <div class="space-y-1">
                    <Link
                        v-for="item in navigationItems"
                        :key="item.name"
                        :href="route(item.href)"
                        class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg nav-item-optimized relative"
                        :class="[
                            isActiveRoute(item.href)
                                ? 'bg-red-600 text-white shadow-lg'
                                : 'text-gray-300 hover:bg-red-600 hover:text-white',
                            sidebarCollapsed ? 'justify-center' : ''
                        ]"
                        :title="sidebarCollapsed ? item.name : ''"
                    >
                        <svg class="flex-shrink-0 w-5 h-5" :class="sidebarCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                        </svg>
                        <div v-if="!sidebarCollapsed" class="flex-1 nav-text-transition">
                            <div class="font-medium">{{ item.name }}</div>
                            <div class="text-xs text-gray-400 group-hover:text-gray-200">{{ item.description }}</div>
                        </div>

                        <!-- Tooltip for collapsed state -->
                        <div v-if="sidebarCollapsed" class="absolute left-16 bg-gray-900 text-white px-2 py-1 rounded-md text-xs opacity-0 group-hover:opacity-100 tooltip-optimized whitespace-nowrap z-50">
                            {{ item.name }}
                        </div>
                    </Link>
                </div>
            </nav>

            <!-- User info at bottom -->
            <div class="absolute bottom-0 left-0 right-0 p-3 border-t border-gray-800">
                <div class="flex items-center" :class="sidebarCollapsed ? 'justify-center' : ''">
                    <div class="flex-shrink-0 w-8 h-8 bg-red-600 rounded-full flex items-center justify-center">
                        <span class="text-sm font-medium text-white">{{ $page.props.auth.user.name.charAt(0) }}</span>
                    </div>
                    <div v-if="!sidebarCollapsed" class="ml-3 flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ $page.props.auth.user.name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $page.props.auth.user.role || 'User' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile sidebar overlay -->
        <div v-if="sidebarOpen" class="fixed inset-0 z-40 lg:hidden" @click="sidebarOpen = false">
            <div class="absolute inset-0 bg-black bg-opacity-75 mobile-overlay"></div>
        </div>

        <!-- Main content -->
        <div
            class="main-content-optimized reduce-paint"
            :class="sidebarCollapsed ? 'lg:ml-16' : 'lg:ml-64'"
        >
            <!-- Top navigation -->
            <div class="bg-gradient-to-r from-gray-900 to-black border-b border-gray-800 shadow-lg">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <!-- Mobile menu button -->
                        <button
                            @click="toggleSidebar"
                            class="lg:hidden inline-flex items-center justify-center p-2 rounded-lg text-white hover:text-red-500 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-red-500 transition-colors duration-200"
                        >
                            <svg class="h-6 w-6 transform transition-transform duration-200" :class="{ 'rotate-90': sidebarOpen }" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <!-- Breadcrumb or page title -->
                        <div class="flex-1 lg:ml-4">
                            <div class="flex items-center space-x-2 text-sm text-gray-400">
                                <!-- <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                                </svg>
                                <span>VSMART TUNE UP</span> -->
                            </div>
                        </div>

                        <!-- User menu and notifications -->
                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <!-- <button class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.07 2.82l3.12 3.12M7.05 5.84l3.12 3.12M4.03 8.86l3.12 3.12M1.01 11.88l3.12 3.12" />
                                </svg>
                            </button> -->

                            <!-- User menu -->
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button class="flex items-center space-x-3 p-2 rounded-lg text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200">
                                        <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium">{{ $page.props.auth.user.name.charAt(0) }}</span>
                                        </div>
                                        <div class="hidden md:block text-left">
                                            <div class="text-sm font-medium">{{ $page.props.auth.user.name }}</div>
                                            <div class="text-xs text-gray-400">{{ $page.props.auth.user.role || 'User' }}</div>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <div class="py-1">
                                        <DropdownLink :href="route('profile.edit')" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-red-500 hover:text-white transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            Profile
                                        </DropdownLink>
                                        <hr class="border-gray-200">
                                        <DropdownLink :href="route('logout')" method="post" as="button" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-red-500 hover:text-white transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Log Out
                                        </DropdownLink>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-gradient-to-r from-gray-900 to-black border-b border-gray-800 shadow-sm">
                <div class="px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="bg-black min-h-screen">
                <slot />
            </main>
        </div>

        <!-- Loading Spinner -->
        <LoadingSpinner :show="isLoading" />
    </div>
</template>
