<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Add CORS middleware globally
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
        
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);
        
        // Add CORS to web middleware for Inertia.js requests
        $middleware->web(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        // Register custom middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminAccess::class,
            'technician' => \App\Http\Middleware\TechnicianAccess::class,
            'role' => \App\Http\Middleware\EnsureUserRole::class,
            'registration.enabled' => \App\Http\Middleware\CheckRegistrationEnabled::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
