<?php

namespace App\Http\Controllers;

use App\Models\DeviceType;
use Illuminate\Http\Request;

class DeviceTypeController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        DeviceType::create($validated);

        // Always redirect back with success message for Inertia requests
        return back()->with('success', 'Device category created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeviceType $deviceType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $deviceType->update($validated);

        // Always redirect back with success message for Inertia requests
        return back()->with('success', 'Device category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeviceType $deviceType)
    {
        // Check if device type has associated devices
        if ($deviceType->devices()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete device category that has associated devices.']);
        }

        // Check if device type has associated services
        if ($deviceType->services()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete device category that has associated services.']);
        }

        $deviceType->delete();

        // Always redirect back with success message for Inertia requests
        return back()->with('success', 'Device category deleted successfully.');
    }
}
