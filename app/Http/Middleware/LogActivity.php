<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Auth::check() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $routeName = $request->route() ? $request->route()->getName() : 'unknown';
            
            // Skip logging the activity log itself and notifications/search
            if (str_contains($routeName, 'activity-log') || str_contains($routeName, 'notifications')) {
                return $response;
            }

            $module = $this->deriveModule($routeName);
            $action = $this->deriveAction($request->method(), $routeName);
            
            \App\Models\ActivityLog::log(
                $action,
                $module,
                $this->deriveDescription($request, $module, $action),
                $request->except(['password', 'password_confirmation', '_token', '_method'])
            );
        }

        return $response;
    }

    private function deriveModule($routeName)
    {
        if (str_contains($routeName, 'proposals')) return 'Proposals';
        if (str_contains($routeName, 'reports')) return 'Reports';
        if (str_contains($routeName, 'budgets')) return 'Budgets';
        if (str_contains($routeName, 'users')) return 'Users';
        if (str_contains($routeName, 'villages')) return 'Villages';
        if (str_contains($routeName, 'projects')) return 'Projects';
        if (str_contains($routeName, 'donors')) return 'Donors';
        if (str_contains($routeName, 'expense-codes')) return 'Expense Codes';
        if (str_contains($routeName, 'bank')) return 'Bank Book';
        if (str_contains($routeName, 'receivables')) return 'Receivables Book';
        return 'System';
    }

    private function deriveAction($method, $routeName)
    {
        if ($method === 'POST') return str_contains($routeName, 'store') ? 'Created' : 'Action';
        if (in_array($method, ['PUT', 'PATCH'])) return 'Updated';
        if ($method === 'DELETE') return 'Deleted';
        return $method;
    }

    private function deriveDescription($request, $module, $action)
    {
        $user = Auth::user();
        return "{$user->nama} ({$user->role->value}) {$action} in {$module}";
    }
}
