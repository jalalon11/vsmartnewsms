<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user has the required role
        if ($user->role !== $role) {
            // Redirect based on user's actual role
            if ($user->role === 'technician') {
                return redirect()->route('dashboard')->with('error', 'Access denied. You do not have permission to access this area.');
            } elseif ($user->role === 'admin') {
                return redirect()->route('dashboard')->with('error', 'Access denied. You do not have permission to access this area.');
            }
            
            // Default redirect for unknown roles
            return redirect()->route('login')->with('error', 'Invalid user role.');
        }

        return $next($request);
    }
}
