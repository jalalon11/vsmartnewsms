<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\Device;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Part::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('part_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('supplier', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Stock status filter
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low_stock':
                    $query->whereRaw('quantity_in_stock <= minimum_stock_level');
                    break;
                case 'out_of_stock':
                    $query->where('quantity_in_stock', 0);
                    break;
                case 'in_stock':
                    $query->where('quantity_in_stock', '>', 0);
                    break;
            }
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        // Sorting
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        if (in_array($sortField, ['name', 'part_number', 'category', 'quantity_in_stock', 'selling_price', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        }

        $parts = $query->with('device.customer')->paginate(15)->withQueryString();

        // Get unique categories for filter
        $categories = Part::distinct()->pluck('category')->filter()->sort()->values();

        // Get devices for the part creation modal
        $devices = Device::with('customer')->get()->map(function ($device) {
            return [
                'id' => $device->id,
                'label' => $device->customer->first_name . ' ' . $device->customer->last_name . ' - ' . $device->brand . ' ' . $device->model,
                'customer_name' => $device->customer->first_name . ' ' . $device->customer->last_name,
                'device_info' => $device->brand . ' ' . $device->model,
                'serial_number' => $device->serial_number,
            ];
        });

        return Inertia::render('Parts/Index', [
            'parts' => $parts,
            'categories' => $categories,
            'devices' => $devices,
            'filters' => $request->only(['search', 'category', 'stock_status', 'status', 'sort', 'direction'])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'device_id' => 'nullable|exists:devices,id',
            'compatible_devices' => 'nullable|array',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'quantity_in_stock' => 'required|integer|min:0',
            'minimum_stock_level' => 'required|integer|min:0',
            'supplier' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'status' => 'nullable|in:ordered,in_transit,in_stock',
            'order_date' => 'nullable|date',
            'expected_arrival_date' => 'nullable|date',
            'received_date' => 'nullable|date',
        ]);

        // Auto-generate part number
        $validated['part_number'] = 'PN-' . date('Y') . '-' . str_pad(Part::count() + 1, 6, '0', STR_PAD_LEFT);

        // Set default status to ordered
        if (!isset($validated['status'])) {
            $validated['status'] = 'ordered';
        }

        $part = Part::create($validated);

        // If it's an AJAX request (from modal), return JSON response
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Part created successfully.',
                'part' => $part
            ]);
        }

        return redirect()->route('parts.index')
            ->with('success', 'Part created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Part $part)
    {
        return Inertia::render('Parts/Show', [
            'part' => $part
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Part $part)
    {
        $validated = $request->validate([
            'part_number' => 'required|string|unique:parts,part_number,' . $part->id . '|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'device_id' => 'nullable|exists:devices,id',
            'compatible_devices' => 'nullable|array',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'quantity_in_stock' => 'required|integer|min:0',
            'minimum_stock_level' => 'required|integer|min:0',
            'supplier' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'status' => 'nullable|in:ordered,in_transit,in_stock',
            'order_date' => 'nullable|date',
            'expected_arrival_date' => 'nullable|date',
            'received_date' => 'nullable|date',
            'status_notes' => 'nullable|string',
        ]);

        $part->update($validated);

        // If it's an AJAX request (from modal), return JSON response
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Part updated successfully.',
                'part' => $part
            ]);
        }

        return redirect()->route('parts.index')
            ->with('success', 'Part updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Part $part)
    {
        // Check if part is used in any repair orders
        if ($part->repairOrders()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete part that has been used in repair orders.']);
        }

        $part->delete();

        return redirect()->route('parts.index')
            ->with('success', 'Part deleted successfully.');
    }

    /**
     * Update stock quantity
     */
    public function updateStock(Request $request, Part $part)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:add,subtract,set',
            'notes' => 'nullable|string|max:255',
        ]);

        $oldQuantity = $part->quantity_in_stock;

        switch ($validated['type']) {
            case 'add':
                $part->quantity_in_stock += $validated['quantity'];
                break;
            case 'subtract':
                $part->quantity_in_stock = max(0, $part->quantity_in_stock - $validated['quantity']);
                break;
            case 'set':
                $part->quantity_in_stock = $validated['quantity'];
                break;
        }

        $part->save();

        return back()->with('success', "Stock updated from {$oldQuantity} to {$part->quantity_in_stock}.");
    }

    /**
     * Update part status
     */
    public function updateStatus(Request $request, Part $part)
    {
        $validated = $request->validate([
            'status' => 'required|in:ordered,in_transit,in_stock',
            'order_date' => 'nullable|date',
            'expected_arrival_date' => 'nullable|date',
            'received_date' => 'nullable|date',
            'status_notes' => 'nullable|string|max:500',
        ]);

        $oldStatus = $part->status;

        if (!$part->canTransitionTo($validated['status'])) {
            return back()->withErrors(['status' => "Cannot transition from {$oldStatus} to {$validated['status']}"]);
        }

        $success = $part->updateStatus($validated['status'], $validated);

        if ($success) {
            return back()->with('success', "Part status updated from {$oldStatus} to {$validated['status']}.");
        } else {
            return back()->withErrors(['status' => 'Failed to update part status.']);
        }
    }
}
