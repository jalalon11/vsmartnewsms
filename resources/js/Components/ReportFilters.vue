<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    filters: {
        type: Object,
        default: () => ({})
    },
    reportType: {
        type: String,
        required: true
    },
    customers: {
        type: Array,
        default: () => []
    },
    technicians: {
        type: Array,
        default: () => []
    },
    deviceTypes: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['filtersChanged']);

// Filter state with proper null guards
const localFilters = ref({
    period: props.filters?.period || 'monthly',
    start_date: props.filters?.start_date || '',
    end_date: props.filters?.end_date || '',
    customer_id: props.filters?.customer_id || '',
    technician_id: props.filters?.technician_id || '',
    device_type_id: props.filters?.device_type_id || '',
    status: props.filters?.status || '',
    priority: props.filters?.priority || '',
    year: props.filters?.year || new Date().getFullYear(),
    month: props.filters?.month || new Date().getMonth() + 1,
    quarter: props.filters?.quarter || Math.ceil((new Date().getMonth() + 1) / 3)
});

const showAdvancedFilters = ref(false);

// Computed properties
const periodOptions = [
    { value: 'daily', label: 'Daily' },
    { value: 'weekly', label: 'Weekly' },
    { value: 'monthly', label: 'Monthly' },
    { value: 'quarterly', label: 'Quarterly' },
    { value: 'yearly', label: 'Yearly' },
    { value: 'custom', label: 'Custom Range' }
];

