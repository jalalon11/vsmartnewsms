<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeviceTypeServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Device Types (Categories)
        $iosDevice = \App\Models\DeviceType::create([
            'name' => 'iOS Device',
            'category' => 'Mobile',
            'description' => 'Apple iOS devices like iPhone, iPad',
            'is_active' => true,
        ]);

        $androidDevice = \App\Models\DeviceType::create([
            'name' => 'Android Device',
            'category' => 'Mobile',
            'description' => 'Android smartphones and tablets',
            'is_active' => true,
        ]);

        $laptop = \App\Models\DeviceType::create([
            'name' => 'Laptop',
            'category' => 'Computer',
            'description' => 'Laptop computers',
            'is_active' => true,
        ]);

        $printer = \App\Models\DeviceType::create([
            'name' => 'Printer',
            'category' => 'Peripheral',
            'description' => 'Printers and printing devices',
            'is_active' => true,
        ]);

        // Create Services for iOS Devices
        \App\Models\Service::create([
            'name' => 'Screen Replacement',
            'description' => 'Replace cracked or damaged screen',
            'category' => 'Hardware Repair',
            'device_type_id' => $iosDevice->id,
            'base_price' => 3500.00,
            'estimated_duration' => 60,
            'is_active' => true,
        ]);

        \App\Models\Service::create([
            'name' => 'Battery Replacement',
            'description' => 'Replace old or faulty battery',
            'category' => 'Hardware Repair',
            'device_type_id' => $iosDevice->id,
            'base_price' => 2500.00,
            'estimated_duration' => 45,
            'is_active' => true,
        ]);

        \App\Models\Service::create([
            'name' => 'Water Damage Repair',
            'description' => 'Repair water damaged iOS device',
            'category' => 'Hardware Repair',
            'device_type_id' => $iosDevice->id,
            'base_price' => 4500.00,
            'estimated_duration' => 120,
            'is_active' => true,
        ]);

        // Create Services for Android Devices
        \App\Models\Service::create([
            'name' => 'Screen Replacement',
            'description' => 'Replace cracked or damaged screen',
            'category' => 'Hardware Repair',
            'device_type_id' => $androidDevice->id,
            'base_price' => 2800.00,
            'estimated_duration' => 60,
            'is_active' => true,
        ]);

        \App\Models\Service::create([
            'name' => 'Battery Replacement',
            'description' => 'Replace old or faulty battery',
            'category' => 'Hardware Repair',
            'device_type_id' => $androidDevice->id,
            'base_price' => 2000.00,
            'estimated_duration' => 45,
            'is_active' => true,
        ]);

        \App\Models\Service::create([
            'name' => 'Charging Port Repair',
            'description' => 'Fix charging port issues',
            'category' => 'Hardware Repair',
            'device_type_id' => $androidDevice->id,
            'base_price' => 1800.00,
            'estimated_duration' => 90,
            'is_active' => true,
        ]);

        // Create Services for Laptops
        \App\Models\Service::create([
            'name' => 'Keyboard Replacement',
            'description' => 'Replace faulty keyboard',
            'category' => 'Hardware Repair',
            'device_type_id' => $laptop->id,
            'base_price' => 3000.00,
            'estimated_duration' => 90,
            'is_active' => true,
        ]);

        \App\Models\Service::create([
            'name' => 'Hard Drive Replacement',
            'description' => 'Replace faulty hard drive',
            'category' => 'Hardware Repair',
            'device_type_id' => $laptop->id,
            'base_price' => 5000.00,
            'estimated_duration' => 120,
            'is_active' => true,
        ]);

        \App\Models\Service::create([
            'name' => 'RAM Upgrade',
            'description' => 'Upgrade system memory',
            'category' => 'Hardware Upgrade',
            'device_type_id' => $laptop->id,
            'base_price' => 4000.00,
            'estimated_duration' => 60,
            'is_active' => true,
        ]);

        // Create Services for Printers
        \App\Models\Service::create([
            'name' => 'Ink Replacement',
            'description' => 'Replace printer ink cartridges',
            'category' => 'Maintenance',
            'device_type_id' => $printer->id,
            'base_price' => 800.00,
            'estimated_duration' => 15,
            'is_active' => true,
        ]);

        \App\Models\Service::create([
            'name' => 'Print Head Cleaning',
            'description' => 'Clean clogged print heads',
            'category' => 'Maintenance',
            'device_type_id' => $printer->id,
            'base_price' => 500.00,
            'estimated_duration' => 30,
            'is_active' => true,
        ]);

        \App\Models\Service::create([
            'name' => 'Paper Jam Repair',
            'description' => 'Fix paper jam issues',
            'category' => 'Hardware Repair',
            'device_type_id' => $printer->id,
            'base_price' => 600.00,
            'estimated_duration' => 45,
            'is_active' => true,
        ]);
    }
}
