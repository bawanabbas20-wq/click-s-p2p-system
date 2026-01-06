<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\LanguageManager::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
        
        // Apply global rate limiting to all web routes (uses cache driver, not Redis-specific)
        $middleware->alias([
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // In production, render clean error pages without stack traces
        $exceptions->render(function (\Throwable $e, Request $request) {
            if (!config('app.debug')) {
                // Get status code
                $statusCode = $e instanceof HttpException ? $e->getStatusCode() : 500;
                
                // For AJAX requests, return JSON
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => match($statusCode) {
                            403 => 'Access denied.',
                            404 => 'Resource not found.',
                            419 => 'Session expired. Please refresh.',
                            429 => 'Too many requests. Please slow down.',
                            500 => 'Server error. Please try again later.',
                            503 => 'Service temporarily unavailable.',
                            default => 'An error occurred.',
                        },
                    ], $statusCode);
                }
                
                // For web requests, show error view if it exists
                $view = "errors.{$statusCode}";
                if (view()->exists($view)) {
                    return response()->view($view, [], $statusCode);
                }
                
                // Fallback to 500 for unknown errors
                if ($statusCode >= 500 && view()->exists('errors.500')) {
                    return response()->view('errors.500', [], $statusCode);
                }
            }
        });
    })->create();

