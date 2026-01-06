@extends('layouts.app')

@section('title', 'System Health')
@section('page-title', 'System Health Check')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.system-control.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke System Control
        </a>
    </div>

    <!-- Health Checks -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Health Checks</h3>
        
        <div class="space-y-4">
            @foreach($checks as $name => $check)
            <div class="flex items-center justify-between p-4 border rounded-lg 
                {{ $check['status'] === 'ok' ? 'bg-green-50 border-green-200' : ($check['status'] === 'warning' ? 'bg-yellow-50 border-yellow-200' : 'bg-red-50 border-red-200') }}">
                <div class="flex items-center gap-4">
                    @if($check['status'] === 'ok')
                        <i class="fas fa-check-circle text-2xl text-green-500"></i>
                    @elseif($check['status'] === 'warning')
                        <i class="fas fa-exclamation-triangle text-2xl text-yellow-500"></i>
                    @else
                        <i class="fas fa-times-circle text-2xl text-red-500"></i>
                    @endif
                    <div>
                        <h4 class="font-medium text-gray-800 capitalize">{{ $name }}</h4>
                        <p class="text-sm text-gray-500">{{ $check['message'] }}</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-medium uppercase
                    {{ $check['status'] === 'ok' ? 'bg-green-100 text-green-700' : ($check['status'] === 'warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                    {{ $check['status'] }}
                </span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Refresh Button -->
    <div class="flex justify-end">
        <a href="{{ route('admin.system-control.health') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-sync-alt mr-2"></i> Refresh
        </a>
    </div>
</div>
@endsection