const statusOptions = [
    { value: '', label: 'All Statuses' },
    { value: 'pending', label: 'Pending' },
    { value: 'in_progress', label: 'In Progress' },
    { value: 'waiting_parts', label: 'Waiting Parts' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' },
    { value: 'delivered', label: 'Delivered' }
];

const priorityOptions = [
    { value: '', label: 'All Priorities' },
    { value: 'low', label: 'Low' },
    { value: 'medium', label: 'Medium' },
    { value: 'high', label: 'High' },
    { value: 'urgent', label: 'Urgent' }
];

const yearOptions = computed(() => {
    const currentYear = new Date().getFullYear();
    const years = [];
    for (let i = currentYear - 5; i <= currentYear + 1; i++) {
        years.push({ value: i, label: i.toString() });
    }
    return years;
});

const monthOptions = [
    { value: 1, label: 'January' },
    { value: 2, label: 'February' },
    { value: 3, label: 'March' },
    { value: 4, label: 'April' },
    { value: 5, label: 'May' },
    { value: 6, label: 'June' },
    { value: 7, label: 'July' },
    { value: 8, label: 'August' },
    { value: 9, label: 'September' },
    { value: 10, label: 'October' },
    { value: 11, label: 'November' },
    { value: 12, label: 'December' }
];

const quarterOptions = [
    { value: 1, label: 'Q1 (Jan-Mar)' },
    { value: 2, label: 'Q2 (Apr-Jun)' },
    { value: 3, label: 'Q3 (Jul-Sep)' },
    { value: 4, label: 'Q4 (Oct-Dec)' }
];

const showCustomDateRange = computed(() => localFilters.value.period === 'custom');

// Methods
const applyFilters = () => {
    if (!localFilters.value) return;

    const cleanFilters = Object.fromEntries(
        Object.entries(localFilters.value).filter(([key, value]) => value !== '' && value !== null && value !== undefined)
    );

    emit('filtersChanged', cleanFilters);

    // Navigate with new filters - guard against invalid reportType
    const validReportTypes = ['revenue', 'orders', 'customers', 'services', 'index'];
    const reportType = validReportTypes.includes(props.reportType) ? props.reportType : 'index';

    router.get(route(`reports.${reportType}`), cleanFilters, {
        preserveState: true,
        preserveScroll: true
    });
};

const resetFilters = () => {
    localFilters.value = {
        period: 'monthly',
        start_date: '',
        end_date: '',
        customer_id: '',
        technician_id: '',
        device_type_id: '',
        status: '',
        priority: '',
        year: new Date().getFullYear(),
        month: new Date().getMonth() + 1,
        quarter: Math.ceil((new Date().getMonth() + 1) / 3)
    };
    applyFilters();
};

const toggleAdvancedFilters = () => {
    showAdvancedFilters.value = !showAdvancedFilters.value;
};

// Quick date presets
const applyQuickFilter = (preset) => {
    const now = new Date();
    const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
    const startOfYear = new Date(now.getFullYear(), 0, 1);
    
    switch (preset) {
        case 'today':
            localFilters.value.period = 'daily';
            localFilters.value.start_date = now.toISOString().split('T')[0];
            localFilters.value.end_date = now.toISOString().split('T')[0];
            break;
        case 'this_week':
            localFilters.value.period = 'weekly';
            const startOfWeek = new Date(now.setDate(now.getDate() - now.getDay()));
            localFilters.value.start_date = startOfWeek.toISOString().split('T')[0];
            localFilters.value.end_date = new Date().toISOString().split('T')[0];
            break;
        case 'this_month':
            localFilters.value.period = 'monthly';
            localFilters.value.month = now.getMonth() + 1;
            localFilters.value.year = now.getFullYear();
            break;
        case 'this_year':
            localFilters.value.period = 'yearly';
            localFilters.value.year = now.getFullYear();
            break;
        case 'last_30_days':
            localFilters.value.period = 'custom';
            const thirtyDaysAgo = new Date(now.setDate(now.getDate() - 30));
            localFilters.value.start_date = thirtyDaysAgo.toISOString().split('T')[0];
            localFilters.value.end_date = new Date().toISOString().split('T')[0];
            break;
    }
    applyFilters();
};

// Watch for external filter changes
watch(() => props.filters, (newFilters) => {
    Object.assign(localFilters.value, newFilters);
}, { deep: true });
</script>

<template>
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white">Report Filters</h3>
            <button
                @click="toggleAdvancedFilters"
                class="text-sm text-gray-400 hover:text-white transition-colors"
            >
                {{ showAdvancedFilters ? 'Hide' : 'Show' }} Advanced Filters
            </button>
        </div>

        <!-- Quick Filters -->
        <div class="flex flex-wrap gap-2">
            <button
                v-for="(preset, key) in {
                    today: 'Today',
                    this_week: 'This Week',
                    this_month: 'This Month',
                    this_year: 'This Year',
                    last_30_days: 'Last 30 Days'
                }"
                :key="key"
                @click="applyQuickFilter(key)"
                class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white text-sm rounded-md transition-colors"
            >
                {{ preset }}
            </button>
        </div>

        <!-- Basic Filters -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <!-- Period -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Period</label>
                <select
                    v-model="localFilters.period"
                    class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2 text-sm"
                >
                    <option v-for="option in periodOptions" :key="option.value" :value="option.value">
                        {{ option.label }}
                    </option>
                </select>
            </div>

            <!-- Year (for non-custom periods) -->
            <div v-if="!showCustomDateRange">
                <label class="block text-sm font-medium text-gray-300 mb-2">Year</label>
                <select
                    v-model="localFilters.year"
                    class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2 text-sm"
                >
                    <option v-for="option in yearOptions" :key="option.value" :value="option.value">
                        {{ option.label }}
                    </option>
                </select>
            </div>

            <!-- Month (for monthly period) -->
            <div v-if="localFilters.period === 'monthly' && !showCustomDateRange">
                <label class="block text-sm font-medium text-gray-300 mb-2">Month</label>
                <select
                    v-model="localFilters.month"
                    class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2 text-sm"
                >
                    <option v-for="option in monthOptions" :key="option.value" :value="option.value">
                        {{ option.label }}
                    </option>
                </select>
            </div>

            <!-- Quarter (for quarterly period) -->
            <div v-if="localFilters.period === 'quarterly' && !showCustomDateRange">
                <label class="block text-sm font-medium text-gray-300 mb-2">Quarter</label>
                <select
                    v-model="localFilters.quarter"
                    class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2 text-sm"
                >
                    <option v-for="option in quarterOptions" :key="option.value" :value="option.value">
                        {{ option.label }}
                    </option>
                </select>
            </div>
        </div>

        <!-- Custom Date Range -->
        <div v-if="showCustomDateRange" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Start Date</label>
                <input
                    v-model="localFilters.start_date"
                    type="date"
                    class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2 text-sm"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">End Date</label>
                <input
                    v-model="localFilters.end_date"
                    type="date"
                    class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2 text-sm"
                >
            </div>
        </div>

        <!-- Advanced Filters -->
        <Transition
            enter-active-class="transition-all duration-300"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-96"
            leave-active-class="transition-all duration-300"
            leave-from-class="opacity-100 max-h-96"
            leave-to-class="opacity-0 max-h-0"
        >
            <div v-if="showAdvancedFilters" class="space-y-4 border-t border-gray-700 pt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Customer Filter -->
                    <div v-if="customers.length > 0">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Customer</label>
                        <select
                            v-model="localFilters.customer_id"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2 text-sm"
                        >
                            <option value="">All Customers</option>
                            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                {{ customer.first_name }} {{ customer.last_name }}
                            </option>
                        </select>
                    </div>

                    <!-- Technician Filter -->
                    <div v-if="technicians.length > 0">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Technician</label>
                        <select
                            v-model="localFilters.technician_id"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2 text-sm"
                        >
                            <option value="">All Technicians</option>
                            <option v-for="technician in technicians" :key="technician.id" :value="technician.id">
                                {{ technician.user?.name || technician.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Device Type Filter -->
                    <div v-if="deviceTypes.length > 0">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Device Type</label>
                        <select
                            v-model="localFilters.device_type_id"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2 text-sm"
                        >
                            <option value="">All Device Types</option>
                            <option v-for="deviceType in deviceTypes" :key="deviceType.id" :value="deviceType.id">
                                {{ deviceType.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                        <select
                            v-model="localFilters.status"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2 text-sm"
                        >
                            <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Priority Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Priority</label>
                        <select
                            v-model="localFilters.priority"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2 text-sm"
                        >
                            <option v-for="option in priorityOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center pt-4 border-t border-gray-700">
            <button
                @click="resetFilters"
                class="px-4 py-2 bg-gray-600 hover:bg-gray-500 text-white text-sm font-medium rounded-lg transition-colors"
            >
                Reset Filters
            </button>
            <button
                @click="applyFilters"
                class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors"
            >
                Apply Filters
            </button>
        </div>
    </div>
</template>
