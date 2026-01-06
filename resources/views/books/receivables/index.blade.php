@extends('layouts.app')

@section('title', 'Buku Piutang (Advances)')
@section('page-title', 'Buku Piutang')

@section('content')
<div class="space-y-6">
    <!-- Filter Card -->
    <div class="bg-white rounded-xl shadow-sm p-6 border">
        <form action="{{ route('books.receivables.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Proyek</label>
                <select name="project" class="w-full border-gray-300 rounded-lg text-sm">
                    <option value="">Semua Proyek</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->kode_proyek }}" {{ request('project') == $project->kode_proyek ? 'selected' : '' }}>
                            {{ $project->kode_proyek }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Bulan</label>
                <select name="month" class="w-full border-gray-300 rounded-lg text-sm">
                    <option value="">Semua Bulan</option>
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ sprintf('%02d', $m) }}" {{ request('month') == sprintf('%02d', $m) ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tahun</label>
                <select name="year" class="w-full border-gray-300 rounded-lg text-sm">
                    <option value="">Semua Tahun</option>
                    @for($y=date('Y'); $y>=2020; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <a href="{{ route('books.receivables.export', request()->all()) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-file-excel mr-2"></i> Export
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Periode</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Proyek</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Beginning Balance</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Ending Balance</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-center">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($headers as $header)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-bold text-gray-800">
                            {{ date('F', mktime(0, 0, 0, $header->periode_bulan, 1)) }} {{ $header->periode_tahun }}
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $header->kode_proyek }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                             ${{ number_format($header->beginning_balance_usd, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-blue-600">
                             ${{ number_format($header->ending_balance_usd, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 text-xs rounded-full {{ $header->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($header->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('books.receivables.show', $header->id_piutang) }}" class="text-blue-600 hover:text-blue-800">
                                View Details <i class="fas fa-chevron-right ml-1"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">Tidak ada data buku piutang</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($headers->hasPages())
            <div class="px-6 py-4 border-t text-sm">
                {{ $headers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
