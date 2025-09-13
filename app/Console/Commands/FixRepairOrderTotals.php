<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RepairOrder;
use App\Models\Service;

class FixRepairOrderTotals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repair-orders:fix-totals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix repair order totals and migrate single services to multiple services system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing repair order totals...');

        $repairOrders = RepairOrder::all();
        $fixed = 0;
        $migrated = 0;

        foreach ($repairOrders as $repairOrder) {
            $needsUpdate = false;

            // Check if repair order has service_id but no services in pivot table
            if ($repairOrder->service_id && $repairOrder->services()->count() == 0) {
                $service = Service::find($repairOrder->service_id);
                if ($service) {
                    $this->info("Migrating repair order {$repairOrder->order_number} to multiple services system...");

                    // Attach the service to the repair order
                    $repairOrder->services()->attach($repairOrder->service_id, [
                        'service_price' => $repairOrder->labor_cost > 0 ? $repairOrder->labor_cost : $service->base_price,
                        'estimated_duration' => $service->estimated_duration,
                        'status' => 'pending',
                        'service_notes' => null,
                    ]);

                    $migrated++;
                    $needsUpdate = true;
                }
            }

            // Check if totals need recalculation
            $calculatedLaborCost = $repairOrder->calculateTotalServiceCost();
            if ($repairOrder->labor_cost != $calculatedLaborCost || $needsUpdate) {
                $this->info("Updating totals for repair order {$repairOrder->order_number}...");
                $repairOrder->updateTotals();
                $fixed++;
            }
        }

        $this->info("Fixed {$fixed} repair orders and migrated {$migrated} to multiple services system.");
        return 0;
    }
}
