@extends('layouts.app')

@section('title', 'Kelola Proyek')
@section('page-title', 'Daftar Proyek')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <p class="text-gray-600">Daftar proyek PRCF Indonesia</p>
        <a href="{{ route('finance.projects.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow">
            <i class="fas fa-plus mr-2"></i> Tambah Proyek
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden border">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Kode</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Nama Proyek</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Durasi</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Budget (USD)</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-center">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($projects as $project)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono text-sm font-bold">{{ $project->kode_proyek }}</td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-800">{{ $project->nama_proyek }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $project->start_date ? $project->start_date->format('M Y') : 'N/A' }} - 
                            {{ $project->end_date ? $project->end_date->format('M Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            ${{ number_format($project->total_budget_usd, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 text-xs rounded-full {{ $project->status_proyek->value === 'ongoing' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $project->status_proyek->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('finance.projects.edit', $project->kode_proyek) }}" class="text-yellow-600 hover:text-yellow-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('finance.projects.destroy', $project->kode_proyek) }}" method="POST" onsubmit="return confirm('Hapus proyek ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">Data proyek kosong</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($projects->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
