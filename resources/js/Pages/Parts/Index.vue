<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PartModal from '@/Components/PartModal.vue';
import StockUpdateModal from '@/Components/StockUpdateModal.vue';
import PartStatusModal from '@/Components/PartStatusModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    parts: Object,
    categories: Array,
    devices: Array,
    filters: Object,
});

// Modal states
const showPartModal = ref(false);
const showStockModal = ref(false);
const showStatusModal = ref(false);
const showDeleteModal = ref(false);
const editingPart = ref(null);
const stockPart = ref(null);
const statusPart = ref(null);
const deletingPart = ref(null);
const isDeleting = ref(false);

// Search and filter states
const searchQuery = ref(props.filters?.search || '');
const categoryFilter = ref(props.filters?.category || 'all');
const stockStatusFilter = ref(props.filters?.stock_status || 'all');
const statusFilter = ref(props.filters?.status || 'all');
const sortField = ref(props.filters?.sort || 'name');
const sortDirection = ref(props.filters?.direction || 'asc');

// Computed properties
const filteredParts = computed(() => {
    if (!searchQuery.value && categoryFilter.value === 'all' && stockStatusFilter.value === 'all' && statusFilter.value === 'all') {
        return props.parts.data;
    }
    
    return props.parts.data?.filter(part => {
        const query = searchQuery.value.toLowerCase();
        const matchesSearch = !query || 
            part.name?.toLowerCase().includes(query) ||
            part.part_number?.toLowerCase().includes(query) ||
            part.description?.toLowerCase().includes(query) ||
            part.supplier?.toLowerCase().includes(query);
        
        const matchesCategory = categoryFilter.value === 'all' || part.category === categoryFilter.value;
        const matchesStatus = statusFilter.value === 'all' || 
            (statusFilter.value === 'active' && part.is_active) ||
            (statusFilter.value === 'inactive' && !part.is_active);
        
        let matchesStockStatus = true;
        if (stockStatusFilter.value === 'low_stock') {
            matchesStockStatus = part.quantity_in_stock <= part.minimum_stock_level;
        } else if (stockStatusFilter.value === 'out_of_stock') {
            matchesStockStatus = part.quantity_in_stock === 0;
        } else if (stockStatusFilter.value === 'in_stock') {
            matchesStockStatus = part.quantity_in_stock > 0;
        }
        
        return matchesSearch && matchesCategory && matchesStatus && matchesStockStatus;
    }) || [];
});

// Watch for search changes and debounce
let searchTimeout;
watch(searchQuery, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        updateFilters();
    }, 300);
});

// Functions
const openCreateModal = () => {
    editingPart.value = null;
    showPartModal.value = true;
};

const openEditModal = (part) => {
    editingPart.value = part;
    showPartModal.value = true;
};

const openStockModal = (part) => {
    stockPart.value = part;
    showStockModal.value = true;
};

const openStatusModal = (part) => {
    statusPart.value = part;
    showStatusModal.value = true;
};

const openDeleteModal = (part) => {
    deletingPart.value = part;
    showDeleteModal.value = true;
};

const handlePartSaved = () => {
    showPartModal.value = false;
    router.reload({ only: ['parts'] });
};

const handleStockUpdated = () => {
    showStockModal.value = false;
    router.reload({ only: ['parts'] });
};

const handleStatusUpdated = () => {
    showStatusModal.value = false;
    router.reload({ only: ['parts'] });
};

const handleDeleteConfirm = () => {
    if (!deletingPart.value) return;
    
    isDeleting.value = true;
    router.delete(route('parts.destroy', deletingPart.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            deletingPart.value = null;
            isDeleting.value = false;
        },
        onError: () => {
            isDeleting.value = false;
        },
    });
};

const handleDeleteCancel = () => {
    showDeleteModal.value = false;
    deletingPart.value = null;
    isDeleting.value = false;
};

const updateFilters = () => {
    const params = {
        search: searchQuery.value || undefined,
        category: categoryFilter.value !== 'all' ? categoryFilter.value : undefined,
        stock_status: stockStatusFilter.value !== 'all' ? stockStatusFilter.value : undefined,
        status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
        sort: sortField.value,
        direction: sortDirection.value,
    };
    
    router.get(route('parts.index'), params, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    categoryFilter.value = 'all';
    stockStatusFilter.value = 'all';
    statusFilter.value = 'all';
    updateFilters();
};

const sort = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    updateFilters();
};

const getStatusColor = (status) => {
    switch(status) {
        case 'ordered': return 'bg-yellow-900 text-yellow-300 border-yellow-600';
        case 'in_transit': return 'bg-blue-900 text-blue-300 border-blue-600';
        case 'in_stock': return 'bg-green-900 text-green-300 border-green-600';
        default: return 'bg-gray-900 text-gray-300 border-gray-600';
    }
};

