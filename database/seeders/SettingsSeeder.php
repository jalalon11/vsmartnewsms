<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default registration_enabled setting
        Setting::firstOrCreate(
            ['key' => 'registration_enabled'],
            [
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable or disable user registration'
            ]
        );
    }
}
