<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TechnicianAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user is a technician
        if ($user->role !== 'technician') {
            return redirect()->route('dashboard')->with('error', 'Access denied. This area is restricted to technicians only.');
        }

        // Check if technician profile exists and is active
        if (!$user->technician) {
            return redirect()->route('login')->with('error', 'Technician profile not found. Please contact administrator.');
        }

        if (!$user->technician->is_active) {
            return redirect()->route('login')->with('error', 'Your technician account has been deactivated. Please contact administrator.');
        }

        return $next($request);
    }
}
