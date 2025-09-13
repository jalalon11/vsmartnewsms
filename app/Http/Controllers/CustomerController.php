<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Customer::with(['devices', 'repairOrders']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        // Remove status filter - no longer needed

        // Sorting
        $sortBy = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');

        switch ($sortBy) {
            case 'name':
                $query->orderBy('first_name', $direction)->orderBy('last_name', $direction);
                break;
            case 'email':
                $query->orderBy('email', $direction);
                break;
            case 'created_at':
                $query->orderBy('created_at', $direction);
                break;
            default:
                $query->orderBy('first_name', 'asc')->orderBy('last_name', 'asc');
        }

        // Add counts for devices and repair orders with status breakdown
        $query->withCount([
            'devices',
            'repairOrders',
            'repairOrders as pending_orders_count' => function ($q) {
                $q->where('status', 'pending');
            },
            'repairOrders as in_progress_orders_count' => function ($q) {
                $q->where('status', 'in_progress');
            },
            'repairOrders as completed_orders_count' => function ($q) {
                $q->where('status', 'completed');
            }
        ]);

        $customers = $query->paginate(15)->withQueryString();

        return Inertia::render('Customers/Index', [
            'customers' => $customers,
            'filters' => $request->only(['search', 'sort', 'direction'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Customers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'facebook_link' => 'required|url',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zip_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
        ]);

        $customer = Customer::create($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $customer->load(['devices.deviceType', 'repairOrders.service', 'repairOrders.services', 'repairOrders.technician.user']);

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

        return Inertia::render('Customers/Show', [
            'customer' => $customer,
            'deviceTypes' => \App\Models\DeviceType::all(),
            'services' => \App\Models\Service::where('is_active', true)->get(),
            'technicians' => $allTechnicians,
            'parts' => \App\Models\Part::where('is_active', true)->orderBy('name')->get()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return Inertia::render('Customers/Edit', [
            'customer' => $customer
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'facebook_link' => 'required|url',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zip_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
