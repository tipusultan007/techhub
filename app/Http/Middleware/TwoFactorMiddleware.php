<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasTwoFactorEnabled()) {
            if (! $request->session()->has('two_factor_verified')) {
                // Ignore these routes to prevent infinite loops
                $excludedRoutes = [
                    'two-factor.verify', 
                    'two-factor.verify.store',
                    'two-factor.select',
                    'two-factor.resend',
                    'logout'
                ];

                if (! in_array($request->route()->getName(), $excludedRoutes)) {
                    return redirect()->route('two-factor.verify');
                }
            }
        }

        return $next($request);
    }
}
