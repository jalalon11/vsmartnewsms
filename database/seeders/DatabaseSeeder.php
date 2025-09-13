<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@hardware-repair.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create technician user
        $techUser = User::create([
            'name' => 'John Technician',
            'email' => 'tech@hardware-repair.com',
            'password' => bcrypt('password'),
            'role' => 'technician',
            'is_active' => true,
        ]);

        // Create receptionist user
        $receptionist = User::create([
            'name' => 'Jane Receptionist',
            'email' => 'reception@hardware-repair.com',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        // Create device types
        $deviceTypes = [
            ['name' => 'Smartphone', 'category' => 'Mobile', 'description' => 'Mobile phones and smartphones'],
            ['name' => 'Laptop', 'category' => 'Computer', 'description' => 'Laptop computers'],
            ['name' => 'Desktop', 'category' => 'Computer', 'description' => 'Desktop computers'],
            ['name' => 'Printer', 'category' => 'Peripheral', 'description' => 'Printers and scanners'],
            ['name' => 'Tablet', 'category' => 'Mobile', 'description' => 'Tablets and iPads'],
        ];

        foreach ($deviceTypes as $type) {
            \App\Models\DeviceType::create($type);
        }

        // Create services
        $services = [
            ['name' => 'Screen Repair', 'description' => 'Replace broken or cracked screens', 'base_price' => 150.00, 'estimated_duration' => 120, 'category' => 'Hardware'],
            ['name' => 'Battery Replacement', 'description' => 'Replace old or faulty batteries', 'base_price' => 80.00, 'estimated_duration' => 60, 'category' => 'Hardware'],
            ['name' => 'Water Damage Repair', 'description' => 'Repair devices damaged by water', 'base_price' => 200.00, 'estimated_duration' => 240, 'category' => 'Hardware'],
            ['name' => 'Software Installation', 'description' => 'Install operating system and software', 'base_price' => 100.00, 'estimated_duration' => 180, 'category' => 'Software'],
            ['name' => 'Virus Removal', 'description' => 'Remove malware and viruses', 'base_price' => 75.00, 'estimated_duration' => 90, 'category' => 'Software'],
            ['name' => 'Data Recovery', 'description' => 'Recover lost or corrupted data', 'base_price' => 250.00, 'estimated_duration' => 300, 'category' => 'Data'],
            ['name' => 'Diagnostic', 'description' => 'Diagnose hardware and software issues', 'base_price' => 50.00, 'estimated_duration' => 45, 'category' => 'Diagnostic'],
        ];

        foreach ($services as $service) {
            \App\Models\Service::create($service);
        }

        // Create technician profile
        \App\Models\Technician::create([
            'user_id' => $techUser->id,
            'employee_id' => 'TECH001',
            'specialization' => 'Mobile Devices',
            'skills' => ['Screen Repair', 'Battery Replacement', 'Water Damage'],
            'hourly_rate' => 45.00,
            'is_active' => true,
        ]);

        // Create parts
        $parts = [
            ['part_number' => 'SCR-IP14-BLK', 'name' => 'iPhone 14 Screen Black', 'description' => 'Replacement screen for iPhone 14', 'category' => 'Screen', 'cost_price' => 120.00, 'selling_price' => 180.00, 'quantity_in_stock' => 10],
            ['part_number' => 'BAT-IP14', 'name' => 'iPhone 14 Battery', 'description' => 'Replacement battery for iPhone 14', 'category' => 'Battery', 'cost_price' => 40.00, 'selling_price' => 60.00, 'quantity_in_stock' => 15],
            ['part_number' => 'SCR-SAM-S23', 'name' => 'Samsung S23 Screen', 'description' => 'Replacement screen for Samsung Galaxy S23', 'category' => 'Screen', 'cost_price' => 100.00, 'selling_price' => 150.00, 'quantity_in_stock' => 8],
            ['part_number' => 'CHG-USB-C', 'name' => 'USB-C Charger', 'description' => 'Universal USB-C charging cable', 'category' => 'Charger', 'cost_price' => 15.00, 'selling_price' => 25.00, 'quantity_in_stock' => 25],
        ];

        foreach ($parts as $part) {
            \App\Models\Part::create($part);
        }
    }
}
