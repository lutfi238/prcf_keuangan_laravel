@extends('layouts.app')

@section('title', 'System Control')
@section('page-title', 'System Control')

@section('content')
<div class="space-y-6">
    <!-- System Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-gray-500">PHP Version</p>
            <p class="text-xl font-bold text-gray-800">{{ $systemInfo['php_version'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-gray-500">Laravel Version</p>
            <p class="text-xl font-bold text-gray-800">{{ $systemInfo['laravel_version'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-gray-500">Database Size</p>
            <p class="text-xl font-bold text-gray-800">{{ $systemInfo['db_size_mb'] }} MB</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-gray-500">Server Time</p>
            <p class="text-xl font-bold text-gray-800">{{ $systemInfo['server_time'] }}</p>
        </div>
    </div>

    <!-- User Stats -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">User Statistics</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-3xl font-bold text-blue-600">{{ $stats['total_users'] }}</p>
                <p class="text-sm text-gray-600">Total Users</p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <p class="text-3xl font-bold text-green-600">{{ $stats['active_users'] }}</p>
                <p class="text-sm text-gray-600">Active</p>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_users'] }}</p>
                <p class="text-sm text-gray-600">Pending</p>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <p class="text-3xl font-bold text-purple-600">{{ $stats['admin_count'] }}</p>
                <p class="text-sm text-gray-600">Admins</p>
            </div>
        </div>
    </div>

    <!-- System Controls -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">System Controls</h3>
        
        <div class="space-y-4">
            <!-- Maintenance Mode -->
            <div class="flex items-center justify-between p-4 border rounded-lg {{ $settings['maintenance_mode'] ? 'bg-red-50 border-red-200' : 'bg-gray-50' }}">
                <div>
                    <h4 class="font-medium text-gray-800">Maintenance Mode</h4>
                    <p class="text-sm text-gray-500">Ketika aktif, hanya Admin yang dapat mengakses sistem</p>
                </div>
                <div class="flex items-center gap-4">
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $settings['maintenance_mode'] ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        {{ $settings['maintenance_mode'] ? 'Aktif' : 'Nonaktif' }}
                    </span>
                    <form method="POST" action="{{ route('admin.system-control.toggle-maintenance') }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg {{ $settings['maintenance_mode'] ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-red-600 text-white hover:bg-red-700' }}">
                            {{ $settings['maintenance_mode'] ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Registration -->
            <div class="flex items-center justify-between p-4 border rounded-lg {{ $settings['registration_enabled'] ? 'bg-green-50 border-green-200' : 'bg-gray-50' }}">
                <div>
                    <h4 class="font-medium text-gray-800">Public Registration</h4>
                    <p class="text-sm text-gray-500">Izinkan pengguna baru untuk mendaftar akun</p>
                </div>
                <div class="flex items-center gap-4">
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $settings['registration_enabled'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $settings['registration_enabled'] ? 'Aktif' : 'Nonaktif' }}
                    </span>
                    <form method="POST" action="{{ route('admin.system-control.toggle-registration') }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg {{ $settings['registration_enabled'] ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-green-600 text-white hover:bg-green-700' }}">
                            {{ $settings['registration_enabled'] ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('admin.users.index') }}" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <div>
                    <h4 class="font-medium">Manage Users</h4>
                    <p class="text-sm text-gray-500">Kelola akun pengguna</p>
                </div>
            </div>
        </a>
        <a href="{{ route('admin.activity-log.index') }}" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-history text-purple-600"></i>
                </div>
                <div>
                    <h4 class="font-medium">Activity Log</h4>
                    <p class="text-sm text-gray-500">Lihat log aktivitas</p>
                </div>
            </div>
        </a>
        <a href="{{ route('admin.system-control.health') }}" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-heartbeat text-green-600"></i>
                </div>
                <div>
                    <h4 class="font-medium">System Health</h4>
                    <p class="text-sm text-gray-500">Cek kesehatan sistem</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
