<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Configure API-only authentication - no redirects to login routes
        $middleware->redirectGuestsTo(function (Request $request) {
            // For API requests, return null to prevent redirects
            if ($request->is('api/*')) {
                return null;
            }
            // For non-API requests, still return null since we're API-only
            return null;
        });
    })
    ->withProviders([
        // Ensure Filesystem service is loaded before Sanctum
        \Illuminate\Filesystem\FilesystemServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
