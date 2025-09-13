<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\RepairOrder;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\Device;
use App\Models\DeviceType;
use App\Models\Part;
use App\Models\Technician;
use App\Models\InvoicePayment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NewReportController extends Controller
{
    /**
     * Main Reports Dashboard - Comprehensive Analytics
     */
    public function index(Request $request)
    {
        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);

        return Inertia::render('Reports/Dashboard', [
            'overview' => $this->getOverviewMetrics($dateRange),
            'customerMetrics' => $this->getCustomerMetrics($dateRange),
            'salesMetrics' => $this->getSalesMetrics($dateRange),
            'repairMetrics' => $this->getRepairMetrics($dateRange),
            'partsMetrics' => $this->getPartsMetrics($dateRange),
            'technicianMetrics' => $this->getTechnicianMetrics($dateRange),
            'charts' => $this->getDashboardCharts($dateRange),
            'period' => $period,
            'dateRange' => $dateRange,
        ]);
    }

    /**
     * Revenue Analytics Page
     */
    public function revenue(Request $request)
    {
        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);

        return Inertia::render('Reports/Revenue', [
            'data' => $this->getRevenueData($dateRange),
            'charts' => $this->getRevenueCharts($dateRange),
            'period' => $period,
            'dateRange' => $dateRange,
        ]);
    }

    /**
     * Orders Analytics Page
     */
    public function orders(Request $request)
    {
        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);

        return Inertia::render('Reports/Orders', [
            'data' => $this->getOrdersData($dateRange),
            'charts' => $this->getOrdersCharts($dateRange),
            'period' => $period,
            'dateRange' => $dateRange,
        ]);
    }

    /**
     * Customers Analytics Page
     */
    public function customers(Request $request)
    {
        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);

        return Inertia::render('Reports/Customers', [
            'data' => $this->getCustomersData($dateRange),
            'charts' => $this->getCustomersCharts($dateRange),
            'period' => $period,
            'dateRange' => $dateRange,
        ]);
    }

    /**
     * Services Analytics Page
     */
    public function services(Request $request)
    {
        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);

        return Inertia::render('Reports/Services', [
            'data' => $this->getServicesData($dateRange),
            'charts' => $this->getServicesCharts($dateRange),
            'period' => $period,
            'dateRange' => $dateRange,
        ]);
    }

    /**
     * Customer Analytics Detailed Page
     */
    public function customerAnalytics(Request $request)
    {
        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);

        return Inertia::render('Reports/CustomerAnalytics', [
            'metrics' => $this->getCustomerMetrics($dateRange),
            'charts' => $this->getCustomerCharts($dateRange),
            'period' => $period,
            'dateRange' => $dateRange,
        ]);
    }

    /**
     * Sales Analytics Detailed Page
     */
    public function salesAnalytics(Request $request)
    {
        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);

        return Inertia::render('Reports/SalesAnalytics', [
            'metrics' => $this->getSalesMetrics($dateRange),
            'charts' => $this->getSalesCharts($dateRange),
            'period' => $period,
            'dateRange' => $dateRange,
        ]);
    }

    /**
     * Parts Analytics Detailed Page
     */
    public function partsAnalytics(Request $request)
    {
        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);

        return Inertia::render('Reports/PartsAnalytics', [
            'metrics' => $this->getPartsMetrics($dateRange),
            'charts' => $this->getPartsCharts($dateRange),
            'period' => $period,
            'dateRange' => $dateRange,
        ]);
    }

    /**
     * Device Repair Analytics Detailed Page
     */
    public function deviceRepairAnalytics(Request $request)
    {
        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);

        return Inertia::render('Reports/DeviceRepairAnalytics', [
            'metrics' => $this->getRepairMetrics($dateRange),
            'charts' => $this->getRepairCharts($dateRange),
            'period' => $period,
            'dateRange' => $dateRange,
        ]);
    }

    /**
     * Technician Performance Detailed Page
     */
    public function technicianPerformance(Request $request)
    {
        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);

        return Inertia::render('Reports/TechnicianPerformance', [
            'metrics' => $this->getTechnicianMetrics($dateRange),
            'charts' => $this->getTechnicianCharts($dateRange),
            'period' => $period,
            'dateRange' => $dateRange,
        ]);
    }

    /**
     * Get Dashboard Metrics
     */
    private function getDashboardMetrics()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // Current metrics
        $totalRevenue = Invoice::where('status', 'paid')->sum('total_amount');
        $totalOrders = RepairOrder::count();
        $totalCustomers = Customer::count();
        $activeRepairs = RepairOrder::whereIn('status', ['pending', 'in_progress', 'waiting_parts'])->count();

        // Monthly comparisons
        $thisMonthRevenue = Invoice::where('status', 'paid')
            ->whereBetween('paid_date', [$startOfMonth, $now])
            ->sum('total_amount');
        
        $lastMonthRevenue = Invoice::where('status', 'paid')
            ->whereBetween('paid_date', [$startOfLastMonth, $endOfLastMonth])
            ->sum('total_amount');

        $thisMonthOrders = RepairOrder::whereBetween('created_at', [$startOfMonth, $now])->count();
        $lastMonthOrders = RepairOrder::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $thisMonthCustomers = Customer::whereBetween('created_at', [$startOfMonth, $now])->count();
        $lastMonthCustomers = Customer::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'total_customers' => $totalCustomers,
            'active_repairs' => $activeRepairs,
            'revenue_growth' => $this->calculateGrowth($thisMonthRevenue, $lastMonthRevenue),
            'orders_growth' => $this->calculateGrowth($thisMonthOrders, $lastMonthOrders),
            'customers_growth' => $this->calculateGrowth($thisMonthCustomers, $lastMonthCustomers),
            'completion_rate' => $this->getCompletionRate(),
        ];
    }

    /**
     * Get Dashboard Charts Data
     */
    private function getDashboardCharts($dateRange)
    {
        // Revenue trend
        $revenueTrend = Invoice::where('status', 'paid')
            ->whereBetween('paid_date', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('DATE(paid_date) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Repairs by status
        $repairsByStatus = RepairOrder::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Top services by revenue
        $topServices = DB::table('repair_orders')
            ->join('services', 'repair_orders.service_id', '=', 'services.id')
            ->join('invoices', 'repair_orders.id', '=', 'invoices.repair_order_id')
            ->where('invoices.status', 'paid')
            ->whereBetween('invoices.paid_date', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('services.name, SUM(invoices.total_amount) as revenue, COUNT(repair_orders.id) as orders')
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // Device types distribution
        $deviceTypesDistribution = DB::table('repair_orders')
            ->join('devices', 'repair_orders.device_id', '=', 'devices.id')
            ->join('device_types', 'devices.device_type_id', '=', 'device_types.id')
            ->whereBetween('repair_orders.created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('device_types.name, COUNT(*) as count')
            ->groupBy('device_types.id', 'device_types.name')
            ->orderByDesc('count')
            ->get();

        return [
            'revenue_trend' => $revenueTrend,
            'repairs_by_status' => $repairsByStatus,
            'top_services' => $topServices,
            'device_types_distribution' => $deviceTypesDistribution,
        ];
    }

    /**
     * Get Trend Data for Dashboard
     */
    private function getTrendData()
    {
        $last7Days = Carbon::now()->subDays(7);
        
        return [
            'daily_orders' => RepairOrder::where('created_at', '>=', $last7Days)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'daily_customers' => Customer::where('created_at', '>=', $last7Days)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];
    }

    /**
     * Calculate growth percentage
     */
    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }

    /**
     * Get completion rate
     */
    private function getCompletionRate()
    {
        $totalOrders = RepairOrder::count();
        $completedOrders = RepairOrder::whereIn('status', ['completed', 'delivered'])->count();
        
        return $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0;
    }

    /**
     * Get date range based on period
     */
    private function getDateRange($period)
    {
        $now = Carbon::now();

        switch ($period) {
            case '7days':
                return ['start' => $now->copy()->subDays(7), 'end' => $now];
            case '30days':
                return ['start' => $now->copy()->subDays(30), 'end' => $now];
            case '90days':
                return ['start' => $now->copy()->subDays(90), 'end' => $now];
            case 'thisyear':
                return ['start' => $now->copy()->startOfYear(), 'end' => $now];
            default:
                return ['start' => $now->copy()->subDays(30), 'end' => $now];
        }
    }

    /**
     * Get Revenue Data for detailed page
     */
    private function getRevenueData($dateRange)
    {
        $totalRevenue = Invoice::where('status', 'paid')
            ->whereBetween('paid_date', [$dateRange['start'], $dateRange['end']])
            ->sum('total_amount');

        $averageDaily = $totalRevenue / max(1, $dateRange['start']->diffInDays($dateRange['end']));

        $topServices = DB::table('repair_orders')
            ->join('services', 'repair_orders.service_id', '=', 'services.id')
            ->join('invoices', 'repair_orders.id', '=', 'invoices.repair_order_id')
            ->where('invoices.status', 'paid')
            ->whereBetween('invoices.paid_date', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('services.name, SUM(invoices.total_amount) as revenue, COUNT(repair_orders.id) as orders')
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('revenue')
            ->get();

        return [
            'total_revenue' => $totalRevenue,
            'average_daily' => $averageDaily,
            'top_services' => $topServices,
            'revenue_sources' => $topServices->count(),
        ];
    }

    /**
     * Get Revenue Charts Data
     */
    private function getRevenueCharts($dateRange)
    {
        // Daily revenue trend
        $dailyRevenue = Invoice::where('status', 'paid')
            ->whereBetween('paid_date', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('DATE(paid_date) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Revenue by service
        $revenueByService = DB::table('repair_orders')
            ->join('services', 'repair_orders.service_id', '=', 'services.id')
            ->join('invoices', 'repair_orders.id', '=', 'invoices.repair_order_id')
            ->where('invoices.status', 'paid')
            ->whereBetween('invoices.paid_date', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('services.name, SUM(invoices.total_amount) as revenue')
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return [
            'daily_trend' => $dailyRevenue,
            'by_service' => $revenueByService,
        ];
    }

    /**
     * Get Orders Data for detailed page
     */
    private function getOrdersData($dateRange)
    {
        $totalOrders = RepairOrder::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
        $completedOrders = RepairOrder::whereIn('status', ['completed', 'delivered'])
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        $ordersByStatus = RepairOrder::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        $averageOrderValue = Invoice::where('status', 'paid')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->avg('total_amount') ?? 0;

        return [
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'completion_rate' => $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0,
            'orders_by_status' => $ordersByStatus,
            'average_order_value' => $averageOrderValue,
        ];
    }

    /**
     * Get Orders Charts Data
     */
    private function getOrdersCharts($dateRange)
    {
        // Daily orders trend
        $dailyOrders = RepairOrder::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'daily_trend' => $dailyOrders,
        ];
    }

    /**
     * Get Customers Data for detailed page
     */
    private function getCustomersData($dateRange)
    {
        $totalCustomers = Customer::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
        $newCustomers = Customer::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();

        $topCustomers = DB::table('customers')
            ->join('repair_orders', 'customers.id', '=', 'repair_orders.customer_id')
            ->join('invoices', 'repair_orders.id', '=', 'invoices.repair_order_id')
            ->where('invoices.status', 'paid')
            ->whereBetween('invoices.paid_date', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('customers.id, CONCAT(customers.first_name, " ", customers.last_name) as name, customers.phone, SUM(invoices.total_amount) as revenue, COUNT(repair_orders.id) as orders')
            ->groupBy('customers.id', 'customers.first_name', 'customers.last_name', 'customers.phone')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return [
            'total_customers' => $totalCustomers,
            'new_customers' => $newCustomers,
            'top_customers' => $topCustomers,
            'customer_retention' => $this->getCustomerRetention($dateRange),
        ];
    }

    /**
     * Get Customers Charts Data
     */
    private function getCustomersCharts($dateRange)
    {
        // Daily customer growth
        $dailyCustomers = Customer::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'daily_growth' => $dailyCustomers,
        ];
    }

    /**
     * Get Services Data for detailed page
     */
    private function getServicesData($dateRange)
    {
        $totalServices = Service::count();
        $activeServices = DB::table('repair_orders')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->distinct('service_id')
            ->count();

        $servicePerformance = DB::table('services')
            ->leftJoin('repair_orders', 'services.id', '=', 'repair_orders.service_id')
            ->leftJoin('invoices', 'repair_orders.id', '=', 'invoices.repair_order_id')
            ->whereBetween('repair_orders.created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('services.name, services.base_price as price, COUNT(repair_orders.id) as orders, SUM(CASE WHEN invoices.status = "paid" THEN invoices.total_amount ELSE 0 END) as revenue')
            ->groupBy('services.id', 'services.name', 'services.base_price')
            ->orderByDesc('revenue')
            ->get();

        return [
            'total_services' => $totalServices,
            'active_services' => $activeServices,
            'service_performance' => $servicePerformance,
        ];
    }

    /**
     * Get Services Charts Data
     */
    private function getServicesCharts($dateRange)
    {
        // Service popularity
        $servicePopularity = DB::table('repair_orders')
            ->join('services', 'repair_orders.service_id', '=', 'services.id')
            ->whereBetween('repair_orders.created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('services.name, COUNT(*) as count')
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return [
            'popularity' => $servicePopularity,
        ];
    }

    /**
     * Get customer retention rate
     */
    private function getCustomerRetention($dateRange)
    {
        $repeatCustomers = DB::table('customers')
            ->join('repair_orders', 'customers.id', '=', 'repair_orders.customer_id')
            ->whereBetween('repair_orders.created_at', [$dateRange['start'], $dateRange['end']])
            ->groupBy('customers.id')
            ->havingRaw('COUNT(repair_orders.id) > 1')
            ->count();

        $totalCustomers = Customer::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();

        return $totalCustomers > 0 ? ($repeatCustomers / $totalCustomers) * 100 : 0;
    }

    /**
     * Get Overview Metrics for Dashboard
     */
    private function getOverviewMetrics($dateRange)
    {
        $totalRevenue = Invoice::where('status', 'paid')
            ->whereBetween('paid_date', [$dateRange['start'], $dateRange['end']])
            ->sum('total_amount');

        $totalOrders = RepairOrder::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
        $completedOrders = RepairOrder::whereIn('status', ['completed', 'delivered'])
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        $totalCustomers = Customer::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
        $activeRepairs = RepairOrder::whereIn('status', ['pending', 'in_progress', 'waiting_parts'])->count();

        $totalPartsCost = DB::table('repair_order_parts')
            ->join('repair_orders', 'repair_order_parts.repair_order_id', '=', 'repair_orders.id')
            ->whereBetween('repair_orders.created_at', [$dateRange['start'], $dateRange['end']])
            ->sum('repair_order_parts.total_price');

        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $completionRate = $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'total_customers' => $totalCustomers,
            'active_repairs' => $activeRepairs,
            'total_parts_cost' => $totalPartsCost,
            'average_order_value' => $averageOrderValue,
            'completion_rate' => $completionRate,
            'profit_margin' => $totalRevenue > 0 ? (($totalRevenue - $totalPartsCost) / $totalRevenue) * 100 : 0,
        ];
    }

    /**
     * Get Customer Metrics
     */
    private function getCustomerMetrics($dateRange)
    {
        $newCustomers = Customer::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();

        $topCustomers = DB::table('customers')
            ->join('repair_orders', 'customers.id', '=', 'repair_orders.customer_id')
            ->join('invoices', 'repair_orders.id', '=', 'invoices.repair_order_id')
            ->where('invoices.status', 'paid')
            ->whereBetween('invoices.paid_date', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('customers.id, CONCAT(customers.first_name, " ", customers.last_name) as name,
                        customers.phone, SUM(invoices.total_amount) as total_spent,
                        COUNT(repair_orders.id) as total_orders')
            ->groupBy('customers.id', 'customers.first_name', 'customers.last_name', 'customers.phone')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        $customerRetention = $this->getCustomerRetention($dateRange);

        return [
            'new_customers' => $newCustomers,
            'top_customers' => $topCustomers,
            'customer_retention' => $customerRetention,
        ];
    }

    /**
     * Get Sales Metrics
     */
    private function getSalesMetrics($dateRange)
    {
        $totalSales = Invoice::where('status', 'paid')
            ->whereBetween('paid_date', [$dateRange['start'], $dateRange['end']])
            ->sum('total_amount');

        $pendingInvoices = Invoice::where('status', 'pending')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->sum('total_amount');

        $partiallyPaidInvoices = Invoice::where('status', 'partially_paid')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->sum('balance_due');

        $paymentMethods = InvoicePayment::whereBetween('payment_date', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();

        $dailySales = Invoice::where('status', 'paid')
            ->whereBetween('paid_date', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('DATE(paid_date) as date, SUM(total_amount) as sales')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'total_sales' => $totalSales,
            'pending_amount' => $pendingInvoices,
            'partially_paid_amount' => $partiallyPaidInvoices,
            'payment_methods' => $paymentMethods,
            'daily_sales' => $dailySales,
        ];
    }

    /**
     * Get Repair Metrics
     */
    private function getRepairMetrics($dateRange)
    {
        $repairsByStatus = RepairOrder::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        $repairsByDeviceType = DB::table('repair_orders')
            ->join('devices', 'repair_orders.device_id', '=', 'devices.id')
            ->join('device_types', 'devices.device_type_id', '=', 'device_types.id')
            ->whereBetween('repair_orders.created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('device_types.name, COUNT(*) as count')
            ->groupBy('device_types.id', 'device_types.name')
            ->orderByDesc('count')
            ->get();

        $averageRepairTime = RepairOrder::whereNotNull('actual_completion')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, actual_completion)) as avg_hours')
            ->value('avg_hours') ?? 0;

        $commonIssues = RepairOrder::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('issue_description, COUNT(*) as count')
            ->groupBy('issue_description')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return [
            'repairs_by_status' => $repairsByStatus,
            'repairs_by_device_type' => $repairsByDeviceType,
            'average_repair_time' => $averageRepairTime,
            'common_issues' => $commonIssues,
        ];
    }

    /**
     * Get Parts Metrics
     */
    private function getPartsMetrics($dateRange)
    {
        $totalPartsValue = Part::where('is_active', true)->sum(DB::raw('quantity_in_stock * cost_price'));
        $lowStockParts = Part::where('is_active', true)
            ->whereColumn('quantity_in_stock', '<=', 'minimum_stock_level')
            ->count();

        $partsUsage = DB::table('repair_order_parts')
            ->join('parts', 'repair_order_parts.part_id', '=', 'parts.id')
            ->join('repair_orders', 'repair_order_parts.repair_order_id', '=', 'repair_orders.id')
            ->whereBetween('repair_orders.created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('parts.name, SUM(repair_order_parts.quantity_used) as total_used,
                        SUM(repair_order_parts.total_price) as total_cost')
            ->groupBy('parts.id', 'parts.name')
            ->orderByDesc('total_used')
            ->limit(10)
            ->get();

        $partsCostTrend = DB::table('repair_order_parts')
            ->join('repair_orders', 'repair_order_parts.repair_order_id', '=', 'repair_orders.id')
            ->whereBetween('repair_orders.created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('DATE(repair_orders.created_at) as date, SUM(repair_order_parts.total_price) as cost')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'total_inventory_value' => $totalPartsValue,
            'low_stock_count' => $lowStockParts,
            'top_used_parts' => $partsUsage,
            'parts_cost_trend' => $partsCostTrend,
        ];
    }

    /**
     * Get Technician Metrics
     */
    private function getTechnicianMetrics($dateRange)
    {
        $technicianPerformance = DB::table('technicians')
            ->join('users', 'technicians.user_id', '=', 'users.id')
            ->leftJoin('repair_orders', 'technicians.id', '=', 'repair_orders.technician_id')
            ->where('technicians.is_active', true)
            ->whereBetween('repair_orders.created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('users.name, technicians.specialization,
                        COUNT(repair_orders.id) as total_repairs,
                        SUM(CASE WHEN repair_orders.status IN ("completed", "delivered") THEN 1 ELSE 0 END) as completed_repairs,
                        AVG(CASE WHEN repair_orders.actual_completion IS NOT NULL
                            THEN TIMESTAMPDIFF(HOUR, repair_orders.created_at, repair_orders.actual_completion)
                            ELSE NULL END) as avg_completion_time')
            ->groupBy('technicians.id', 'users.name', 'technicians.specialization')
            ->orderByDesc('completed_repairs')
            ->get();

        $workloadDistribution = DB::table('repair_orders')
            ->join('technicians', 'repair_orders.technician_id', '=', 'technicians.id')
            ->join('users', 'technicians.user_id', '=', 'users.id')
            ->whereIn('repair_orders.status', ['pending', 'in_progress', 'waiting_parts'])
            ->selectRaw('users.name, COUNT(*) as active_repairs')
            ->groupBy('technicians.id', 'users.name')
            ->orderByDesc('active_repairs')
            ->get();

        return [
            'technician_performance' => $technicianPerformance,
            'workload_distribution' => $workloadDistribution,
        ];
    }

    /**
     * Get Customer Charts
     */
    private function getCustomerCharts($dateRange)
    {
        $customerGrowth = Customer::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $customersByCity = Customer::selectRaw('city, COUNT(*) as count')
            ->groupBy('city')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return [
            'customer_growth' => $customerGrowth,
            'customers_by_city' => $customersByCity,
        ];
    }

    /**
     * Get Sales Charts
     */
    private function getSalesCharts($dateRange)
    {
        $salesTrend = Invoice::where('status', 'paid')
            ->whereBetween('paid_date', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('DATE(paid_date) as date, SUM(total_amount) as sales, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $paymentMethodsChart = InvoicePayment::whereBetween('payment_date', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get();

        return [
            'sales_trend' => $salesTrend,
            'payment_methods' => $paymentMethodsChart,
        ];
    }

    /**
     * Get Parts Charts
     */
    private function getPartsCharts($dateRange)
    {
        $partsUsageTrend = DB::table('repair_order_parts')
            ->join('repair_orders', 'repair_order_parts.repair_order_id', '=', 'repair_orders.id')
            ->whereBetween('repair_orders.created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('DATE(repair_orders.created_at) as date, SUM(repair_order_parts.quantity_used) as quantity')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $partsByCategory = Part::where('is_active', true)
            ->selectRaw('category, COUNT(*) as count, SUM(quantity_in_stock * cost_price) as value')
            ->groupBy('category')
            ->orderByDesc('value')
            ->get();

        return [
            'parts_usage_trend' => $partsUsageTrend,
            'parts_by_category' => $partsByCategory,
        ];
    }

    /**
     * Get Repair Charts
     */
    private function getRepairCharts($dateRange)
    {
        $repairsTrend = RepairOrder::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $repairsByPriority = RepairOrder::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->get();

        return [
            'repairs_trend' => $repairsTrend,
            'repairs_by_priority' => $repairsByPriority,
        ];
    }

    /**
     * Get Technician Charts
     */
    private function getTechnicianCharts($dateRange)
    {
        $technicianProductivity = DB::table('technicians')
            ->join('users', 'technicians.user_id', '=', 'users.id')
            ->leftJoin('repair_orders', 'technicians.id', '=', 'repair_orders.technician_id')
            ->whereBetween('repair_orders.created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('users.name, DATE(repair_orders.created_at) as date, COUNT(repair_orders.id) as repairs')
            ->groupBy('technicians.id', 'users.name', 'date')
            ->orderBy('date')
            ->get();

        $specializationDistribution = Technician::where('is_active', true)
            ->selectRaw('specialization, COUNT(*) as count')
            ->groupBy('specialization')
            ->get();

        return [
            'technician_productivity' => $technicianProductivity,
            'specialization_distribution' => $specializationDistribution,
        ];
    }

}
