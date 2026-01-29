<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Skip check for Admin panel routes
        if ($request->is('backend*') || $request->is('login') || $request->is('logout')) {
            return $next($request);
        }

        // 2. Skip check for authenticated admins/staff
        if (Auth::check() && (Auth::user()->hasPermissionTo('manage settings') || Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Admin'))) {
            return $next($request);
        }

        // 3. Skip check if we are already on the maintenance/coming-soon pages
        if ($request->is('maintenance') || $request->is('coming-soon')) {
            return $next($request);
        }

        // 4. Check Maintenance Mode
        if (settings('maintenance_mode')) {
            return redirect()->route('maintenance');
        }

        // 5. Check Coming Soon Mode
        if (settings('coming_soon_mode')) {
            return redirect()->route('coming-soon');
        }

        return $next($request);
    }
}
