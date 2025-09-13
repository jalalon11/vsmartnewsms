<?php

namespace App\Services;

use App\Models\Part;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryAnalyticsService
{
    /**
     * Get comprehensive inventory overview.
     */
    public function getInventoryOverview()
    {
        $totalParts = Part::count();
        $activeParts = Part::where('is_active', true)->count();
        $lowStockParts = Part::whereRaw('quantity_in_stock <= minimum_stock_level')->count();
        $outOfStockParts = Part::where('quantity_in_stock', 0)->count();
        
        // Calculate total inventory value
        $totalCostValue = Part::sum(DB::raw('quantity_in_stock * cost_price'));
        $totalSellingValue = Part::sum(DB::raw('quantity_in_stock * selling_price'));
        $potentialProfit = $totalSellingValue - $totalCostValue;

        return [
            'total_parts' => $totalParts,
            'active_parts' => $activeParts,
            'low_stock_parts' => $lowStockParts,
            'out_of_stock_parts' => $outOfStockParts,
            'low_stock_percentage' => $totalParts > 0 ? ($lowStockParts / $totalParts) * 100 : 0,
            'total_cost_value' => $totalCostValue,
            'total_selling_value' => $totalSellingValue,
            'potential_profit' => $potentialProfit,
            'potential_margin' => $totalSellingValue > 0 ? ($potentialProfit / $totalSellingValue) * 100 : 0,
        ];
    }

    /**
     * Get inventory by category analysis.
     */
    public function getInventoryByCategory()
    {
        return Part::select(
                'category',
                DB::raw('COUNT(*) as total_parts'),
                DB::raw('SUM(quantity_in_stock) as total_quantity'),
                DB::raw('SUM(quantity_in_stock * cost_price) as total_cost_value'),
                DB::raw('SUM(quantity_in_stock * selling_price) as total_selling_value'),
                DB::raw('SUM(CASE WHEN quantity_in_stock = 0 THEN 1 ELSE 0 END) as out_of_stock_count'),
                DB::raw('SUM(CASE WHEN quantity_in_stock <= minimum_stock_level THEN 1 ELSE 0 END) as low_stock_count'),
                DB::raw('AVG(selling_price - cost_price) as avg_profit_per_unit'),
                DB::raw('AVG((selling_price - cost_price) / selling_price * 100) as avg_margin_percentage')
            )
            ->groupBy('category')
            ->orderBy('total_selling_value', 'desc')
            ->get();
    }

    /**
     * Get parts turnover analysis.
     */
    public function getPartsTurnoverAnalysis($startDate, $endDate)
    {
        return DB::table('parts')
            ->leftJoin('repair_order_parts', 'parts.id', '=', 'repair_order_parts.part_id')
            ->leftJoin('repair_orders', function($join) use ($startDate, $endDate) {
                $join->on('repair_order_parts.repair_order_id', '=', 'repair_orders.id')
                     ->whereBetween('repair_orders.created_at', [$startDate, $endDate]);
            })
            ->select(
                'parts.id',
                'parts.name',
                'parts.part_number',
                'parts.category',
                'parts.quantity_in_stock',
                'parts.minimum_stock_level',
                'parts.cost_price',
                'parts.selling_price',
                DB::raw('COALESCE(SUM(repair_order_parts.quantity_used), 0) as total_used'),
                DB::raw('COALESCE(SUM(repair_order_parts.total_price), 0) as total_revenue'),
                DB::raw('COALESCE(SUM(repair_order_parts.quantity_used * parts.cost_price), 0) as total_cost'),
                DB::raw('CASE 
                    WHEN parts.quantity_in_stock > 0 AND SUM(repair_order_parts.quantity_used) > 0 
                    THEN SUM(repair_order_parts.quantity_used) / parts.quantity_in_stock 
                    ELSE 0 
                END as turnover_ratio'),
                DB::raw('CASE 
                    WHEN SUM(repair_order_parts.quantity_used) > 0 
                    THEN DATEDIFF(?, ?) / (SUM(repair_order_parts.quantity_used) / GREATEST(parts.quantity_in_stock, 1))
                    ELSE NULL 
                END as days_of_supply')
            )
            ->addBinding([$endDate, $startDate], 'select')
            ->groupBy('parts.id', 'parts.name', 'parts.part_number', 'parts.category', 
                     'parts.quantity_in_stock', 'parts.minimum_stock_level', 
                     'parts.cost_price', 'parts.selling_price')
            ->orderBy('turnover_ratio', 'desc')
            ->get();
    }

    /**
     * Get slow-moving inventory.
     */
    public function getSlowMovingInventory($days = 90)
    {
        $cutoffDate = Carbon::now()->subDays($days);

        return Part::select(
                'parts.*',
                DB::raw('COALESCE(SUM(repair_order_parts.quantity_used), 0) as usage_count'),
                DB::raw('MAX(repair_orders.created_at) as last_used_date'),
                DB::raw('parts.quantity_in_stock * parts.cost_price as tied_up_capital')
            )
            ->leftJoin('repair_order_parts', 'parts.id', '=', 'repair_order_parts.part_id')
            ->leftJoin('repair_orders', function($join) use ($cutoffDate) {
                $join->on('repair_order_parts.repair_order_id', '=', 'repair_orders.id')
                     ->where('repair_orders.created_at', '>=', $cutoffDate);
            })
            ->where('parts.quantity_in_stock', '>', 0)
            ->groupBy('parts.id')
            ->havingRaw('COALESCE(SUM(repair_order_parts.quantity_used), 0) = 0')
            ->orderBy('tied_up_capital', 'desc')
            ->get();
    }

    /**
     * Get fast-moving inventory.
     */
    public function getFastMovingInventory($days = 30, $limit = 20)
    {
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        return DB::table('repair_order_parts')
            ->join('parts', 'repair_order_parts.part_id', '=', 'parts.id')
            ->join('repair_orders', 'repair_order_parts.repair_order_id', '=', 'repair_orders.id')
            ->whereBetween('repair_orders.created_at', [$startDate, $endDate])
            ->select(
                'parts.name',
                'parts.part_number',
                'parts.category',
                'parts.quantity_in_stock',
                'parts.minimum_stock_level',
                DB::raw('SUM(repair_order_parts.quantity_used) as total_used'),
                DB::raw('SUM(repair_order_parts.total_price) as total_revenue'),
                DB::raw('SUM(repair_order_parts.quantity_used) / ? as daily_usage_rate') 
            )
            ->addBinding([$days], 'select')
            ->groupBy('parts.id', 'parts.name', 'parts.part_number', 'parts.category', 
                     'parts.quantity_in_stock', 'parts.minimum_stock_level')
            ->orderBy('total_used', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get reorder recommendations.
     */
    public function getReorderRecommendations($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        return DB::table('parts')
            ->leftJoin('repair_order_parts', 'parts.id', '=', 'repair_order_parts.part_id')
            ->leftJoin('repair_orders', function($join) use ($startDate, $endDate) {
                $join->on('repair_order_parts.repair_order_id', '=', 'repair_orders.id')
                     ->whereBetween('repair_orders.created_at', [$startDate, $endDate]);
            })
            ->select(
                'parts.id',
                'parts.name',
                'parts.part_number',
                'parts.category',
                'parts.quantity_in_stock',
                'parts.minimum_stock_level',
                'parts.cost_price',
                'parts.supplier',
                DB::raw('COALESCE(SUM(repair_order_parts.quantity_used), 0) as usage_in_period'),
                DB::raw('COALESCE(SUM(repair_order_parts.quantity_used), 0) / ? as daily_usage_rate'),
                DB::raw('CASE 
                    WHEN COALESCE(SUM(repair_order_parts.quantity_used), 0) > 0 
                    THEN parts.quantity_in_stock / (COALESCE(SUM(repair_order_parts.quantity_used), 0) / ?)
                    ELSE 999 
                END as days_of_stock_remaining'),
                DB::raw('GREATEST(parts.minimum_stock_level - parts.quantity_in_stock, 0) as reorder_quantity')
            )
            ->addBinding([$days, $days], 'select')
            ->groupBy('parts.id', 'parts.name', 'parts.part_number', 'parts.category', 
                     'parts.quantity_in_stock', 'parts.minimum_stock_level', 
                     'parts.cost_price', 'parts.supplier')
            ->having('days_of_stock_remaining', '<', 14) // Less than 2 weeks of stock
            ->orHaving('reorder_quantity', '>', 0) // Below minimum stock level
            ->orderBy('days_of_stock_remaining', 'asc')
            ->get();
    }

    /**
     * Get inventory valuation by age.
     */
    public function getInventoryValuationByAge()
    {
        // This is a simplified version - in a real system, you'd track when parts were received
        return Part::select(
                'category',
                DB::raw('SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN quantity_in_stock * cost_price ELSE 0 END) as value_0_30_days'),
                DB::raw('SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY) AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY) THEN quantity_in_stock * cost_price ELSE 0 END) as value_30_90_days'),
                DB::raw('SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 180 DAY) AND created_at < DATE_SUB(NOW(), INTERVAL 90 DAY) THEN quantity_in_stock * cost_price ELSE 0 END) as value_90_180_days'),
                DB::raw('SUM(CASE WHEN created_at < DATE_SUB(NOW(), INTERVAL 180 DAY) THEN quantity_in_stock * cost_price ELSE 0 END) as value_over_180_days'),
                DB::raw('SUM(quantity_in_stock * cost_price) as total_value')
            )
            ->groupBy('category')
            ->get();
    }

    /**
     * Get ABC analysis (Pareto analysis) of parts.
     */
    public function getABCAnalysis($startDate, $endDate)
    {
        // Get parts with their revenue contribution
        $partsRevenue = DB::table('repair_order_parts')
            ->join('parts', 'repair_order_parts.part_id', '=', 'parts.id')
            ->join('repair_orders', 'repair_order_parts.repair_order_id', '=', 'repair_orders.id')
            ->whereBetween('repair_orders.created_at', [$startDate, $endDate])
            ->select(
                'parts.id',
                'parts.name',
                'parts.part_number',
                'parts.category',
                DB::raw('SUM(repair_order_parts.total_price) as total_revenue')
            )
            ->groupBy('parts.id', 'parts.name', 'parts.part_number', 'parts.category')
            ->orderBy('total_revenue', 'desc')
            ->get();

        $totalRevenue = $partsRevenue->sum('total_revenue');
        $cumulativeRevenue = 0;
        
        return $partsRevenue->map(function ($part, $index) use ($totalRevenue, &$cumulativeRevenue) {
            $cumulativeRevenue += $part->total_revenue;
            $cumulativePercentage = ($cumulativeRevenue / $totalRevenue) * 100;
            
            // ABC Classification
            if ($cumulativePercentage <= 80) {
                $classification = 'A';
            } elseif ($cumulativePercentage <= 95) {
                $classification = 'B';
            } else {
                $classification = 'C';
            }
            
            return [
                'id' => $part->id,
                'name' => $part->name,
                'part_number' => $part->part_number,
                'category' => $part->category,
                'total_revenue' => $part->total_revenue,
                'revenue_percentage' => ($part->total_revenue / $totalRevenue) * 100,
                'cumulative_percentage' => $cumulativePercentage,
                'classification' => $classification,
                'rank' => $index + 1,
            ];
        });
    }

    /**
     * Get inventory health score.
     */
    public function getInventoryHealthScore()
    {
        $overview = $this->getInventoryOverview();
        
        // Calculate health score based on various factors
        $stockLevelScore = max(0, 100 - $overview['low_stock_percentage']);
        $profitabilityScore = min(100, $overview['potential_margin']);
        $utilizationScore = 100; // This would need usage data to calculate properly
        
        $overallScore = ($stockLevelScore + $profitabilityScore + $utilizationScore) / 3;
        
        return [
            'overall_score' => round($overallScore, 1),
            'stock_level_score' => round($stockLevelScore, 1),
            'profitability_score' => round($profitabilityScore, 1),
            'utilization_score' => round($utilizationScore, 1),
            'recommendations' => $this->getHealthRecommendations($overview),
        ];
    }

    /**
     * Get health recommendations based on inventory analysis.
     */
    private function getHealthRecommendations($overview)
    {
        $recommendations = [];
        
        if ($overview['low_stock_percentage'] > 20) {
            $recommendations[] = [
                'type' => 'warning',
                'message' => 'High percentage of low-stock items. Consider reviewing reorder points.',
                'action' => 'Review minimum stock levels for frequently used parts.',
            ];
        }
        
        if ($overview['out_of_stock_parts'] > 0) {
            $recommendations[] = [
                'type' => 'critical',
                'message' => $overview['out_of_stock_parts'] . ' parts are out of stock.',
                'action' => 'Reorder out-of-stock items immediately to avoid service delays.',
            ];
        }
        
        if ($overview['potential_margin'] < 30) {
            $recommendations[] = [
                'type' => 'info',
                'message' => 'Low profit margin on inventory. Consider price optimization.',
                'action' => 'Review pricing strategy for parts with low margins.',
            ];
        }
        
        return $recommendations;
    }
}
