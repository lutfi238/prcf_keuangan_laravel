<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Proyek;
use App\Models\Proposal;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display the dashboard based on user role
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get basic stats for dashboard
        $stats = $this->getDashboardStats($user);
        
        return view('dashboard.index', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    /**
     * Get dashboard statistics based on user role
     */
    private function getDashboardStats($user): array
    {
        $stats = [];
        
        // Common stats for all roles
        $stats[] = [
            'label' => 'Total Proyek',
            'value' => Proyek::count(),
            'color' => 'bg-green-500',
            'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        ];
        
        $stats[] = [
            'label' => 'Proyek Aktif',
            'value' => Proyek::where('status_proyek', 'ongoing')->count(),
            'color' => 'bg-blue-500',
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        ];
        
        $stats[] = [
            'label' => 'Total Proposal',
            'value' => Proposal::count(),
            'color' => 'bg-yellow-500',
            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        ];
        
        $stats[] = [
            'label' => 'Proposal Pending',
            'value' => Proposal::where('status', 'Submitted')->count(),
            'color' => 'bg-orange-500',
            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        ];

        // Role-specific stats
        if ($user->isAdmin()) {
            $stats[] = [
                'label' => 'Total Users',
                'value' => User::count(),
                'color' => 'bg-purple-500',
                'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            ];
            $stats[] = [
                'label' => 'User Pending',
                'value' => User::where('status', 'pending')->count(),
                'color' => 'bg-red-500',
                'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z',
            ];
        }

        if ($user->isFinanceManager()) {
            $stats[] = [
                'label' => 'Menunggu Approval',
                'value' => Proposal::where('status', 'Submitted')->count(),
                'color' => 'bg-indigo-500',
                'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
            ];
        }

        return $stats;
    }
}