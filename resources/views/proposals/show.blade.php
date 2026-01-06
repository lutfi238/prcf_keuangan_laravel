@extends('layouts.app')

@section('title', $proposal->judul_proposal)
@section('page-title', 'Detail Proposal')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('proposals.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Proposal
        </a>
    </div>
    
    <!-- Proposal Header -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $proposal->judul_proposal }}</h2>
                <p class="text-gray-500 mt-1">Penanggung Jawab: {{ $proposal->pj }}</p>
            </div>
            <span class="px-3 py-1 text-sm rounded-full {{ $proposal->status->badgeColor() }}">
                {{ $proposal->status->label() }}
            </span>
        </div>
        
        <!-- Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div>
                <p class="text-sm text-gray-500">Kode Proyek</p>
                <p class="font-semibold">{{ $proposal->kode_proyek }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Pemohon</p>
                <p class="font-semibold">{{ $proposal->pemohon }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal</p>
                <p class="font-semibold">{{ $proposal->date?->format('d F Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Budget (USD)</p>
                <p class="font-semibold text-green-600">${{ number_format($proposal->total_budget_usd, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Budget (IDR)</p>
                <p class="font-semibold">Rp {{ number_format($proposal->total_budget_idr) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Exchange Rate</p>
                <p class="font-semibold">{{ number_format($proposal->exrate_at_submission) }}</p>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="flex flex-wrap gap-3 mt-6 pt-6 border-t">
            @if($proposal->isEditable() && (auth()->user()->isProjectManager() || auth()->user()->isAdmin()))
            <a href="{{ route('proposals.edit', $proposal->id_proposal) }}" 
               class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            
            <form method="POST" action="{{ route('proposals.submit', $proposal->id_proposal) }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-paper-plane mr-1"></i> Submit untuk Review
                </button>
            </form>
            
            <form method="POST" action="{{ route('proposals.destroy', $proposal->id_proposal) }}" class="inline"
                  onsubmit="return confirm('Yakin ingin menghapus proposal ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-trash mr-1"></i> Hapus
                </button>
            </form>
            @endif
            
            @if($proposal->status->value === 'submitted' && auth()->user()->isFinanceManager())
            <a href="{{ route('proposals.review-fm', $proposal->id_proposal) }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-check-circle mr-1"></i> Review & Approve
            </a>
            @endif
            
            @if($proposal->tor)
            <a href="{{ Storage::url($proposal->tor) }}" target="_blank"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                <i class="fas fa-file-pdf mr-1"></i> Download TOR
            </a>
            @endif
            
            @if($proposal->file_budget)
            <a href="{{ Storage::url($proposal->file_budget) }}" target="_blank"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                <i class="fas fa-file-excel mr-1"></i> Download Budget
            </a>
            @endif
        </div>
    </div>
    
    <!-- Approval Info -->
    @if($proposal->approvedByFm)
    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
        <h4 class="font-semibold text-green-800 mb-2">
            <i class="fas fa-check-circle mr-1"></i> Disetujui oleh Finance Manager
        </h4>
        <p class="text-green-700">
            {{ $proposal->approvedByFm->nama }} pada {{ $proposal->fm_approval_date?->format('d F Y H:i') }}
        </p>
    </div>
    @endif
    
    <!-- Budget Details -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Detail Budget</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Desa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exp Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">USD</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">IDR</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($proposal->budgetDetails as $detail)
                <tr>
                    <td class="px-6 py-4 text-sm">{{ $detail->village?->village_name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm">{{ $detail->exp_code }}</td>
                    <td class="px-6 py-4 text-sm">{{ $detail->description ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-right">${{ number_format($detail->requested_usd, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-right">Rp {{ number_format($detail->requested_idr) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada detail budget</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="3" class="px-6 py-3 text-right font-semibold">Total:</td>
                    <td class="px-6 py-3 text-right font-semibold">${{ number_format($proposal->total_budget_usd, 2) }}</td>
                    <td class="px-6 py-3 text-right font-semibold">Rp {{ number_format($proposal->total_budget_idr) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
