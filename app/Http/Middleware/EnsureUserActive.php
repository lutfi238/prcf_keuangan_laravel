<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->isPending()) {
            auth()->logout();
            return redirect()->route('auth.pending')
                ->with('info', 'Akun Anda sedang menunggu persetujuan Admin.');
        }

        if ($request->user()->isInactive()) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Akun Anda tidak aktif. Hubungi Administrator.');
        }

        return $next($request);
    }
}
