<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Service::with('deviceType');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhereHas('deviceType', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status') && $request->get('status') !== 'all') {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Category filter
        if ($request->filled('category') && $request->get('category') !== 'all') {
            $query->where('category', $request->get('category'));
        }

        // Device type filter
        if ($request->filled('device_type') && $request->device_type !== 'all') {
            $query->where('device_type_id', $request->device_type);
        }

        // Sorting
        $sortBy = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');

        switch ($sortBy) {
            case 'name':
                $query->orderBy('name', $direction);
                break;
            case 'base_price':
                $query->orderBy('base_price', $direction);
                break;
            case 'category':
                $query->orderBy('category', $direction);
                break;
            case 'created_at':
                $query->orderBy('created_at', $direction);
                break;
            default:
                $query->orderBy('name', 'asc');
        }

        // Add counts for repair orders
        $query->withCount(['repairOrders']);

        $services = $query->paginate(15)->withQueryString();

        // Get unique categories for filter
        $categories = Service::distinct()->pluck('category')->filter()->sort()->values();

        return Inertia::render('Services/Index', [
            'services' => $services,
            'categories' => $categories,
            'deviceTypes' => \App\Models\DeviceType::withCount('services')->orderBy('name')->get(),
            'filters' => $request->only(['search', 'status', 'category', 'device_type', 'sort', 'direction'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Services/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'device_type_id' => 'required|exists:device_types,id',
            'base_price' => 'required|numeric|min:0',
            'estimated_duration' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        // Set a default category based on device type
        $deviceType = \App\Models\DeviceType::find($validated['device_type_id']);
        $validated['category'] = $deviceType ? $deviceType->name : 'General';

        Service::create($validated);

        return redirect()->route('services.index')
                        ->with('success', 'Service created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        $service->load([
            'repairOrders' => function ($query) {
                $query->with(['customer', 'device.deviceType', 'technician.user'])
                      ->orderBy('created_at', 'desc')
                      ->limit(10);
            }
        ]);

        // Get statistics
        $stats = [
            'total_orders' => $service->repairOrders()->count(),
            'completed_orders' => $service->repairOrders()->where('status', 'completed')->count(),
            'in_progress_orders' => $service->repairOrders()->whereIn('status', ['pending', 'in_progress', 'waiting_parts'])->count(),
            'total_revenue' => $service->repairOrders()
                ->where('status', 'completed')
                ->sum('total_cost'),
            'avg_completion_time' => $service->repairOrders()
                ->where('status', 'completed')
                ->whereNotNull('actual_completion')
                ->selectRaw('AVG(DATEDIFF(actual_completion, created_at)) as avg_days')
                ->value('avg_days'),
        ];

        return Inertia::render('Services/Show', [
            'service' => $service,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        return Inertia::render('Services/Edit', [
            'service' => $service
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'device_type_id' => 'required|exists:device_types,id',
            'base_price' => 'required|numeric|min:0',
            'estimated_duration' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        // Set a default category based on device type
        $deviceType = \App\Models\DeviceType::find($validated['device_type_id']);
        $validated['category'] = $deviceType ? $deviceType->name : 'General';

        $service->update($validated);

        return redirect()->route('services.index')
                        ->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        // Check if service has active repair orders
        $activeOrders = $service->repairOrders()
            ->whereIn('status', ['pending', 'in_progress', 'waiting_parts'])
            ->count();

        if ($activeOrders > 0) {
            return back()->withErrors([
                'error' => 'Cannot delete service with active repair orders. Please complete or reassign all active orders first.'
            ]);
        }

        // Soft delete by setting is_active to false instead of actual deletion
        $service->update(['is_active' => false]);

        return redirect()->route('services.index')
                        ->with('success', 'Service deactivated successfully.');
    }
}
