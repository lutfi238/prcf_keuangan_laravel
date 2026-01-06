@extends('layouts.app')

@section('title', 'Laporan Donor')
@section('page-title', 'Laporan Donor')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border">
        <form action="{{ route('reports.donor.index') }}" method="GET" class="flex gap-4">
            <select name="project" class="border-gray-300 rounded-lg text-sm">
                <option value="">Semua Proyek</option>
                @foreach($projects as $project)
                    <option value="{{ $project->kode_proyek }}" {{ request('project') == $project->kode_proyek ? 'selected' : '' }}>
                        {{ $project->kode_proyek }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                <i class="fas fa-filter"></i>
            </button>
        </form>
        @if(auth()->user()->isProgramManager() || auth()->user()->isAdmin())
            <a href="{{ route('reports.donor.create') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm transition">
                <i class="fas fa-plus mr-2"></i> Buat Laporan Donor
            </a>
        @endif
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Periode</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Proyek</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">Draft Anggaran</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">Realisasi</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-center">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($reports as $report)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-800">{{ $report->periode }}</span>
                            <p class="text-xs text-gray-400">Dibuat: {{ $report->created_at->format('d/m/Y') }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $report->kode_proyek }}</td>
                        <td class="px-6 py-4 text-right text-sm">${{ number_format($report->total_anggaran, 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-blue-600">${{ number_format($report->total_realisasi, 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 text-xs rounded-full {{ $report->status === 'sent' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ ucfirst($report->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right flex justify-end gap-2">
                            <a href="{{ route('reports.donor.download', $report->id_donor) }}" class="p-2 text-green-600 hover:bg-green-50 rounded-lg" title="Download">
                                <i class="fas fa-download"></i>
                            </a>
                            <a href="{{ route('reports.donor.show', $report->id_donor) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="View Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">Belum ada laporan donor.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
