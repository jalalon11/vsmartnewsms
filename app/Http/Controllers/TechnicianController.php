<?php

namespace App\Http\Controllers;

use App\Models\Technician;
use App\Models\User;
use App\Models\RepairOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class TechnicianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Technician::with(['user', 'repairOrders']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
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

        // Specialization filter
        if ($request->filled('specialization') && $request->get('specialization') !== 'all') {
            $query->where('specialization', $request->get('specialization'));
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        switch ($sortBy) {
            case 'name':
                $query->join('users', 'technicians.user_id', '=', 'users.id')
                      ->orderBy('users.name', $direction)
                      ->select('technicians.*');
                break;
            case 'employee_id':
                $query->orderBy('employee_id', $direction);
                break;
            case 'specialization':
                $query->orderBy('specialization', $direction);
                break;
            case 'created_at':
                $query->orderBy('created_at', $direction);
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        // Add counts for repair orders
        $query->withCount(['repairOrders', 'repairOrders as active_orders_count' => function ($q) {
            $q->whereIn('status', ['pending', 'in_progress', 'waiting_parts']);
        }]);

        $technicians = $query->paginate(15)->withQueryString();

        // Get unique specializations for filter
        $specializations = Technician::distinct()->pluck('specialization')->filter()->sort()->values();

        return Inertia::render('Technicians/Index', [
            'technicians' => $technicians,
            'specializations' => $specializations,
            'filters' => $request->only(['search', 'status', 'specialization', 'sort', 'direction'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Technicians/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'employee_id' => 'required|string|max:50|unique:technicians',
            'phone' => 'nullable|string|max:20',
            'specialization' => 'required|string|max:100',
            'hire_date' => 'required|date',
            'certifications' => 'nullable|string',
            'skills' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Create user first
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'technician',
            'is_active' => true,
        ]);

        // Create technician
        Technician::create([
            'user_id' => $user->id,
            'employee_id' => $validated['employee_id'],
            'phone' => $validated['phone'],
            'specialization' => $validated['specialization'],
            'hire_date' => $validated['hire_date'],
            'certifications' => $validated['certifications'],
            'skills' => $validated['skills'],
            'notes' => $validated['notes'],
            'is_active' => true,
        ]);

        return redirect()->route('technicians.index')
                        ->with('success', 'Technician created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Technician $technician)
    {
        $technician->load([
            'user',
            'repairOrders' => function ($query) {
                $query->with(['customer', 'device.deviceType', 'service', 'services'])
                      ->orderBy('created_at', 'desc')
                      ->limit(10);
            }
        ]);

        // Get statistics
        $stats = [
            'total_orders' => $technician->repairOrders()->count(),
            'completed_orders' => $technician->repairOrders()->whereIn('status', ['completed', 'delivered'])->count(),
            'in_progress_orders' => $technician->repairOrders()->whereIn('status', ['pending', 'in_progress', 'waiting_parts'])->count(),
            'avg_completion_time' => $technician->repairOrders()
                ->whereIn('status', ['completed', 'delivered'])
                ->whereNotNull('actual_completion')
                ->selectRaw('AVG(DATEDIFF(actual_completion, created_at)) as avg_days')
                ->value('avg_days'),
            'total_revenue' => $technician->repairOrders()
                ->whereIn('status', ['completed', 'delivered'])
                ->sum('total_cost'),
        ];

        return Inertia::render('Technicians/Show', [
            'technician' => $technician,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Technician $technician)
    {
        $technician->load('user');

        return Inertia::render('Technicians/Edit', [
            'technician' => $technician
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Technician $technician)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $technician->user_id,
            'employee_id' => 'required|string|max:50|unique:technicians,employee_id,' . $technician->id,
            'phone' => 'nullable|string|max:20',
            'specialization' => 'required|string|max:100',
            'hire_date' => 'required|date',
            'certifications' => 'nullable|string',
            'skills' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Update user
        $technician->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update technician
        $technician->update([
            'employee_id' => $validated['employee_id'],
            'phone' => $validated['phone'],
            'specialization' => $validated['specialization'],
            'hire_date' => $validated['hire_date'],
            'certifications' => $validated['certifications'],
            'skills' => $validated['skills'],
            'notes' => $validated['notes'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('technicians.index')
                        ->with('success', 'Technician updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Technician $technician)
    {
        // Check if technician has active repair orders
        $activeOrders = $technician->repairOrders()
            ->whereIn('status', ['pending', 'in_progress', 'waiting_parts'])
            ->count();

        if ($activeOrders > 0) {
            return back()->withErrors([
                'error' => 'Cannot delete technician with active repair orders. Please reassign or complete all active orders first.'
            ]);
        }

        // Soft delete by setting is_active to false instead of actual deletion
        $technician->update(['is_active' => false]);

        return redirect()->route('technicians.index')
                        ->with('success', 'Technician deactivated successfully.');
    }

    /**
     * Toggle technician active status
     */
    public function toggleStatus(Technician $technician)
    {
        if ($technician->is_active) {
            // Deactivating - check for active orders
            $activeOrders = $technician->repairOrders()
                ->whereIn('status', ['pending', 'in_progress', 'waiting_parts'])
                ->count();

            if ($activeOrders > 0) {
                return back()->withErrors(['error' => 'Cannot deactivate technician with active repair orders.']);
            }

            $technician->update(['is_active' => false]);
            $message = 'Technician deactivated successfully.';
        } else {
            // Reactivating
            $technician->update(['is_active' => true]);
            $message = 'Technician reactivated successfully.';
        }

        // If it's an AJAX request, return JSON response
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return back()->with('success', $message);
    }
}
