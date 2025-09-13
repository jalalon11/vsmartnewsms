<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\RepairOrder;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['repairOrder.device', 'customer', 'createdBy']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('repairOrder', function ($q) use ($search) {
                      $q->where('order_number', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'partially_paid') {
                $query->partiallyPaid();
            } else {
                $query->where('status', $request->status);
            }
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $invoices = $query->paginate(15)->withQueryString();

        // Get summary statistics
        $stats = [
            'total' => Invoice::count(),
            'pending' => Invoice::pending()->count(),
            'paid' => Invoice::paid()->count(),
            'partially_paid' => Invoice::partiallyPaid()->count(),
            'total_amount' => Invoice::sum('total_amount'),
            'pending_amount' => Invoice::pending()->sum('total_amount'),
        ];

        return Inertia::render('Invoices/Index', [
            'invoices' => $invoices,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status', 'date_from', 'date_to', 'sort', 'direction'])
        ]);
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create(Request $request)
    {
        $repairOrderId = $request->get('repair_order_id');
        $repairOrder = null;

        if ($repairOrderId) {
            $repairOrder = RepairOrder::with(['customer', 'device', 'service', 'services', 'parts'])->find($repairOrderId);
        }

        $customers = Customer::orderBy('first_name')->get();
        $repairOrders = RepairOrder::with(['customer', 'device', 'service', 'services'])
                                  ->whereIn('status', ['completed', 'delivered'])
                                  ->whereDoesntHave('invoice')
                                  ->orderBy('created_at', 'desc')
                                  ->get();

        return Inertia::render('Invoices/Create', [
            'customers' => $customers,
            'repairOrders' => $repairOrders,
            'preselectedRepairOrder' => $repairOrder,
        ]);
    }

    /**
     * Store a newly created invoice.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'repair_order_id' => 'required|exists:repair_orders,id',
            'customer_id' => 'required|exists:customers,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'subtotal' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Calculate totals
        $discountAmount = $validated['discount_amount'] ?? 0;
        $totalAmount = $validated['subtotal'] - $discountAmount;

        // Generate invoice number
        $invoice = new Invoice();
        $invoiceNumber = $invoice->generateInvoiceNumber();

        $invoice = Invoice::create([
            ...$validated,
            'invoice_number' => $invoiceNumber,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'created_by' => 1, // Default to admin user for now
        ]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice created successfully.');
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load([
            'repairOrder.device.deviceType',
            'repairOrder.service',
            'repairOrder.services',
            'repairOrder.parts' => function($query) {
                $query->withPivot('quantity_used', 'unit_price', 'total_price');
            },
            'customer',
            'createdBy'
        ]);

        return Inertia::render('Invoices/Show', [
            'invoice' => $invoice
        ]);
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        $invoice->load(['repairOrder.customer', 'customer']);

        $customers = Customer::orderBy('first_name')->get();
        $repairOrders = RepairOrder::with(['customer', 'device', 'service', 'services'])
                                  ->whereIn('status', ['completed', 'delivered'])
                                  ->where(function($query) use ($invoice) {
                                      $query->whereDoesntHave('invoice')
                                            ->orWhere('id', $invoice->repair_order_id);
                                  })
                                  ->orderBy('created_at', 'desc')
                                  ->get();

        return Inertia::render('Invoices/Edit', [
            'invoice' => $invoice,
            'customers' => $customers,
            'repairOrders' => $repairOrders,
        ]);
    }

    /**
     * Update the specified invoice.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'repair_order_id' => 'required|exists:repair_orders,id',
            'customer_id' => 'required|exists:customers,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'subtotal' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Calculate totals
        $discountAmount = $validated['discount_amount'] ?? 0;
        $totalAmount = $validated['subtotal'] - $discountAmount;

        $invoice->update([
            ...$validated,
            'total_amount' => $totalAmount,
        ]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    /**
     * Remove the specified invoice.
     */
    public function destroy(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->withErrors(['error' => 'Cannot delete a paid invoice.']);
        }

        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    /**
     * Mark invoice as paid or add partial payment.
     */
    public function markAsPaid(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,card,bank_transfer,check',
            'paid_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
            'reference_number' => 'nullable|string|max:100',
        ]);

        // Check if amount exceeds remaining balance
        $remainingBalance = $invoice->total_amount - $invoice->amount_paid;
        if ($validated['amount'] > $remainingBalance) {
            return back()->withErrors(['amount' => 'Payment amount cannot exceed remaining balance of ₱' . number_format($remainingBalance, 2)]);
        }

        // Add payment
        $payment = $invoice->addPayment(
            $validated['amount'],
            $validated['payment_method'],
            $validated['paid_date'],
            $validated['notes'],
            $validated['reference_number']
        );

        $message = $invoice->isFullyPaid()
            ? 'Invoice fully paid successfully.'
            : 'Partial payment of ₱' . number_format($validated['amount'], 2) . ' recorded successfully.';

        return back()->with('success', $message);
    }

    /**
     * Bulk mark invoices as paid.
     */
    public function bulkMarkAsPaid(Request $request)
    {
        $validated = $request->validate([
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => 'exists:invoices,id',
        ]);

        $invoices = Invoice::whereIn('id', $validated['invoice_ids'])
            ->where('status', 'pending')
            ->get();

        foreach ($invoices as $invoice) {
            $invoice->markAsPaid('cash', now());
        }

        return back()->with('success', count($invoices) . ' invoices marked as paid successfully.');
    }

    /**
     * Bulk delete invoices.
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => 'exists:invoices,id',
        ]);

        $deletedCount = Invoice::whereIn('id', $validated['invoice_ids'])
            ->where('status', '!=', 'paid')
            ->delete();

        return back()->with('success', $deletedCount . ' invoices deleted successfully.');
    }

    /**
     * Generate invoice from repair order.
     */
    public function generateFromRepairOrder(RepairOrder $repairOrder)
    {
        try {
            // Check if invoice already exists
            if ($repairOrder->invoice) {
                return redirect()->route('invoices.show', $repairOrder->invoice)
                    ->with('info', 'Invoice already exists for this repair order.');
            }

            // Validate repair order status
            if (!in_array($repairOrder->status, ['completed', 'delivered'])) {
                return back()->withErrors(['error' => 'Can only generate invoices for completed or delivered repair orders.']);
            }

            // Calculate subtotal from repair order
            $subtotal = ($repairOrder->labor_cost ?? 0) + ($repairOrder->parts_cost ?? 0);

            if ($subtotal <= 0) {
                return back()->withErrors(['error' => 'Cannot generate invoice with zero amount. Please ensure labor cost and parts cost are set.']);
            }

            // Generate invoice number
            $invoice = new Invoice();
            $invoiceNumber = $invoice->generateInvoiceNumber();

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'repair_order_id' => $repairOrder->id,
                'customer_id' => $repairOrder->customer_id,
                'issue_date' => now(),
                'due_date' => now()->addDays(30),
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'total_amount' => $subtotal,
                'status' => 'pending',
                'created_by' => 1, // Default to admin user for now
            ]);

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice generated successfully from repair order.');

        } catch (\Exception $e) {
            Log::error('Invoice generation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to generate invoice. Please try again.']);
        }
    }
}
