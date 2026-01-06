<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenance
{
    /**
     * Handle an incoming request.
     * Allow Admin to bypass maintenance mode.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (SystemSetting::isMaintenanceMode()) {
            $user = $request->user();
            
            // Allow Admin to access during maintenance
            if ($user && $user->isAdmin()) {
                return $next($request);
            }
            
            // Allow login/logout routes
            if ($request->routeIs('login', 'login.submit', 'logout', 'auth.*')) {
                return $next($request);
            }
            
            // Show maintenance page for everyone else
            return response()->view('errors.maintenance', [], 503);
        }

        return $next($request);
    }
}
