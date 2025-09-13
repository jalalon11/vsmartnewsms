<?php

namespace App\Services;

use App\Models\RepairOrder;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Part;
use App\Models\Service;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Calculate revenue metrics for a given period.
     */
    public function calculateRevenueMetrics($startDate, $endDate)
    {
        $invoices = Invoice::where('status', 'paid')
            ->whereBetween('paid_date', [$startDate, $endDate])
            ->get();

        $totalRevenue = $invoices->sum('total_amount');
        $totalOrders = $invoices->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Revenue by payment method
        $paymentMethodBreakdown = $invoices->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_amount'),
                'percentage' => 0, // Will be calculated later
            ];
        });

        // Calculate percentages
        foreach ($paymentMethodBreakdown as $method => $data) {
            $paymentMethodBreakdown[$method]['percentage'] = $totalRevenue > 0 
                ? ($data['total'] / $totalRevenue) * 100 
                : 0;
        }

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'average_order_value' => $averageOrderValue,
            'payment_method_breakdown' => $paymentMethodBreakdown,
        ];
    }

    /**
     * Calculate cost metrics for a given period.
     */
    public function calculateCostMetrics($startDate, $endDate)
    {
        // Parts costs
        $partsCosts = DB::table('repair_order_parts')
            ->join('parts', 'repair_order_parts.part_id', '=', 'parts.id')
            ->join('repair_orders', 'repair_order_parts.repair_order_id', '=', 'repair_orders.id')
            ->whereBetween('repair_orders.created_at', [$startDate, $endDate])
            ->select(
                'parts.category',
                DB::raw('SUM(repair_order_parts.quantity_used * parts.cost_price) as total_cost'),
                DB::raw('SUM(repair_order_parts.total_price) as total_revenue'),
                DB::raw('SUM(repair_order_parts.total_price) - SUM(repair_order_parts.quantity_used * parts.cost_price) as profit')
            )
            ->groupBy('parts.category')
            ->get();

        $totalPartsCost = $partsCosts->sum('total_cost');
        $totalPartsRevenue = $partsCosts->sum('total_revenue');
        $totalPartsProfit = $partsCosts->sum('profit');

        return [
            'total_parts_cost' => $totalPartsCost,
            'total_parts_revenue' => $totalPartsRevenue,
            'total_parts_profit' => $totalPartsProfit,
            'parts_margin' => $totalPartsRevenue > 0 ? ($totalPartsProfit / $totalPartsRevenue) * 100 : 0,
            'parts_by_category' => $partsCosts,
        ];
    }

    /**
     * Calculate profitability metrics.
     */
    public function calculateProfitabilityMetrics($startDate, $endDate)
    {
        $revenueMetrics = $this->calculateRevenueMetrics($startDate, $endDate);
        $costMetrics = $this->calculateCostMetrics($startDate, $endDate);

        // Labor revenue (pure profit since no direct costs)
        $laborRevenue = DB::table('repair_order_services')
            ->join('repair_orders', 'repair_order_services.repair_order_id', '=', 'repair_orders.id')
            ->join('invoices', 'repair_orders.id', '=', 'invoices.repair_order_id')
            ->where('invoices.status', 'paid')
            ->whereBetween('invoices.paid_date', [$startDate, $endDate])
            ->sum('repair_order_services.service_price');

        $totalRevenue = $revenueMetrics['total_revenue'];
        $totalCosts = $costMetrics['total_parts_cost'];
        $grossProfit = $totalRevenue - $totalCosts;
        $grossMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        return [
            'total_revenue' => $totalRevenue,
            'labor_revenue' => $laborRevenue,
            'parts_revenue' => $costMetrics['total_parts_revenue'],
            'total_costs' => $totalCosts,
            'gross_profit' => $grossProfit,
            'gross_margin' => $grossMargin,
            'labor_profit' => $laborRevenue, // Labor is pure profit
            'parts_profit' => $costMetrics['total_parts_profit'],
        ];
    }

    /**
     * Get trending data for charts.
     */
    public function getTrendingData($period = 'daily', $days = 30)
    {
        $data = [];
        $now = Carbon::now();

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            
            $revenue = Invoice::where('status', 'paid')
                ->whereDate('paid_date', $date)
                ->sum('total_amount');

            $orders = Invoice::where('status', 'paid')
                ->whereDate('paid_date', $date)
                ->count();

            $data[] = [
                'date' => $date->format('M d'),
                'full_date' => $date->format('Y-m-d'),
                'revenue' => (float) $revenue,
                'orders' => $orders,
            ];
        }

        return $data;
    }

    /**
     * Get monthly trending data.
     */
    public function getMonthlyTrendingData($months = 12)
    {
        $data = [];
        $now = Carbon::now();

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            
            $revenue = Invoice::where('status', 'paid')
                ->whereYear('paid_date', $date->year)
                ->whereMonth('paid_date', $date->month)
                ->sum('total_amount');

            $orders = Invoice::where('status', 'paid')
                ->whereYear('paid_date', $date->year)
                ->whereMonth('paid_date', $date->month)
                ->count();

            $data[] = [
                'month' => $date->format('M Y'),
                'year' => $date->year,
                'month_num' => $date->month,
                'revenue' => (float) $revenue,
                'orders' => $orders,
            ];
        }

        return $data;
    }

    /**
     * Calculate growth percentage.
     */
    public function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return (($current - $previous) / $previous) * 100;
    }

    /**
     * Format currency for display.
     */
    public function formatCurrency($amount)
    {
        return '₱' . number_format($amount, 2);
    }

    /**
     * Format percentage for display.
     */
    public function formatPercentage($percentage, $decimals = 1)
    {
        $sign = $percentage >= 0 ? '+' : '';
        return $sign . number_format($percentage, $decimals) . '%';
    }

    /**
     * Get top performing items.
     */
    public function getTopPerformers($type = 'services', $limit = 10, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? Carbon::now()->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        switch ($type) {
            case 'services':
                return $this->getTopServices($limit, $startDate, $endDate);
            case 'parts':
                return $this->getTopParts($limit, $startDate, $endDate);
            case 'customers':
                return $this->getTopCustomers($limit, $startDate, $endDate);
            default:
                return collect();
        }
    }

    /**
     * Get top services by revenue.
     */
    private function getTopServices($limit, $startDate, $endDate)
    {
        return DB::table('repair_order_services')
            ->join('services', 'repair_order_services.service_id', '=', 'services.id')
            ->join('repair_orders', 'repair_order_services.repair_order_id', '=', 'repair_orders.id')
            ->whereBetween('repair_orders.created_at', [$startDate, $endDate])
            ->select(
                'services.name',
                'services.base_price',
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(repair_order_services.service_price) as total_revenue'),
                DB::raw('AVG(repair_order_services.service_price) as avg_price')
            )
            ->groupBy('services.id', 'services.name', 'services.base_price')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top parts by revenue.
     */
    private function getTopParts($limit, $startDate, $endDate)
    {
        return DB::table('repair_order_parts')
            ->join('parts', 'repair_order_parts.part_id', '=', 'parts.id')
            ->join('repair_orders', 'repair_order_parts.repair_order_id', '=', 'repair_orders.id')
            ->whereBetween('repair_orders.created_at', [$startDate, $endDate])
            ->select(
                'parts.name',
                'parts.part_number',
                'parts.category',
                DB::raw('SUM(repair_order_parts.quantity_used) as total_used'),
                DB::raw('SUM(repair_order_parts.total_price) as total_revenue'),
                DB::raw('AVG(repair_order_parts.unit_price) as avg_price')
            )
            ->groupBy('parts.id', 'parts.name', 'parts.part_number', 'parts.category')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top customers by spending.
     */
    private function getTopCustomers($limit, $startDate, $endDate)
    {
        return Customer::select(
                'customers.id',
                'customers.first_name',
                'customers.last_name',
                DB::raw('COUNT(invoices.id) as total_orders'),
                DB::raw('SUM(invoices.total_amount) as total_spent'),
                DB::raw('AVG(invoices.total_amount) as avg_order_value')
            )
            ->join('invoices', 'customers.id', '=', 'invoices.customer_id')
            ->where('invoices.status', 'paid')
            ->whereBetween('invoices.paid_date', [$startDate, $endDate])
            ->groupBy('customers.id', 'customers.first_name', 'customers.last_name')
            ->orderBy('total_spent', 'desc')
            ->limit($limit)
            ->get();
    }
}
