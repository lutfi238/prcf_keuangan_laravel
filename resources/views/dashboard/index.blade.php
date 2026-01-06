@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl p-6 text-white">
        <h2 class="text-2xl font-bold mb-2">Selamat Datang, {{ auth()->user()->nama }}!</h2>
        <p class="text-blue-100">{{ auth()->user()->role->label() }} - PRCF Indonesia Financial Management System</p>
    </div>
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($stats as $stat)
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="{{ $stat['color'] }} p-3 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">{{ $stat['label'] }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stat['value']) }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if(auth()->user()->isProjectManager())
            <a href="{{ route('proposals.create') }}" 
               class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <i class="fas fa-plus-circle text-blue-600 text-2xl mr-3"></i>
                <div>
                    <p class="font-semibold text-gray-800">Buat Proposal</p>
                    <p class="text-sm text-gray-500">Ajukan proposal baru</p>
                </div>
            </a>
            
            <a href="{{ route('reports.create') }}" 
               class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <i class="fas fa-file-invoice-dollar text-green-600 text-2xl mr-3"></i>
                <div>
                    <p class="font-semibold text-gray-800">Buat Laporan</p>
                    <p class="text-sm text-gray-500">Buat laporan keuangan</p>
                </div>
            </a>
            @endif
            
            @if(auth()->user()->isFinanceManager())
            <a href="{{ route('proposals.index', ['status' => 'submitted']) }}" 
               class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                <i class="fas fa-clipboard-check text-yellow-600 text-2xl mr-3"></i>
                <div>
                    <p class="font-semibold text-gray-800">Review Proposal</p>
                    <p class="text-sm text-gray-500">Proposal menunggu approval</p>
                </div>
            </a>
            @endif
            
            @if(auth()->user()->isStaffAccountant())
            <a href="{{ route('reports.index', ['status' => 'submitted']) }}" 
               class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                <i class="fas fa-check-double text-purple-600 text-2xl mr-3"></i>
                <div>
                    <p class="font-semibold text-gray-800">Verifikasi Laporan</p>
                    <p class="text-sm text-gray-500">Laporan menunggu verifikasi</p>
                </div>
            </a>
            @endif
            
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.users.index', ['status' => 'pending']) }}" 
               class="flex items-center p-4 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                <i class="fas fa-user-plus text-red-600 text-2xl mr-3"></i>
                <div>
                    <p class="font-semibold text-gray-800">User Pending</p>
                    <p class="text-sm text-gray-500">Aktivasi user baru</p>
                </div>
            </a>
            @endif
        </div>
    </div>
</div>
@endsection