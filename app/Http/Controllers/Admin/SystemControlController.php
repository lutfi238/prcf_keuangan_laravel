<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SystemControlController extends Controller
{
    /**
     * Display system control panel
     */
    public function index()
    {
        $settings = [
            'maintenance_mode' => SystemSetting::isMaintenanceMode(),
            'registration_enabled' => SystemSetting::isRegistrationEnabled(),
        ];

        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'pending_users' => User::where('status', 'pending')->count(),
            'admin_count' => User::where('role', 'Admin')->count(),
        ];

        // Get database size (MySQL)
        $dbSize = DB::select("SELECT 
            ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb 
            FROM information_schema.tables 
            WHERE table_schema = ?", [env('DB_DATABASE')])[0]->size_mb ?? 0;

        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'db_size_mb' => $dbSize,
            'server_time' => now()->format('Y-m-d H:i:s'),
        ];

        return view('admin.system-control.index', compact('settings', 'stats', 'systemInfo'));
    }

    /**
     * Toggle maintenance mode
     */
    public function toggleMaintenance(Request $request)
    {
        $newState = SystemSetting::toggleMaintenanceMode();
        $status = $newState ? 'diaktifkan' : 'dinonaktifkan';

        Log::info("ADMIN ACTION: Maintenance mode {$status} by user ID " . auth()->id());

        return back()->with('success', "Maintenance mode berhasil {$status}.");
    }

    /**
     * Toggle registration
     */
    public function toggleRegistration(Request $request)
    {
        $newState = SystemSetting::toggleRegistration();
        $status = $newState ? 'diaktifkan' : 'dinonaktifkan';

        Log::info("ADMIN ACTION: Registration {$status} by user ID " . auth()->id());

        return back()->with('success', "Registrasi publik berhasil {$status}.");
    }

    /**
     * System health check
     */
    public function health()
    {
        $checks = [];

        // Database connection
        try {
            DB::connection()->getPdo();
            $checks['database'] = ['status' => 'ok', 'message' => 'Connected'];
        } catch (\Exception $e) {
            $checks['database'] = ['status' => 'error', 'message' => $e->getMessage()];
        }

        // Storage writable
        $storagePath = storage_path('app');
        $checks['storage'] = [
            'status' => is_writable($storagePath) ? 'ok' : 'error',
            'message' => is_writable($storagePath) ? 'Writable' : 'Not writable',
        ];

        // Logs writable
        $logsPath = storage_path('logs');
        $checks['logs'] = [
            'status' => is_writable($logsPath) ? 'ok' : 'error',
            'message' => is_writable($logsPath) ? 'Writable' : 'Not writable',
        ];

        // Memory usage
        $memoryUsage = round(memory_get_usage(true) / 1024 / 1024, 2);
        $checks['memory'] = [
            'status' => $memoryUsage < 128 ? 'ok' : 'warning',
            'message' => "{$memoryUsage} MB used",
        ];

        return view('admin.system-control.health', compact('checks'));
    }
}
