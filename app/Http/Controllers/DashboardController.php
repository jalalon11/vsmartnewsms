<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\RepairOrder;
use App\Models\Invoice;
use App\Models\Part;
use App\Models\Technician;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Check if user is a technician
        if ($user->role === 'technician') {
            return $this->technicianDashboard($user);
        }

        // Admin dashboard
        return $this->adminDashboard();
    }

    private function adminDashboard()
    {
        // Get statistics
        $stats = [
            'total_customers' => Customer::count(),
            'active_repairs' => RepairOrder::whereIn('status', ['pending', 'in_progress', 'waiting_parts'])->count(),
            'completed_today' => RepairOrder::where(function($query) {
                $query->where('status', 'completed')
                      ->whereDate('actual_completion', Carbon::today());
            })->orWhere(function($query) {
                $query->where('status', 'delivered')
                      ->whereDate('delivered_at', Carbon::today());
            })->count(),
            'pending_invoices' => Invoice::where('status', 'pending')->count(),
            'low_stock_parts' => Part::whereRaw('quantity_in_stock <= minimum_stock_level')->count(),
        ];

        // Recent repair orders
        $recentRepairs = RepairOrder::with(['customer', 'device.deviceType', 'service', 'technician.user', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Append computed attributes to each repair order
        $recentRepairs->each(function ($repairOrder) {
            $repairOrder->append(['assigned_technician_name', 'technician_selection_id']);
        });

        // Repair status distribution
        $repairStatusStats = RepairOrder::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Monthly revenue (last 6 months)
        $monthlyRevenue = Invoice::where('status', 'paid')
            ->where('paid_date', '>=', Carbon::now()->subMonths(6))
            ->selectRaw('MONTH(paid_date) as month, YEAR(paid_date) as year, SUM(total_amount) as revenue')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Sales analytics data
        $salesAnalytics = $this->getSalesAnalytics();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentRepairs' => $recentRepairs,
            'repairStatusStats' => $repairStatusStats,
            'monthlyRevenue' => $monthlyRevenue,
            'salesAnalytics' => $salesAnalytics,
            'userRole' => 'admin',
        ]);
    }

    private function technicianDashboard($user)
    {
        $technician = $user->technician;

        if (!$technician) {
            // If user is marked as technician but no technician record exists
            return redirect()->route('login')->withErrors(['error' => 'Technician profile not found.']);
        }

        // Get technician-specific statistics
        $stats = [
            'assigned_orders' => $technician->repairOrders()->whereIn('status', ['pending', 'in_progress', 'waiting_parts'])->count(),
            'completed_today' => $technician->repairOrders()->whereIn('status', ['completed', 'delivered'])
                ->whereDate('actual_completion', Carbon::today())
                ->count(),
            'total_completed' => $technician->repairOrders()->whereIn('status', ['completed', 'delivered'])->count(),
            'pending_orders' => $technician->repairOrders()->where('status', 'pending')->count(),
        ];

        // Technician's assigned repair orders
        $assignedOrders = $technician->repairOrders()
            ->with(['customer', 'device.deviceType', 'service'])
            ->whereIn('status', ['pending', 'in_progress', 'waiting_parts'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent completed orders
        $recentCompleted = $technician->repairOrders()
            ->with(['customer', 'device.deviceType', 'service'])
            ->whereIn('status', ['completed', 'delivered'])
            ->orderBy('actual_completion', 'desc')
            ->limit(5)
            ->get();

        // Performance stats (last 30 days)
        $performanceStats = [
            'orders_completed' => $technician->repairOrders()
                ->whereIn('status', ['completed', 'delivered'])
                ->where('actual_completion', '>=', Carbon::now()->subDays(30))
                ->count(),
            'avg_completion_time' => $technician->repairOrders()
                ->whereIn('status', ['completed', 'delivered'])
                ->whereNotNull('actual_completion')
                ->where('actual_completion', '>=', Carbon::now()->subDays(30))
                ->selectRaw('AVG(DATEDIFF(actual_completion, created_at)) as avg_days')
                ->value('avg_days'),
        ];

        return Inertia::render('TechnicianDashboard', [
            'stats' => $stats,
            'assignedOrders' => $assignedOrders,
            'recentCompleted' => $recentCompleted,
            'performanceStats' => $performanceStats,
            'technician' => $technician,
            'userRole' => 'technician',
        ]);
    }

    private function getSalesAnalytics()
    {
        $now = Carbon::now();

        // This week sales
        $weekStart = $now->copy()->startOfWeek();
        $weekEnd = $now->copy()->endOfWeek();
        $weekSales = Invoice::where('status', 'paid')
            ->whereBetween('paid_date', [$weekStart, $weekEnd])
            ->sum('total_amount');

        // This month sales
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();
        $monthSales = Invoice::where('status', 'paid')
            ->whereBetween('paid_date', [$monthStart, $monthEnd])
            ->sum('total_amount');

        // This year sales
        $yearStart = $now->copy()->startOfYear();
        $yearEnd = $now->copy()->endOfYear();
        $yearSales = Invoice::where('status', 'paid')
            ->whereBetween('paid_date', [$yearStart, $yearEnd])
            ->sum('total_amount');

        // Weekly sales data for chart (last 7 days)
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $sales = Invoice::where('status', 'paid')
                ->whereDate('paid_date', $date)
                ->sum('total_amount');
            $weeklyData[] = [
                'date' => $date->format('M d'),
                'sales' => (float) $sales
            ];
        }

        // Monthly sales data for chart (last 12 months)
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $sales = Invoice::where('status', 'paid')
                ->whereYear('paid_date', $date->year)
                ->whereMonth('paid_date', $date->month)
                ->sum('total_amount');
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'sales' => (float) $sales
            ];
        }

        // Yearly sales data for chart (last 5 years)
        $yearlyData = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = Carbon::now()->subYears($i)->year;
            $sales = Invoice::where('status', 'paid')
                ->whereYear('paid_date', $year)
                ->sum('total_amount');
            $yearlyData[] = [
                'year' => $year,
                'sales' => (float) $sales
            ];
        }

        return [
            'totals' => [
                'week' => (float) $weekSales,
                'month' => (float) $monthSales,
                'year' => (float) $yearSales
            ],
            'charts' => [
                'weekly' => $weeklyData,
                'monthly' => $monthlyData,
                'yearly' => $yearlyData
            ]
        ];
    }

    public function technicianView()
    {
        $user = auth()->user();

        // Only allow admin users to access this view
        if ($user->role !== 'admin') {
            return redirect()->route('dashboard')->withErrors(['error' => 'Access denied.']);
        }

        // Create a mock technician object for admin
        $mockTechnician = (object) [
            'id' => null,
            'user_id' => $user->id,
            'employee_id' => 'ADMIN-' . $user->id,
            'specialization' => 'Administrator',
            'user' => $user,
            'is_active' => true,
        ];

        // Get repair orders assigned to admin as technician
        $assignedOrders = RepairOrder::with(['customer', 'device.deviceType', 'service'])
            ->where('assigned_to', $user->id)
            ->whereIn('status', ['pending', 'in_progress', 'waiting_parts'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent completed orders (admin can see all)
        $recentCompleted = RepairOrder::with(['customer', 'device.deviceType', 'service'])
            ->whereIn('status', ['completed', 'delivered'])
            ->orderBy('actual_completion', 'desc')
            ->limit(5)
            ->get();

        // Performance stats for admin
        $performanceStats = [
            'total_completed' => RepairOrder::whereIn('status', ['completed', 'delivered'])->count(),
            'avg_completion_time' => RepairOrder::whereIn('status', ['completed', 'delivered'])
                ->whereNotNull('actual_completion')
                ->selectRaw('AVG(DATEDIFF(actual_completion, created_at)) as avg_days')
                ->value('avg_days') ?? 0,
            'completion_rate' => RepairOrder::count() > 0
                ? (RepairOrder::whereIn('status', ['completed', 'delivered'])->count() / RepairOrder::count()) * 100
                : 0,
        ];

        // Stats for technician view
        $stats = [
            'assigned_orders' => $assignedOrders->count(),
            'completed_today' => RepairOrder::whereIn('status', ['completed', 'delivered'])
                ->whereDate('actual_completion', Carbon::today())
                ->count(),
            'pending_orders' => RepairOrder::where('status', 'pending')->count(),
            'in_progress_orders' => RepairOrder::where('status', 'in_progress')->count(),
        ];

        return Inertia::render('TechnicianDashboard', [
            'stats' => $stats,
            'assignedOrders' => $assignedOrders,
            'recentCompleted' => $recentCompleted,
            'performanceStats' => $performanceStats,
            'technician' => $mockTechnician,
            'userRole' => 'admin-as-technician',
            'isAdminView' => true,
        ]);
    }
}
