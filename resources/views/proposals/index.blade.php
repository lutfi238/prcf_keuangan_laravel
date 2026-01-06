@extends('layouts.app')

@section('title', 'Daftar Proposal')
@section('page-title', 'Proposal')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-gray-600">Kelola proposal pengajuan dana</p>
        </div>
        @if(auth()->user()->isProjectManager() || auth()->user()->isAdmin())
        <a href="{{ route('proposals.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus mr-2"></i> Buat Proposal
        </a>
        @endif
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Cari proposal...">
            </div>
            <div class="w-48">
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $status)
                    <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="w-48">
                <select name="project" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Proyek</option>
                    @foreach($projects as $project)
                    <option value="{{ $project->kode_proyek }}" {{ request('project') == $project->kode_proyek ? 'selected' : '' }}>
                        {{ $project->kode_proyek }}
                    </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
        </form>
    </div>
    
    <!-- Proposal List -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul Proposal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyek</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemohon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Budget</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($proposals as $proposal)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <a href="{{ route('proposals.show', $proposal->id_proposal) }}" 
                           class="text-blue-600 hover:underline font-medium">
                            {{ Str::limit($proposal->judul_proposal, 40) }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $proposal->kode_proyek }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $proposal->pemohon }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        ${{ number_format($proposal->total_budget_usd, 2) }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $proposal->status->badgeColor() }}">
                            {{ $proposal->status->label() }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $proposal->date?->format('d/m/Y') ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('proposals.show', $proposal->id_proposal) }}" 
                               class="text-gray-600 hover:text-blue-600" title="Lihat">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($proposal->isEditable() && (auth()->user()->isProjectManager() || auth()->user()->isAdmin()))
                            <a href="{{ route('proposals.edit', $proposal->id_proposal) }}" 
                               class="text-gray-600 hover:text-yellow-600" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endif
                            @if($proposal->status->value === 'submitted' && auth()->user()->isFinanceManager())
                            <a href="{{ route('proposals.review-fm', $proposal->id_proposal) }}" 
                               class="text-green-600 hover:text-green-800" title="Review">
                                <i class="fas fa-check-circle"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-4"></i>
                        <p>Belum ada proposal</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Pagination -->
        @if($proposals->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $proposals->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
