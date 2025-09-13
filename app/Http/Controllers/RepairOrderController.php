<?php

namespace App\Http\Controllers;

use App\Models\RepairOrder;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Service;
use App\Models\Technician;
use App\Models\Part;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RepairOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RepairOrder::with(['customer', 'device.deviceType', 'service', 'services', 'technician.user', 'invoice', 'createdBy']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('issue_description', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('first_name', 'like', "%{$search}%")
                                   ->orWhere('last_name', 'like', "%{$search}%")
                                   ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                  })
                  ->orWhereHas('device', function ($deviceQuery) use ($search) {
                      $deviceQuery->where('brand', 'like', "%{$search}%")
                                 ->orWhere('model', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status') && $request->get('status') !== 'all') {
            $query->where('status', $request->get('status'));
        }

        // Priority filter
        if ($request->filled('priority') && $request->get('priority') !== 'all') {
            $query->where('priority', $request->get('priority'));
        }

        // Technician filter
        if ($request->filled('technician') && $request->get('technician') !== 'all') {
            $query->where('technician_id', $request->get('technician'));
        }

        $repairOrders = $query->orderBy('created_at', 'desc')
                             ->paginate(15)
                             ->withQueryString();

        // Append computed attributes to each repair order
        $repairOrders->getCollection()->transform(function ($repairOrder) {
            $repairOrder->append(['assigned_technician_name', 'technician_selection_id']);
            return $repairOrder;
        });

        // Get data for filters and modals
        $customers = Customer::orderBy('first_name')->get();
        $devices = Device::with(['customer', 'deviceType'])->orderBy('brand')->get();
        $services = Service::with('deviceType')->where('is_active', true)->orderBy('name')->get();

        // Get technicians and admin users who can act as technicians
        $technicians = Technician::with('user')->where('is_active', true)->get();
        $adminUsers = \App\Models\User::where('role', 'admin')->where('is_active', true)->get();

        // Create mock technician objects for admin users
        $adminTechnicians = $adminUsers->map(function ($user) {
            return (object) [
                'id' => 'admin-' . $user->id,
                'user_id' => $user->id,
                'employee_id' => 'ADMIN-' . $user->id,
                'specialization' => 'Administrator',
                'is_active' => true,
                'user' => $user,
            ];
        });

        // Combine technicians and admin users
        $allTechnicians = $technicians->concat($adminTechnicians);

        return Inertia::render('RepairOrders/Index', [
            'repairOrders' => $repairOrders,
            'customers' => $customers,
            'devices' => $devices,
            'services' => $services,
            'technicians' => $allTechnicians,
            'parts' => \App\Models\Part::where('is_active', true)->orderBy('name')->get(),
            'filters' => $request->only(['search', 'status', 'priority', 'technician'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::orderBy('first_name')->get();
        $services = Service::with('deviceType')->where('is_active', true)->orderBy('name')->get();

        // Get technicians and admin users who can act as technicians
        $technicians = Technician::with('user')->where('is_active', true)->get();
        $adminUsers = \App\Models\User::where('role', 'admin')->where('is_active', true)->get();

        // Create mock technician objects for admin users
        $adminTechnicians = $adminUsers->map(function ($user) {
            return (object) [
                'id' => 'admin-' . $user->id,
                'user_id' => $user->id,
                'employee_id' => 'ADMIN-' . $user->id,
                'specialization' => 'Administrator',
                'is_active' => true,
                'user' => $user,
            ];
        });

        // Combine technicians and admin users
        $allTechnicians = $technicians->concat($adminTechnicians);

        $parts = Part::where('is_active', true)->where('quantity_in_stock', '>', 0)->orderBy('name')->get();

        return Inertia::render('RepairOrders/Create', [
            'customers' => $customers,
            'services' => $services,
            'technicians' => $allTechnicians,
            'parts' => $parts,
        ]);
    }

    /**
     * Get parts for a specific device (AJAX endpoint).
     */
    public function getPartsForDevice(Device $device)
    {
        // Get parts specifically linked to this device
        $deviceParts = Part::where('device_id', $device->id)
            ->where('is_active', true)
            ->where('quantity_in_stock', '>', 0)
            ->orderBy('name')
            ->get();

        // Get general parts (not linked to any specific device) that are compatible
        $generalParts = Part::whereNull('device_id')
            ->where('is_active', true)
            ->where('quantity_in_stock', '>', 0)
            ->where(function ($query) use ($device) {
                $query->where('compatible_devices', 'like', '%' . $device->deviceType->name . '%')
                      ->orWhere('compatible_devices', 'like', '%' . $device->brand . '%')
                      ->orWhere('compatible_devices', 'like', '%' . $device->model . '%');
            })
            ->orderBy('name')
            ->get();

        return response()->json([
            'device_parts' => $deviceParts,
            'compatible_parts' => $generalParts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'device_id' => 'required|exists:devices,id',
            'services' => 'required|array|min:1',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.service_price' => 'nullable|numeric|min:0',
            'services.*.service_notes' => 'nullable|string',
            'technician_id' => 'nullable|string', // Changed to string to handle admin-{id} format
            'issue_description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'customer_notes' => 'nullable|string',
            'estimated_completion' => 'nullable|date',
            'selected_parts' => 'nullable|array',
            'selected_parts.*.part_id' => 'required|exists:parts,id',
            'selected_parts.*.quantity' => 'required|integer|min:1',
            'selected_parts.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Handle technician_id validation (can be technician ID or admin-{user_id})
        if (!empty($validated['technician_id'])) {
            if (str_starts_with($validated['technician_id'], 'admin-')) {
                // Extract user ID from admin-{user_id} format
                $userId = str_replace('admin-', '', $validated['technician_id']);
                $adminUser = \App\Models\User::where('id', $userId)->where('role', 'admin')->where('is_active', true)->first();
                if (!$adminUser) {
                    return back()->withErrors(['technician_id' => 'Selected admin user not found or inactive.']);
                }
                // Set technician_id to null and assigned_to to the admin user ID
                $validated['technician_id'] = null;
                $validated['assigned_to'] = $userId;
            } else {
                // Regular technician validation
                $technician = Technician::where('id', $validated['technician_id'])->where('is_active', true)->first();
                if (!$technician) {
                    return back()->withErrors(['technician_id' => 'Selected technician not found or inactive.']);
                }
                // Clear assigned_to for regular technicians
                $validated['assigned_to'] = null;
            }
        }

        $validated['order_number'] = 'RO-' . date('Y') . '-' . str_pad(RepairOrder::count() + 1, 6, '0', STR_PAD_LEFT);
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'pending';

        // Set the primary service (first service) for backward compatibility
        $primaryService = $validated['services'][0];
        $validated['service_id'] = $primaryService['service_id'];

        // Initialize costs
        $validated['labor_cost'] = 0;
        $validated['parts_cost'] = 0;
        $validated['total_cost'] = 0;
        $validated['total_estimated_duration'] = 0;

        // Remove services from validated data as it's not a database field
        $services = $validated['services'];
        unset($validated['services']);

        $repairOrder = RepairOrder::create($validated);

        // Attach services to the repair order
        $totalLaborCost = 0;
        foreach ($services as $serviceData) {
            $service = Service::find($serviceData['service_id']);
            $servicePrice = $serviceData['service_price'] ?? $service->base_price;
            $totalLaborCost += $servicePrice;

            $repairOrder->services()->attach($serviceData['service_id'], [
                'service_price' => $servicePrice,
                'estimated_duration' => $service->estimated_duration,
                'status' => 'pending',
                'service_notes' => $serviceData['service_notes'] ?? null,
            ]);
        }

        // Update labor cost
        $repairOrder->labor_cost = $totalLaborCost;

        // Handle parts if provided
        if (!empty($validated['selected_parts'])) {
            foreach ($validated['selected_parts'] as $partData) {
                // Validate stock availability
                $part = \App\Models\Part::find($partData['part_id']);
                if (!$part) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected part not found.'
                    ], 422);
                }

                if ($part->quantity_in_stock < $partData['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for {$part->name}. Available: {$part->quantity_in_stock}, Requested: {$partData['quantity']}"
                    ], 422);
                }

                if ($part->quantity_in_stock == 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "{$part->name} is out of stock."
                    ], 422);
                }

                $totalPrice = $partData['quantity'] * $partData['unit_price'];

                $repairOrder->parts()->attach($partData['part_id'], [
                    'quantity_used' => $partData['quantity'],
                    'unit_price' => $partData['unit_price'],
                    'total_price' => $totalPrice,
                ]);

                // Update part stock
                $part->quantity_in_stock = max(0, $part->quantity_in_stock - $partData['quantity']);
                $part->save();
            }

            // Update parts cost in repair order
            $totalPartsCost = collect($validated['selected_parts'])->sum(function ($part) {
                return $part['quantity'] * $part['unit_price'];
            });

            $repairOrder->parts_cost = $totalPartsCost;
        } else {
            $totalPartsCost = 0;
        }

        // Update total cost
        $repairOrder->total_cost = $repairOrder->labor_cost + $totalPartsCost;
        $repairOrder->save();

        // Update totals to ensure everything is calculated correctly
        $repairOrder->updateTotals();

        // If it's an AJAX request (from modal), return JSON response
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Repair order created successfully.',
                'repair_order' => $repairOrder
            ]);
        }

        return redirect()->route('repair-orders.show', $repairOrder)
            ->with('success', 'Repair order created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RepairOrder $repairOrder)
    {
        $repairOrder->load([
            'customer',
            'device.deviceType',
            'service',
            'services',
            'technician.user',
            'parts',
            'createdBy'
        ]);

        // Add computed attributes
        $repairOrder->append(['assigned_technician_name', 'technician_selection_id']);

        // Get technicians and admin users who can act as technicians
        $technicians = Technician::with('user')->where('is_active', true)->get();
        $adminUsers = \App\Models\User::where('role', 'admin')->where('is_active', true)->get();

        // Create mock technician objects for admin users
        $adminTechnicians = $adminUsers->map(function ($user) {
            return (object) [
                'id' => 'admin-' . $user->id,
                'user_id' => $user->id,
                'employee_id' => 'ADMIN-' . $user->id,
                'specialization' => 'Administrator',
                'is_active' => true,
                'user' => $user,
            ];
        });

        // Combine technicians and admin users
        $allTechnicians = $technicians->concat($adminTechnicians);

        return Inertia::render('RepairOrders/Show', [
            'repairOrder' => $repairOrder,
            'customers' => Customer::all(),
            'devices' => Device::with('deviceType')->get(),
            'services' => Service::with('deviceType')->where('is_active', true)->get(),
            'technicians' => $allTechnicians
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RepairOrder $repairOrder)
    {
        // Load the services relationship for the repair order
        $repairOrder->load(['services', 'customer', 'device.deviceType', 'service', 'technician.user']);

        $customers = Customer::orderBy('first_name')->get();
        $devices = Device::where('customer_id', $repairOrder->customer_id)->with('deviceType')->get();
        $services = Service::with('deviceType')->where('is_active', true)->orderBy('name')->get();

        // Get technicians and admin users who can act as technicians
        $technicians = Technician::with('user')->where('is_active', true)->get();
        $adminUsers = \App\Models\User::where('role', 'admin')->where('is_active', true)->get();

        // Create mock technician objects for admin users
        $adminTechnicians = $adminUsers->map(function ($user) {
            return (object) [
                'id' => 'admin-' . $user->id,
                'user_id' => $user->id,
                'employee_id' => 'ADMIN-' . $user->id,
                'specialization' => 'Administrator',
                'is_active' => true,
                'user' => $user,
            ];
        });

        // Combine technicians and admin users
        $allTechnicians = $technicians->concat($adminTechnicians);

        return Inertia::render('RepairOrders/Edit', [
            'repairOrder' => $repairOrder,
            'customers' => $customers,
            'devices' => $devices,
            'services' => $services,
            'technicians' => $allTechnicians,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RepairOrder $repairOrder)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'device_id' => 'required|exists:devices,id',
            'services' => 'required|array|min:1',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.service_price' => 'nullable|numeric|min:0',
            'services.*.service_notes' => 'nullable|string',
            'technician_id' => 'nullable|string', // Changed to string to handle admin-{id} format
            'issue_description' => 'required|string',
            'diagnosis' => 'nullable|string',
            'solution' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,waiting_parts,completed,cancelled,delivered',
            'priority' => 'required|in:low,medium,high,urgent',
            'customer_notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'selected_parts' => 'nullable|array',
            'selected_parts.*.part_id' => 'required_with:selected_parts|exists:parts,id',
            'selected_parts.*.quantity' => 'required_with:selected_parts|integer|min:1',
            'selected_parts.*.unit_price' => 'required_with:selected_parts|numeric|min:0',
        ]);

        // Handle technician_id validation (can be technician ID or admin-{user_id})
        if (!empty($validated['technician_id'])) {
            if (str_starts_with($validated['technician_id'], 'admin-')) {
                // Extract user ID from admin-{user_id} format
                $userId = str_replace('admin-', '', $validated['technician_id']);
                $adminUser = \App\Models\User::where('id', $userId)->where('role', 'admin')->where('is_active', true)->first();
                if (!$adminUser) {
                    return back()->withErrors(['technician_id' => 'Selected admin user not found or inactive.']);
                }
                // Set technician_id to null and assigned_to to the admin user ID
                $validated['technician_id'] = null;
                $validated['assigned_to'] = $userId;
            } else {
                // Regular technician validation
                $technician = Technician::where('id', $validated['technician_id'])->where('is_active', true)->first();
                if (!$technician) {
                    return back()->withErrors(['technician_id' => 'Selected technician not found or inactive.']);
                }
                // Clear assigned_to for regular technicians
                $validated['assigned_to'] = null;
            }
        } else {
            // Clear both if no technician selected
            $validated['technician_id'] = null;
            $validated['assigned_to'] = null;
        }

        // Extract services and parts data
        $services = $validated['services'];
        $selectedParts = $validated['selected_parts'] ?? [];

        // Remove services and selected_parts from validated data as they're not direct columns
        unset($validated['services'], $validated['selected_parts']);

        // Set the primary service (first service) for backward compatibility
        $validated['service_id'] = $services[0]['service_id'];

        // Calculate labor cost from services
        $totalLaborCost = 0;
        foreach ($services as $serviceData) {
            $service = Service::find($serviceData['service_id']);
            $servicePrice = $serviceData['service_price'] ?? $service->base_price;
            $totalLaborCost += $servicePrice;
        }
        $validated['labor_cost'] = $totalLaborCost;

        // Calculate parts cost
        $totalPartsCost = 0;
        foreach ($selectedParts as $partData) {
            $totalPartsCost += $partData['unit_price'] * $partData['quantity'];
        }
        $validated['parts_cost'] = $totalPartsCost;

        $validated['total_cost'] = $validated['labor_cost'] + $validated['parts_cost'];

        // Set completion date if status is completed
        if ($validated['status'] === 'completed' && $repairOrder->status !== 'completed') {
            $validated['actual_completion'] = now();
        }

        $repairOrder->update($validated);

        // Update services - detach all and reattach
        $repairOrder->services()->detach();
        foreach ($services as $serviceData) {
            $service = Service::find($serviceData['service_id']);
            $servicePrice = $serviceData['service_price'] ?? $service->base_price;

            $repairOrder->services()->attach($serviceData['service_id'], [
                'service_price' => $servicePrice,
                'estimated_duration' => $service->estimated_duration,
                'status' => 'pending',
                'service_notes' => $serviceData['service_notes'] ?? null,
            ]);
        }

        // Update parts - detach all and reattach
        $repairOrder->parts()->detach();
        foreach ($selectedParts as $partData) {
            $repairOrder->parts()->attach($partData['part_id'], [
                'quantity_used' => $partData['quantity'],
                'unit_price' => $partData['unit_price'],
                'total_price' => $partData['unit_price'] * $partData['quantity'],
            ]);
        }

        // If it's an AJAX request (from modal), return JSON response
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Repair order updated successfully.',
                'repair_order' => $repairOrder
            ]);
        }

        return redirect()->route('repair-orders.show', $repairOrder)
            ->with('success', 'Repair order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RepairOrder $repairOrder)
    {
        $repairOrder->delete();

        return redirect()->route('repair-orders.index')
            ->with('success', 'Repair order deleted successfully.');
    }

    /**
     * Update the status of a repair order (for technicians).
     */
    public function updateStatus(Request $request, RepairOrder $repairOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,waiting_parts,completed,cancelled,delivered'
        ]);

        $updateData = ['status' => $validated['status']];

        // If marking as completed, set completion date
        if ($validated['status'] === 'completed') {
            $updateData['actual_completion'] = now();
        }

        $repairOrder->update($updateData);

        return back()->with('success', 'Order status updated successfully.');
    }

    /**
     * Display repair orders for the authenticated technician or admin in technician view.
     */
    public function technicianOrders(Request $request)
    {
        $user = auth()->user();

        // Allow technicians and admin users (admin can view as technician)
        if ($user->role === 'technician' && !$user->technician) {
            abort(403, 'Technician profile not found.');
        } elseif ($user->role !== 'technician' && $user->role !== 'admin') {
            abort(403, 'Access denied.');
        }

        // Handle different user types
        if ($user->role === 'technician') {
            $technician = $user->technician;
            $query = $technician->repairOrders()->with(['customer', 'device.deviceType', 'service', 'services', 'createdBy']);
        } else {
            // Admin user in technician view - show orders assigned to them as admin
            $query = RepairOrder::with(['customer', 'device.deviceType', 'service', 'services', 'createdBy'])
                ->where('assigned_to', $user->id);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('issue_description', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('first_name', 'like', "%{$search}%")
                                   ->orWhere('last_name', 'like', "%{$search}%")
                                   ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                  });
            });
        }

        // Status filter
        if ($request->filled('status') && $request->get('status') !== 'all') {
            $query->where('status', $request->get('status'));
        }

        // Priority filter
        if ($request->filled('priority') && $request->get('priority') !== 'all') {
            $query->where('priority', $request->get('priority'));
        }

        $repairOrders = $query->orderBy('priority', 'desc')
                             ->orderBy('created_at', 'desc')
                             ->paginate(15)
                             ->withQueryString();

        // Append computed attributes to each repair order
        $repairOrders->getCollection()->transform(function ($repairOrder) {
            $repairOrder->append(['assigned_technician_name', 'technician_selection_id']);
            return $repairOrder;
        });

        // For admin users, create a mock technician object
        $technicianData = $user->role === 'technician' ? $user->technician : null;

        return Inertia::render('Technician/Orders', [
            'repairOrders' => $repairOrders,
            'technician' => $technicianData,
            'filters' => $request->only(['search', 'status', 'priority']),
            'isAdminView' => $user->role === 'admin'
        ]);
    }

    /**
     * Mark a repair order as delivered.
     */
    public function markDelivered(RepairOrder $repairOrder)
    {
        // Only allow marking as delivered if it's completed
        if ($repairOrder->status !== 'completed') {
            return back()->withErrors(['error' => 'Only completed orders can be marked as delivered.']);
        }

        $repairOrder->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        // If it's an AJAX request, return JSON response
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order marked as delivered successfully!'
            ]);
        }

        return back()->with('success', 'Order marked as delivered successfully!');
    }

    /**
     * Cancel a repair order.
     */
    public function cancel(Request $request, RepairOrder $repairOrder)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
            'restore_parts' => 'boolean',
        ]);

        // Check if the order can be cancelled
        if (!$repairOrder->canBeCancelled()) {
            $error = "Cannot cancel order with status '{$repairOrder->status}'.";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $error
                ], 422);
            }

            return back()->withErrors(['error' => $error]);
        }

        // Cancel the order using the model method
        $success = $repairOrder->cancel(
            $validated['cancellation_reason'],
            $validated['restore_parts'] ?? true
        );

        if (!$success) {
            $error = 'Failed to cancel the repair order.';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $error
                ], 500);
            }

            return back()->withErrors(['error' => $error]);
        }

        $message = 'Repair order cancelled successfully.';
        if ($validated['restore_parts'] ?? true) {
            $message .= ' Parts have been restored to inventory.';
        }

        // If it's an AJAX request, return JSON response
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'repair_order' => $repairOrder->fresh()
            ]);
        }

        return back()->with('success', $message);
    }
}
