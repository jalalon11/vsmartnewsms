<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;

class CheckRegistrationEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Setting::isRegistrationEnabled()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Registration is currently disabled.'
                ], 403);
            }
            
            return redirect()->route('registration.blocked');
        }
        
        return $next($request);
    }
}
