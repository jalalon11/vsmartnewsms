<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Screen Repair', 'description' => 'Display and touchscreen repairs', 'sort_order' => 1],
            ['name' => 'Battery Replacement', 'description' => 'Battery replacement and power issues', 'sort_order' => 2],
            ['name' => 'Water Damage', 'description' => 'Water damage assessment and repair', 'sort_order' => 3],
            ['name' => 'Software Issues', 'description' => 'Operating system and software problems', 'sort_order' => 4],
            ['name' => 'Hardware Repair', 'description' => 'Internal component repairs', 'sort_order' => 5],
            ['name' => 'Camera Repair', 'description' => 'Camera and lens repairs', 'sort_order' => 6],
            ['name' => 'Audio Issues', 'description' => 'Speaker and microphone repairs', 'sort_order' => 7],
            ['name' => 'Charging Port', 'description' => 'Charging port and cable issues', 'sort_order' => 8],
            ['name' => 'Button Repair', 'description' => 'Physical button repairs', 'sort_order' => 9],
            ['name' => 'Data Recovery', 'description' => 'Data recovery and backup services', 'sort_order' => 10],
            ['name' => 'Diagnostic', 'description' => 'Device diagnostic and assessment', 'sort_order' => 11],
            ['name' => 'Other', 'description' => 'Other repair services', 'sort_order' => 99],
        ];

        foreach ($categories as $category) {
            ServiceCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
