<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    invoices: Object,
    stats: Object,
    filters: Object,
});

// Reactive filters
const searchQuery = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || 'all');
const dateFromFilter = ref(props.filters?.date_from || '');
const dateToFilter = ref(props.filters?.date_to || '');
const sortBy = ref(props.filters?.sort || 'created_at');
const sortDirection = ref(props.filters?.direction || 'desc');

// Bulk operations
const selectedInvoices = ref([]);
const selectAll = ref(false);
const showBulkActions = ref(false);

// Debounced search
let searchTimeout;
watch(searchQuery, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        updateFilters();
    }, 300);
});

const updateFilters = () => {
    const params = {
        search: searchQuery.value || undefined,
        status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
        date_from: dateFromFilter.value || undefined,
        date_to: dateToFilter.value || undefined,
        sort: sortBy.value !== 'created_at' ? sortBy.value : undefined,
        direction: sortDirection.value !== 'desc' ? sortDirection.value : undefined,
    };
    
    router.get(route('invoices.index'), params, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = 'all';
    dateFromFilter.value = '';
    dateToFilter.value = '';
    sortBy.value = 'created_at';
    sortDirection.value = 'desc';
    updateFilters();
};

const sortTable = (field) => {
    if (sortBy.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortBy.value = field;
        sortDirection.value = 'asc';
    }
    updateFilters();
};

const getStatusColor = (status) => {
    const colors = {
        'pending': 'text-yellow-400 bg-yellow-900/50 border-yellow-700',
        'paid': 'text-green-400 bg-green-900/50 border-green-700',
        'partially_paid': 'text-orange-400 bg-orange-900/50 border-orange-700',
        'cancelled': 'text-gray-400 bg-gray-900/50 border-gray-700',
    };
    return colors[status] || 'text-gray-400 bg-gray-900/50 border-gray-700';
};

const formatCurrency = (amount) => {
    return '₱' + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2 });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const deleteInvoice = (invoice) => {
    if (confirm(`Are you sure you want to delete invoice ${invoice.invoice_number}?`)) {
        router.delete(route('invoices.destroy', invoice.id), {
            onSuccess: () => {
                if (window.toast) {
                    window.toast.success('Invoice deleted successfully!');
                }
            }
        });
    }
};

// Bulk operations functions
const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedInvoices.value = props.invoices.data.map(invoice => invoice.id);
    } else {
        selectedInvoices.value = [];
    }
    showBulkActions.value = selectedInvoices.value.length > 0;
};

const toggleInvoiceSelection = (invoiceId) => {
    const index = selectedInvoices.value.indexOf(invoiceId);
    if (index > -1) {
        selectedInvoices.value.splice(index, 1);
    } else {
        selectedInvoices.value.push(invoiceId);
    }
    selectAll.value = selectedInvoices.value.length === props.invoices.data.length;
    showBulkActions.value = selectedInvoices.value.length > 0;
};

const bulkMarkAsPaid = () => {
    if (confirm(`Mark ${selectedInvoices.value.length} invoices as paid?`)) {
        router.post(route('invoices.bulk-mark-paid'), {
            invoice_ids: selectedInvoices.value
        }, {
            onSuccess: () => {
                selectedInvoices.value = [];
                selectAll.value = false;
                showBulkActions.value = false;
            }
        });
    }
};

const bulkDelete = () => {
    if (confirm(`Delete ${selectedInvoices.value.length} invoices? This action cannot be undone.`)) {
        router.delete(route('invoices.bulk-delete'), {
            data: { invoice_ids: selectedInvoices.value },
            onSuccess: () => {
                selectedInvoices.value = [];
                selectAll.value = false;
                showBulkActions.value = false;
            }
        });
    }
};
</script>