const getStockStatusColor = (part) => {
    if (part.quantity_in_stock === 0) {
        return 'bg-red-100 text-red-800 border-red-200';
    } else if (part.quantity_in_stock <= part.minimum_stock_level) {
        return 'bg-yellow-100 text-yellow-800 border-yellow-200';
    } else {
        return 'bg-green-100 text-green-800 border-green-200';
    }
};

const getStockStatusText = (part) => {
    if (part.quantity_in_stock === 0) {
        return 'Out of Stock';
    } else if (part.quantity_in_stock <= part.minimum_stock_level) {
        return 'Low Stock';
    } else {
        return 'In Stock';
    }
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 2
    }).format(amount || 0);
};
</script>

<template>
    <Head title="Parts Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-1">
                        Parts Management
                    </h2>
                    <p class="text-gray-400 text-sm">Manage inventory and spare parts</p>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">
                    <!-- Search Bar -->
                    <div class="relative flex-1 max-w-md">
                        <input 
                            v-model="searchQuery"
                            type="text" 
                            placeholder="Search parts..." 
                            class="w-full bg-gray-800 border border-gray-700 text-white placeholder-gray-400 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors duration-200"
                        >
                        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <button 
                            v-if="searchQuery"
                            @click="searchQuery = ''"
                            class="absolute right-3 top-2.5 text-gray-400 hover:text-white"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Filter Dropdowns -->
                    <div class="flex items-center space-x-2">
                        <select 
                            v-model="categoryFilter"
                            @change="updateFilters"
                            class="bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                            <option value="all">All Categories</option>
                            <option v-for="category in categories" :key="category" :value="category">
                                {{ category }}
                            </option>
                        </select>

                        <select 
                            v-model="stockStatusFilter"
                            @change="updateFilters"
                            class="bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                            <option value="all">All Stock</option>
                            <option value="in_stock">In Stock</option>
                            <option value="low_stock">Low Stock</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>

                        <select 
                            v-model="statusFilter"
                            @change="updateFilters"
                            class="bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <!-- Clear Filters -->
                    <div class="flex items-center space-x-2">
                        <button
                            @click="clearFilters"
                            v-if="searchQuery || categoryFilter !== 'all' || stockStatusFilter !== 'all' || statusFilter !== 'all'"
                            class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200"
                        >
                            Clear
                        </button>
                        <button
                            @click="openCreateModal"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span>Add Part</span>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <div class="p-6 space-y-6">
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Total Parts</p>
                            <p class="text-2xl font-bold text-white">{{ parts.total || 0 }}</p>
                        </div>
                        <div class="p-2 bg-blue-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Low Stock</p>
                            <p class="text-2xl font-bold text-white">{{ parts.data?.filter(p => p.quantity_in_stock <= p.minimum_stock_level).length || 0 }}</p>
                        </div>
                        <div class="p-2 bg-yellow-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Out of Stock</p>
                            <p class="text-2xl font-bold text-white">{{ parts.data?.filter(p => p.quantity_in_stock === 0).length || 0 }}</p>
                        </div>
                        <div class="p-2 bg-red-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Total Value</p>
                            <p class="text-2xl font-bold text-white">{{ formatCurrency(parts.data?.reduce((sum, p) => sum + (p.selling_price * p.quantity_in_stock), 0) || 0) }}</p>
                        </div>
                        <div class="p-2 bg-green-600 rounded-lg">
                            <span class="text-white text-xl font-bold">₱</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parts Table -->
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-red-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Parts Inventory</h3>
                        </div>
                        <span class="text-sm text-gray-400">{{ parts.data?.length || 0 }} parts</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:text-white" @click="sort('part_number')">
                                    Part Number
                                    <svg v-if="sortField === 'part_number'" class="inline w-4 h-4 ml-1" :class="{ 'transform rotate-180': sortDirection === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:text-white" @click="sort('name')">
                                    Name
                                    <svg v-if="sortField === 'name'" class="inline w-4 h-4 ml-1" :class="{ 'transform rotate-180': sortDirection === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:text-white" @click="sort('category')">
                                    Category
                                    <svg v-if="sortField === 'category'" class="inline w-4 h-4 ml-1" :class="{ 'transform rotate-180': sortDirection === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    Associated Device
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:text-white" @click="sort('quantity_in_stock')">
                                    Stock
                                    <svg v-if="sortField === 'quantity_in_stock'" class="inline w-4 h-4 ml-1" :class="{ 'transform rotate-180': sortDirection === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:text-white" @click="sort('selling_price')">
                                    Price
                                    <svg v-if="sortField === 'selling_price'" class="inline w-4 h-4 ml-1" :class="{ 'transform rotate-180': sortDirection === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-900 divide-y divide-gray-700">
                            <tr v-for="part in parts.data" :key="part.id" class="hover:bg-gray-800 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                                    {{ part.part_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-white font-medium">{{ part.name }}</div>
                                    <div class="text-sm text-gray-400">{{ part.supplier }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ part.category }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div v-if="part.device" class="text-sm">
                                        <div class="text-white font-medium">{{ part.device.customer.first_name }} {{ part.device.customer.last_name }}</div>
                                        <div class="text-gray-400">{{ part.device.brand }} {{ part.device.model }}</div>
                                    </div>
                                    <div v-else class="text-sm text-gray-500 italic">
                                        No specific device
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-white">{{ part.quantity_in_stock }}</span>
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full border" :class="getStockStatusColor(part)">
                                            {{ getStockStatusText(part) }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-400">Min: {{ part.minimum_stock_level }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                                    {{ formatCurrency(part.selling_price) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col space-y-1">
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full border" :class="getStatusColor(part.status)">
                                            {{ part.status?.replace('_', ' ').toUpperCase() || 'IN STOCK' }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full border"
                                              :class="part.is_active ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200'">
                                            {{ part.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-1">
                                        <Link 
                                            :href="route('parts.show', part.id)" 
                                            class="p-2 text-blue-400 hover:text-blue-300 hover:bg-blue-400 hover:bg-opacity-10 rounded-lg transition-all duration-200"
                                            title="View Details"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </Link>
                                        <button 
                                            @click="openEditModal(part)"
                                            class="p-2 text-yellow-400 hover:text-yellow-300 hover:bg-yellow-400 hover:bg-opacity-10 rounded-lg transition-all duration-200"
                                            title="Edit Part"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            @click="openStockModal(part)"
                                            class="p-2 text-green-400 hover:text-green-300 hover:bg-green-400 hover:bg-opacity-10 rounded-lg transition-all duration-200"
                                            title="Update Stock"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-10 0a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2M9 12l2 2 4-4" />
                                            </svg>
                                        </button>
                                        <button
                                            @click="openStatusModal(part)"
                                            class="p-2 text-purple-400 hover:text-purple-300 hover:bg-purple-400 hover:bg-opacity-10 rounded-lg transition-all duration-200"
                                            title="Update Status"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                        <button
                                            @click="openDeleteModal(part)"
                                            class="p-2 text-red-400 hover:text-red-300 hover:bg-red-400 hover:bg-opacity-10 rounded-lg transition-all duration-200"
                                            title="Delete Part"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="parts.links" class="bg-gray-800 px-4 py-3 border-t border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="parts.prev_page_url" :href="parts.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-800 hover:bg-gray-700">
                                Previous
                            </Link>
                            <Link v-if="parts.next_page_url" :href="parts.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-800 hover:bg-gray-700">
                                Next
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-400">
                                    Showing {{ parts.from }} to {{ parts.to }} of {{ parts.total }} results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <template v-for="link in parts.links" :key="link.label">
                                        <Link v-if="link.url" :href="link.url"
                                              class="relative inline-flex items-center px-2 py-2 border text-sm font-medium"
                                              :class="{
                                                  'z-10 bg-red-600 border-red-600 text-white': link.active,
                                                  'bg-gray-800 border-gray-600 text-gray-300 hover:bg-gray-700': !link.active
                                              }"
                                              v-html="link.label">
                                        </Link>
                                        <span v-else
                                              class="relative inline-flex items-center px-2 py-2 border text-sm font-medium cursor-not-allowed opacity-50 bg-gray-800 border-gray-600 text-gray-500"
                                              v-html="link.label">
                                        </span>
                                    </template>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Part Modal -->
        <PartModal
            :show="showPartModal"
            :part="editingPart"
            :devices="devices"
            @close="showPartModal = false"
            @saved="handlePartSaved"
        />

        <!-- Stock Update Modal -->
        <StockUpdateModal
            :show="showStockModal"
            :part="stockPart"
            @close="showStockModal = false"
            @saved="handleStockUpdated"
        />

        <!-- Status Update Modal -->
        <PartStatusModal
            :show="showStatusModal"
            :part="statusPart"
            @close="showStatusModal = false"
            @saved="handleStatusUpdated"
        />

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal
            :show="showDeleteModal"
            :processing="isDeleting"
            title="Delete Part"
            :message="`Are you sure you want to delete ${deletingPart?.name}? This action cannot be undone.`"
            confirm-text="Delete Part"
            cancel-text="Cancel"
            type="danger"
            @confirm="handleDeleteConfirm"
            @cancel="handleDeleteCancel"
        />
    </AuthenticatedLayout>
</template>
