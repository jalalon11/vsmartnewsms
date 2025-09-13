<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    invoice: Object,
});

// Payment modal state
const showPaymentModal = ref(false);
const paymentForm = useForm({
    payment_method: 'cash',
    paid_date: new Date().toISOString().split('T')[0],
    amount: props.invoice.total_amount - props.invoice.amount_paid,
    notes: '',
    reference_number: ''
});

// Dynamic company information - easily configurable
const companyInfo = {
    name: 'VSMART TUNE UP',
    address: 'P-10 Poblacion Manticao, Misamis Oriental, Philippines',
    phone: '+63 912 345 6789',
    email: 'info@hardwarerepair.com',
    tin: '123-456-789-000',
    businessPermit: 'BP-2024-001'
};

const formatCurrency = (amount) => {
    return '₱' + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2 });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const getStatusColor = (status) => {
    const colors = {
        'pending': 'text-yellow-400 bg-yellow-900/50 border-yellow-700',
        'paid': 'text-green-400 bg-green-900/50 border-green-700',
        'overdue': 'text-red-400 bg-red-900/50 border-red-700',
        'cancelled': 'text-gray-400 bg-gray-900/50 border-gray-700',
    };
    return colors[status] || 'text-gray-400 bg-gray-900/50 border-gray-700';
};

// Payment functions
const openPaymentModal = () => {
    // Reset amount to remaining balance
    paymentForm.amount = props.invoice.total_amount - props.invoice.amount_paid;
    showPaymentModal.value = true;
};

const closePaymentModal = () => {
    showPaymentModal.value = false;
    paymentForm.reset();
};

const markAsPaid = () => {
    paymentForm.post(route('invoices.mark-as-paid', props.invoice.id), {
        onSuccess: () => {
            closePaymentModal();
            router.reload({ only: ['invoice'] });
        },
        onError: (errors) => {
            console.error('Payment error:', errors);
        }
    });
};

const printReceipt = () => {
    // Create a new window for printing
    const printWindow = window.open('', '_blank');
    const receiptContent = generateReceiptHTML();

    printWindow.document.write(receiptContent);
    printWindow.document.close();

    // Wait for content to load then print
    printWindow.onload = () => {
        printWindow.print();
        printWindow.close();
    };
};

