<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRegistration
{
    /**
     * Handle an incoming request.
     * Block registration if disabled.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!SystemSetting::isRegistrationEnabled()) {
            return redirect()->route('login')
                ->with('error', 'Pendaftaran saat ini tidak tersedia. Silakan hubungi administrator.');
        }

        return $next($request);
    }
}
