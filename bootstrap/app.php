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
        $middleware->redirectTo(
            function ($request) {
                if ($request->is('backend/*') || $request->is('backend') || $request->is('em-secure-portal/*')) {
                    return route('login');
                }
                return route('customer.login');
            }
        );

        $middleware->redirectUsersTo(
            function ($request) {
                if (\Illuminate\Support\Facades\Auth::guard('web')->check()) {
                    return route('dashboard');
                }
                if (\Illuminate\Support\Facades\Auth::guard('customer')->check()) {
                    return route('customer.dashboard');
                }

                if ($request->is('backend/*') || $request->is('backend') || $request->is('em-secure-portal/*')) {
                    return route('dashboard');
                }
                return route('customer.dashboard');
            }
        );

        $middleware->validateCsrfTokens(except: [
            '/rakbank/webhook',
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'maintenance' => \App\Http\Middleware\CheckMaintenanceMode::class,
            'two-factor' => \App\Http\Middleware\TwoFactorMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