const generateReceiptHTML = () => {
    return `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Official Receipt - ${props.invoice.invoice_number}</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: 'Arial', sans-serif;
                    font-size: 12px;
                    line-height: 1.4;
                    color: #000;
                    max-width: 800px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 20px; }
                .company-name { font-size: 24px; font-weight: bold; margin-bottom: 10px; }
                .company-details { font-size: 11px; margin-bottom: 5px; }
                .receipt-title { font-size: 18px; font-weight: bold; margin-top: 15px; }
                .invoice-info { display: flex; justify-content: space-between; margin: 20px 0; }
                .customer-info { margin-bottom: 20px; }
                .section-title { font-weight: bold; font-size: 14px; margin-bottom: 10px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
                .breakdown-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                .breakdown-table th, .breakdown-table td {
                    border: 1px solid #000;
                    padding: 8px;
                    text-align: left;
                }
                .breakdown-table th { background-color: #f0f0f0; font-weight: bold; }
                .breakdown-table .amount { text-align: right; }
                .totals-section { margin-top: 20px; }
                .total-line { display: flex; justify-content: space-between; margin: 5px 0; }
                .final-total { font-weight: bold; font-size: 16px; border-top: 2px solid #000; padding-top: 10px; margin-top: 10px; }
                .footer { margin-top: 40px; text-align: center; font-size: 10px; }
                .signature-section { margin-top: 40px; display: flex; justify-content: space-between; }
                .signature-box { text-align: center; width: 200px; }
                .signature-line { border-bottom: 1px solid #000; margin-bottom: 5px; height: 40px; }
                @media print {
                    body { margin: 0; padding: 15px; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="company-name">${companyInfo.name}</div>
                <div class="company-details">${companyInfo.address}</div>
                <div class="company-details">Phone: ${companyInfo.phone} | Email: ${companyInfo.email}</div>
                <div class="company-details">TIN: ${companyInfo.tin} | Business Permit: ${companyInfo.businessPermit}</div>
                <div class="receipt-title">OFFICIAL RECEIPT</div>
            </div>

            <div class="invoice-info">
                <div>
                    <strong>Receipt No:</strong> ${props.invoice.invoice_number}<br>
                    <strong>Date Issued:</strong> ${formatDate(props.invoice.issue_date)}<br>
                    <strong>Due Date:</strong> ${formatDate(props.invoice.due_date)}
                </div>
                <div>
                    <strong>Repair Order:</strong> ${props.invoice.repair_order?.order_number || 'N/A'}<br>
                    <strong>Status:</strong> ${props.invoice.status.toUpperCase()}
                </div>
            </div>

            <div class="customer-info">
                <div class="section-title">CUSTOMER INFORMATION</div>
                <strong>Name:</strong> ${props.invoice.customer.first_name} ${props.invoice.customer.last_name}<br>
                <strong>Phone:</strong> ${props.invoice.customer.phone_number}<br>
                ${props.invoice.customer.facebook_link ? `<strong>Facebook:</strong> ${props.invoice.customer.facebook_link}<br>` : ''}
            </div>

            <div class="service-info">
                <div class="section-title">SERVICE DETAILS</div>
                <strong>Device:</strong> ${props.invoice.repair_order?.device?.brand || ''} ${props.invoice.repair_order?.device?.model || ''}<br>
                <strong>Services:</strong> ${props.invoice.repair_order?.services?.length > 0 ? props.invoice.repair_order.services.map(s => s.name).join(', ') : (props.invoice.repair_order?.service?.name || 'N/A')}<br>
                ${props.invoice.repair_order?.diagnosis ? `<strong>Diagnosis:</strong> ${props.invoice.repair_order.diagnosis}<br>` : ''}
                ${props.invoice.repair_order?.solution ? `<strong>Solution:</strong> ${props.invoice.repair_order.solution}<br>` : ''}
            </div>

            <table class="breakdown-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th class="amount">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    ${props.invoice.repair_order?.services?.length > 0 ?
                        props.invoice.repair_order.services.map(service => `
                            <tr>
                                <td>Labor Cost - ${service.name}</td>
                                <td>1</td>
                                <td class="amount">${formatCurrency(service.pivot?.service_price || service.base_price || 0)}</td>
                                <td class="amount">${formatCurrency(service.pivot?.service_price || service.base_price || 0)}</td>
                            </tr>
                        `).join('') : `
                            <tr>
                                <td>Labor Cost - ${props.invoice.repair_order?.service?.name || 'Service'}</td>
                                <td>1</td>
                                <td class="amount">${formatCurrency(props.invoice.repair_order?.labor_cost || 0)}</td>
                                <td class="amount">${formatCurrency(props.invoice.repair_order?.labor_cost || 0)}</td>
                            </tr>
                        `
                    }
                    ${props.invoice.repair_order?.parts?.map(part => `
                        <tr>
                            <td>${part.name} (${part.part_number})</td>
                            <td>${part.pivot.quantity_used}</td>
                            <td class="amount">${formatCurrency(part.pivot.unit_price)}</td>
                            <td class="amount">${formatCurrency(part.pivot.total_price)}</td>
                        </tr>
                    `).join('') || ''}
                </tbody>
            </table>

            <div class="totals-section">
                <div class="total-line">
                    <span>Subtotal:</span>
                    <span>${formatCurrency(props.invoice.subtotal)}</span>
                </div>
                ${props.invoice.tax_rate > 0 ? `
                    <div class="total-line">
                        <span>Tax (${props.invoice.tax_rate}%):</span>
                        <span>${formatCurrency(props.invoice.tax_amount)}</span>
                    </div>
                ` : ''}
                ${props.invoice.discount_amount > 0 ? `
                    <div class="total-line">
                        <span>Discount:</span>
                        <span>-${formatCurrency(props.invoice.discount_amount)}</span>
                    </div>
                ` : ''}
                <div class="total-line final-total">
                    <span>TOTAL AMOUNT:</span>
                    <span>${formatCurrency(props.invoice.total_amount)}</span>
                </div>
            </div>

            

            ${props.invoice.notes ? `
                <div style="margin-top: 20px;">
                    <div class="section-title">NOTES</div>
                    ${props.invoice.notes}
                </div>
            ` : ''}

            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div>Customer Signature</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div>Authorized Signature</div>
                </div>
            </div>

            <div class="footer">
                <p>Thank you for choosing ${companyInfo.name}!</p>
                <p>This is a computer-generated receipt.</p>
                <p>Generated on: ${new Date().toLocaleString()}</p>
            </div>
        </body>
        </html>
    `;
};
</script>

