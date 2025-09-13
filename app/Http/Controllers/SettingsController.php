<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Inertia\Inertia;

class SettingsController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        $settings = [
            'registration_enabled' => Setting::isRegistrationEnabled(),
        ];

        return Inertia::render('Settings/Index', [
            'settings' => $settings
        ]);
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'registration_enabled' => 'required|boolean',
        ]);

        Setting::set(
            'registration_enabled',
            $request->registration_enabled ? 1 : 0,
            'boolean',
            'Enable or disable user registration'
        );

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
