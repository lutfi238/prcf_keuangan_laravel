@extends('layouts.app')

@section('title', 'Daftar Laporan Keuangan')
@section('page-title', 'Laporan Keuangan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-gray-600">Kelola laporan keuangan proyek</p>
        </div>
        @if(auth()->user()->isProjectManager() || auth()->user()->isAdmin())
        <a href="{{ route('reports.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i> Buat Laporan
        </a>
        @endif
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                       placeholder="Cari laporan...">
            </div>
            <div class="w-48">
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $status)
                    <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
        </form>
    </div>
    
    <!-- Report List -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Kegiatan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyek</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelaksana</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reports as $report)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <a href="{{ route('reports.show', $report->id_laporan_keu) }}" 
                           class="text-blue-600 hover:underline font-medium">
                            {{ Str::limit($report->nama_kegiatan, 40) }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $report->kode_projek }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $report->pelaksana }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $report->status_lap->badgeColor() }}">
                            {{ $report->status_lap->label() }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $report->tanggal_laporan?->format('d/m/Y') ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('reports.show', $report->id_laporan_keu) }}" 
                               class="text-gray-600 hover:text-blue-600" title="Lihat">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($report->status_lap->value === 'submitted' && auth()->user()->isStaffAccountant())
                            <a href="{{ route('reports.verify-sa', $report->id_laporan_keu) }}" 
                               class="text-purple-600 hover:text-purple-800" title="Verifikasi">
                                <i class="fas fa-check-double"></i>
                            </a>
                            @endif
                            @if($report->status_lap->value === 'verified' && auth()->user()->isFinanceManager())
                            <a href="{{ route('reports.approve-fm', $report->id_laporan_keu) }}" 
                               class="text-green-600 hover:text-green-800" title="Approve">
                                <i class="fas fa-check-circle"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-4"></i>
                        <p>Belum ada laporan keuangan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($reports->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $reports->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