<template>
    <Head :title="`Invoice ${invoice.invoice_number}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-white">
                        Invoice {{ invoice.invoice_number }}
                    </h2>
                    <p class="text-sm text-gray-400 mt-1">
                        Created {{ formatDate(invoice.created_at) }}
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <button
                        @click="printReceipt"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Print Receipt</span>
                    </button>
                    <Link
                        :href="route('invoices.index')"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Back to Invoices</span>
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-gray-800 rounded-xl shadow-2xl border border-gray-700 overflow-hidden">
                    
                    <!-- Invoice Header -->
                    <div class="bg-gray-900 px-6 py-4 border-b border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-white">Invoice Details</h3>
                            </div>
                            <div>
                                <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full border" :class="getStatusColor(invoice.status)">
                                    {{ invoice.status.charAt(0).toUpperCase() + invoice.status.slice(1) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Content -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            
                            <!-- Customer Information -->
                            <div class="space-y-4">
                                <h4 class="text-lg font-medium text-white border-b border-gray-700 pb-2">Customer Information</h4>
                                <div class="space-y-2">
                                    <div>
                                        <span class="text-sm text-gray-400">Name:</span>
                                        <span class="text-white ml-2">{{ invoice.customer.first_name }} {{ invoice.customer.last_name }}</span>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-400">Phone:</span>
                                        <span class="text-white ml-2">{{ invoice.customer.phone_number }}</span>
                                    </div>
                                    <div v-if="invoice.customer.facebook_link">
                                        <span class="text-sm text-gray-400">Facebook:</span>
                                        <span class="text-white ml-2">{{ invoice.customer.facebook_link }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Invoice Information -->
                            <div class="space-y-4">
                                <h4 class="text-lg font-medium text-white border-b border-gray-700 pb-2">Invoice Information</h4>
                                <div class="space-y-2">
                                    <div>
                                        <span class="text-sm text-gray-400">Issue Date:</span>
                                        <span class="text-white ml-2">{{ formatDate(invoice.issue_date) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-400">Due Date:</span>
                                        <span class="text-white ml-2">{{ formatDate(invoice.due_date) }}</span>
                                    </div>
                                    <div v-if="invoice.repair_order">
                                        <span class="text-sm text-gray-400">Repair Order:</span>
                                        <Link :href="route('repair-orders.show', invoice.repair_order.id)" class="text-blue-400 hover:text-blue-300 ml-2">
                                            {{ invoice.repair_order.order_number }}
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Service Details -->
                        <div v-if="invoice.repair_order" class="mb-8">
                            <h4 class="text-lg font-medium text-white border-b border-gray-700 pb-2 mb-4">Service Details</h4>
                            <div class="bg-gray-900 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-sm text-gray-400">Device:</span>
                                        <span class="text-white ml-2">{{ invoice.repair_order.device?.brand }} {{ invoice.repair_order.device?.model }}</span>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-400">Services:</span>
                                        <div v-if="invoice.repair_order.services && invoice.repair_order.services.length > 0" class="ml-2">
                                            <span v-for="(service, index) in invoice.repair_order.services" :key="service.id" class="text-white">
                                                {{ service.name }}<span v-if="index < invoice.repair_order.services.length - 1" class="text-gray-500">, </span>
                                            </span>
                                        </div>
                                        <span v-else-if="invoice.repair_order.service" class="text-white ml-2">{{ invoice.repair_order.service.name }}</span>
                                        <span v-else class="text-gray-500 ml-2">No services</span>
                                    </div>
                                </div>
                                <div v-if="invoice.repair_order.diagnosis" class="mt-3">
                                    <span class="text-sm text-gray-400">Diagnosis:</span>
                                    <p class="text-white mt-1">{{ invoice.repair_order.diagnosis }}</p>
                                </div>
                                <div v-if="invoice.repair_order.solution" class="mt-3">
                                    <span class="text-sm text-gray-400">Solution:</span>
                                    <p class="text-white mt-1">{{ invoice.repair_order.solution }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Cost Breakdown -->
                        <div class="mb-8">
                            <h4 class="text-lg font-medium text-white border-b border-gray-700 pb-2 mb-4">Cost Breakdown</h4>
                            <div class="bg-gray-900 rounded-lg p-6">

                                <!-- Labor Cost -->
                                <div class="mb-6">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-white font-medium">Labor Cost</span>
                                        <span class="text-white">{{ formatCurrency(invoice.repair_order?.labor_cost || 0) }}</span>
                                    </div>
                                    <div class="text-sm text-gray-400 ml-4">
                                        Service: {{ invoice.repair_order?.service?.name }}
                                    </div>
                                </div>

                                <!-- Parts Cost -->
                                <div v-if="invoice.repair_order?.parts && invoice.repair_order.parts.length > 0" class="mb-6">
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="text-white font-medium">Parts Cost</span>
                                        <span class="text-white">{{ formatCurrency(invoice.repair_order?.parts_cost || 0) }}</span>
                                    </div>
                                    <div class="ml-4 space-y-2">
                                        <div v-for="part in invoice.repair_order.parts" :key="part.id" class="flex justify-between items-center text-sm">
                                            <div class="text-gray-300">
                                                <span class="font-medium">{{ part.name }}</span>
                                                <span class="text-gray-400 ml-2">({{ part.part_number }})</span>
                                                <span class="text-gray-400 ml-2">× {{ part.pivot.quantity_used }}</span>
                                            </div>
                                            <span class="text-gray-300">{{ formatCurrency(part.pivot.total_price) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Subtotal -->
                                <div class="border-t border-gray-700 pt-4 mb-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-white font-medium">Subtotal</span>
                                        <span class="text-white font-medium">{{ formatCurrency(invoice.subtotal) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Summary -->
                        <div class="bg-gray-900 rounded-lg p-6">
                            <h4 class="text-lg font-medium text-white border-b border-gray-700 pb-2 mb-4">Financial Summary</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Subtotal:</span>
                                    <span class="text-white">{{ formatCurrency(invoice.subtotal) }}</span>
                                </div>
                                <div v-if="invoice.tax_rate > 0" class="flex justify-between">
                                    <span class="text-gray-400">Tax ({{ invoice.tax_rate }}%):</span>
                                    <span class="text-white">{{ formatCurrency(invoice.tax_amount) }}</span>
                                </div>
                                <div v-if="invoice.discount_amount > 0" class="flex justify-between">
                                    <span class="text-gray-400">Discount:</span>
                                    <span class="text-red-400">-{{ formatCurrency(invoice.discount_amount) }}</span>
                                </div>
                                <div class="border-t border-gray-700 pt-3">
                                    <div class="flex justify-between text-lg font-semibold">
                                        <span class="text-white">Total Amount:</span>
                                        <span class="text-green-400">{{ formatCurrency(invoice.total_amount) }}</span>
                                    </div>

                                    <!-- Payment Status -->
                                    <div v-if="invoice.amount_paid > 0" class="mt-4 pt-4 border-t border-gray-700">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-gray-300">Amount Paid:</span>
                                            <span class="text-green-400">{{ formatCurrency(invoice.amount_paid) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-300">Balance Due:</span>
                                            <span class="text-yellow-400">{{ formatCurrency(invoice.total_amount - invoice.amount_paid) }}</span>
                                        </div>

                                        <!-- Payment Progress Bar -->
                                        <div class="mt-3">
                                            <div class="flex justify-between text-sm text-gray-400 mb-1">
                                                <span>Payment Progress</span>
                                                <span>{{ Math.round((invoice.amount_paid / invoice.total_amount) * 100) }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-700 rounded-full h-2">
                                                <div class="bg-green-500 h-2 rounded-full transition-all duration-300"
                                                     :style="{ width: (invoice.amount_paid / invoice.total_amount) * 100 + '%' }"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div v-if="invoice.status === 'paid'" class="mt-6 bg-green-900/20 border border-green-700 rounded-lg p-4">
                            <h4 class="text-lg font-medium text-green-400 mb-3">Payment Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm text-gray-400">Payment Method:</span>
                                    <span class="text-white ml-2 capitalize">{{ invoice.payment_method?.replace('_', ' ') }}</span>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-400">Payment Date:</span>
                                    <span class="text-white ml-2">{{ formatDate(invoice.paid_date) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div v-if="invoice.notes" class="mt-6">
                            <h4 class="text-lg font-medium text-white border-b border-gray-700 pb-2 mb-3">Notes</h4>
                            <p class="text-gray-300 bg-gray-900 rounded-lg p-4">{{ invoice.notes }}</p>
                        </div>

                        <!-- Actions -->
                        <div v-if="invoice.status === 'pending' || invoice.status === 'partially_paid'" class="mt-8 flex justify-end">
                            <button
                                @click="openPaymentModal"
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <span>{{ invoice.status === 'partially_paid' ? 'Add Payment' : 'Mark as Paid' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <div v-if="showPaymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md mx-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Add Payment</h3>
                    <button @click="closePaymentModal" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="markAsPaid" class="space-y-4">
                    <!-- Payment Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Payment Amount</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-400">₱</span>
                            <input type="number"
                                   v-model="paymentForm.amount"
                                   step="0.01"
                                   min="0.01"
                                   :max="invoice.total_amount - invoice.amount_paid"
                                   class="w-full bg-gray-700 border border-gray-600 rounded-lg pl-8 pr-3 py-2 text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                            Remaining balance: ₱{{ (invoice.total_amount - invoice.amount_paid).toFixed(2) }}
                        </div>
                        <div v-if="paymentForm.errors.amount" class="text-red-400 text-sm mt-1">
                            {{ paymentForm.errors.amount }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Payment Method</label>
                        <select v-model="paymentForm.payment_method"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="cash">Cash</option>
                            <option value="card">Gcash/Maya</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="check">Check</option>
                        </select>
                        <div v-if="paymentForm.errors.payment_method" class="text-red-400 text-sm mt-1">
                            {{ paymentForm.errors.payment_method }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Payment Date</label>
                        <input type="date"
                               v-model="paymentForm.paid_date"
                               class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <div v-if="paymentForm.errors.paid_date" class="text-red-400 text-sm mt-1">
                            {{ paymentForm.errors.paid_date }}
                        </div>
                    </div>

                    <!-- Reference Number (optional) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Reference Number (Optional)</label>
                        <input type="text"
                               v-model="paymentForm.reference_number"
                               placeholder="Check number, transaction ID, etc."
                               class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <div v-if="paymentForm.errors.reference_number" class="text-red-400 text-sm mt-1">
                            {{ paymentForm.errors.reference_number }}
                        </div>
                    </div>

                    <!-- Notes (optional) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Notes (Optional)</label>
                        <textarea v-model="paymentForm.notes"
                                  rows="2"
                                  placeholder="Additional payment notes..."
                                  class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"></textarea>
                        <div v-if="paymentForm.errors.notes" class="text-red-400 text-sm mt-1">
                            {{ paymentForm.errors.notes }}
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button"
                                @click="closePaymentModal"
                                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                                :disabled="paymentForm.processing"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 disabled:opacity-50">
                            <span v-if="paymentForm.processing">Processing...</span>
                            <span v-else>Add Payment</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
