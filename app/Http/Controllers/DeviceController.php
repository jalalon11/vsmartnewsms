<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Customer;
use App\Models\DeviceType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Device::with(['customer', 'deviceType']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('imei', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('first_name', 'like', "%{$search}%")
                                   ->orWhere('last_name', 'like', "%{$search}%")
                                   ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                  });
            });
        }

        // Device type filter
        if ($request->filled('device_type') && $request->get('device_type') !== 'all') {
            $query->where('device_type_id', $request->get('device_type'));
        }

        // Customer filter
        if ($request->filled('customer') && $request->get('customer') !== 'all') {
            $query->where('customer_id', $request->get('customer'));
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        switch ($sortBy) {
            case 'brand':
                $query->orderBy('brand', $direction)->orderBy('model', $direction);
                break;
            case 'customer':
                $query->join('customers', 'devices.customer_id', '=', 'customers.id')
                      ->orderBy('customers.first_name', $direction)
                      ->orderBy('customers.last_name', $direction)
                      ->select('devices.*');
                break;
            case 'created_at':
                $query->orderBy('created_at', $direction);
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        // Add counts for repair orders
        $query->withCount(['repairOrders']);

        $devices = $query->paginate(15)->withQueryString();

        // Get data for filters
        $customers = Customer::orderBy('first_name')->get();
        $deviceTypes = DeviceType::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('Devices/Index', [
            'devices' => $devices,
            'customers' => $customers,
            'deviceTypes' => $deviceTypes,
            'filters' => $request->only(['search', 'device_type', 'customer', 'sort', 'direction'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $customers = Customer::orderBy('first_name')->get();
        $deviceTypes = DeviceType::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('Devices/Create', [
            'customers' => $customers,
            'deviceTypes' => $deviceTypes,
            'selectedCustomer' => $request->get('customer_id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'device_type_id' => 'required|exists:device_types,id',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'imei' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:100',
            'specifications' => 'nullable|string',
            'accessories' => 'nullable|string',
            'condition_notes' => 'nullable|string',
        ]);

        $device = Device::create($validated);

        return redirect()->route('devices.show', $device)
            ->with('success', 'Device registered successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Device $device)
    {
        $device->load(['customer', 'deviceType', 'repairOrders.service', 'repairOrders.services']);

        $customers = Customer::orderBy('first_name')->get();
        $deviceTypes = DeviceType::orderBy('name')->get();

        // Get technicians and admin users who can act as technicians
        $technicians = \App\Models\Technician::with('user')->where('is_active', true)->get();
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

        return Inertia::render('Devices/Show', [
            'device' => $device,
            'customers' => $customers,
            'deviceTypes' => $deviceTypes,
            'services' => \App\Models\Service::with('deviceType')->where('is_active', true)->orderBy('name')->get(),
            'technicians' => $allTechnicians,
            'parts' => \App\Models\Part::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Device $device)
    {
        $customers = Customer::orderBy('first_name')->get();
        $deviceTypes = DeviceType::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('Devices/Edit', [
            'device' => $device,
            'customers' => $customers,
            'deviceTypes' => $deviceTypes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Device $device)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'device_type_id' => 'required|exists:device_types,id',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'imei' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:100',
            'specifications' => 'nullable|string',
            'accessories' => 'nullable|string',
            'condition_notes' => 'nullable|string',
        ]);

        $device->update($validated);

        return redirect()->route('devices.show', $device)
            ->with('success', 'Device updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Device $device)
    {
        $device->delete();

        return redirect()->route('devices.index')
            ->with('success', 'Device deleted successfully.');
    }

    /**
     * Get devices for a specific customer
     */
    public function getCustomerDevices(Customer $customer)
    {
        $devices = $customer->devices()->with('deviceType')->get();

        return response()->json($devices);
    }

    /**
     * Get parts compatible with a specific device
     */
    public function getDeviceParts(Device $device)
    {
        // Get parts that are specifically assigned to this device
        $deviceParts = \App\Models\Part::where('device_id', $device->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get parts that are compatible with this device type
        $compatibleParts = \App\Models\Part::where('is_active', true)
            ->where(function ($query) use ($device) {
                $query->whereJsonContains('compatible_devices', $device->id)
                      ->orWhereJsonContains('compatible_devices', $device->device_type_id);
            })
            ->orderBy('name')
            ->get();

        return response()->json([
            'device_parts' => $deviceParts,
            'compatible_parts' => $compatibleParts,
        ]);
    }
}