<template>
    <Head title="Invoices" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-white">
                    Invoices
                </h2>
                <div class="text-sm text-gray-400">
                    Generate invoices from completed repair orders
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-900/50 rounded-lg">
                                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Total Invoices</p>
                                <p class="text-2xl font-bold text-white">{{ stats.total }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <div class="flex items-center">
                            <div class="p-2 bg-yellow-900/50 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Pending</p>
                                <p class="text-2xl font-bold text-white">{{ stats.pending }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-900/50 rounded-lg">
                                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Paid</p>
                                <p class="text-2xl font-bold text-white">{{ stats.paid }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <div class="flex items-center">
                            <div class="p-2 bg-orange-900/50 rounded-lg">
                                <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M11 3.055A9.001 9.001 0 0021 12h-9V3.055zM12 21a9 9 0 110-18v9h9a9 9 0 01-9 9z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Partially Paid</p>
                                <p class="text-2xl font-bold text-white">{{ stats.partially_paid }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters and Search -->
                <div class="bg-gray-800 rounded-xl shadow-2xl border border-gray-700 p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                        <div class="md:col-span-2">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search invoices..."
                                class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            />
                        </div>

                        <div>
                            <select 
                                v-model="statusFilter"
                                @change="updateFilters"
                                class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            >
                                <option value="all">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="partially_paid">Partially Paid</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>

                        <div>
                            <input
                                v-model="dateFromFilter"
                                @change="updateFilters"
                                type="date"
                                class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            />
                        </div>

                        <div>
                            <input
                                v-model="dateToFilter"
                                @change="updateFilters"
                                type="date"
                                class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            />
                        </div>

                        <div>
                            <button
                                @click="clearFilters"
                                v-if="searchQuery || statusFilter !== 'all' || dateFromFilter || dateToFilter"
                                class="w-full bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200"
                            >
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bulk Actions Bar -->
                <div v-if="showBulkActions" class="bg-blue-900/20 border border-blue-700 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="text-blue-400 font-medium">{{ selectedInvoices.length }} invoice(s) selected</span>
                            <button @click="selectedInvoices = []; selectAll = false; showBulkActions = false"
                                    class="text-gray-400 hover:text-white text-sm">
                                Clear selection
                            </button>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button @click="bulkMarkAsPaid"
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                                Mark as Paid
                            </button>
                            <button @click="bulkDelete"
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                                Delete Selected
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Invoices Table -->
                <div class="bg-gray-800 rounded-xl shadow-2xl border border-gray-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-900">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-12">
                                        <input type="checkbox"
                                               v-model="selectAll"
                                               @change="toggleSelectAll"
                                               class="rounded border-gray-600 bg-gray-700 text-red-600 focus:ring-red-500 focus:ring-offset-gray-800">
                                    </th>
                                    <th @click="sortTable('invoice_number')" class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-800 transition-colors duration-200 w-32">
                                        <div class="flex items-center space-x-1">
                                            <span>Invoice #</span>
                                            <svg v-if="sortBy === 'invoice_number'" class="w-4 h-4" :class="sortDirection === 'asc' ? 'transform rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-32">Repair Order</th>
                                    <th @click="sortTable('issue_date')" class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-800 transition-colors duration-200 w-28">
                                        <div class="flex items-center space-x-1">
                                            <span>Issue Date</span>
                                            <svg v-if="sortBy === 'issue_date'" class="w-4 h-4" :class="sortDirection === 'asc' ? 'transform rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </th>
                                    <th @click="sortTable('total_amount')" class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-800 transition-colors duration-200 w-24">
                                        <div class="flex items-center space-x-1">
                                            <span>Amount</span>
                                            <svg v-if="sortBy === 'total_amount'" class="w-4 h-4" :class="sortDirection === 'asc' ? 'transform rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-24">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-32">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                <tr v-for="invoice in invoices.data" :key="invoice.id" class="hover:bg-gray-750 transition-colors duration-200">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <input type="checkbox"
                                               :value="invoice.id"
                                               v-model="selectedInvoices"
                                               @change="toggleInvoiceSelection(invoice.id)"
                                               class="rounded border-gray-600 bg-gray-700 text-red-600 focus:ring-red-500 focus:ring-offset-gray-800">
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-white">{{ invoice.invoice_number }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="text-sm text-white">{{ invoice.customer.first_name }} {{ invoice.customer.last_name }}</div>
                                                <div class="text-xs text-gray-400">{{ invoice.customer.email }}</div>
                                            </div>
                                            <Link
                                                :href="route('customers.show', invoice.customer.id)"
                                                class="ml-2 inline-flex items-center px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition-colors duration-200"
                                            >
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                View
                                            </Link>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-300">{{ invoice.repair_order.order_number }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-300">{{ formatDate(invoice.issue_date) }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-white">{{ formatCurrency(invoice.total_amount) }}</div>
                                        <div v-if="invoice.amount_paid > 0" class="text-xs text-green-400">
                                            Paid: {{ formatCurrency(invoice.amount_paid) }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full border" :class="getStatusColor(invoice.status)">
                                            {{ invoice.status.replace('_', ' ').charAt(0).toUpperCase() + invoice.status.replace('_', ' ').slice(1) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <Link
                                                :href="route('invoices.show', invoice.id)"
                                                class="text-blue-400 hover:text-blue-300 transition-colors duration-200"
                                                title="View"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </Link>

                                            <button
                                                @click="deleteInvoice(invoice)"
                                                class="text-red-400 hover:text-red-300 transition-colors duration-200"
                                                title="Delete"
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
                    <div v-if="invoices.links" class="bg-gray-900 px-4 py-3 border-t border-gray-700 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link
                                    v-if="invoices.prev_page_url"
                                    :href="invoices.prev_page_url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-800 hover:bg-gray-700"
                                >
                                    Previous
                                </Link>
                                <Link
                                    v-if="invoices.next_page_url"
                                    :href="invoices.next_page_url"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-800 hover:bg-gray-700"
                                >
                                    Next
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-400">
                                        Showing {{ invoices.from }} to {{ invoices.to }} of {{ invoices.total }} results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <template v-for="(link, index) in (invoices.links || [])" :key="index">
                                            <Link
                                                v-if="link && link.url"
                                                :href="link.url"
                                                v-html="link.label"
                                                class="relative inline-flex items-center px-2 py-2 border text-sm font-medium transition-colors duration-200"
                                                :class="[
                                                    link.active
                                                        ? 'z-10 bg-red-600 border-red-600 text-white'
                                                        : 'bg-gray-800 border-gray-600 text-gray-300 hover:bg-gray-700',
                                                    index === 0 ? 'rounded-l-md' : '',
                                                    index === (invoices.links || []).length - 1 ? 'rounded-r-md' : ''
                                                ]"
                                            />
                                            <span
                                                v-else-if="link"
                                                v-html="link.label"
                                                class="relative inline-flex items-center px-2 py-2 border border-gray-600 bg-gray-800 text-gray-500 text-sm font-medium cursor-not-allowed"
                                                :class="[
                                                    index === 0 ? 'rounded-l-md' : '',
                                                    index === invoices.links.length - 1 ? 'rounded-r-md' : ''
                                                ]"
                                            />
                                        </template>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
