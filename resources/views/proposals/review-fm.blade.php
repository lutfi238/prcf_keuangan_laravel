@extends('layouts.app')

@section('title', 'Review Proposal - FM')
@section('page-title', 'Review Proposal')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('proposals.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>
    
    <!-- Proposal Info -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">{{ $proposal->judul_proposal }}</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Kode Proyek</p>
                <p class="font-semibold">{{ $proposal->kode_proyek }}</p>
            </div>
            <div>
                <p class="text-gray-500">Pemohon</p>
                <p class="font-semibold">{{ $proposal->pemohon }}</p>
            </div>
            <div>
                <p class="text-gray-500">Total Budget Request</p>
                <p class="font-semibold text-blue-600">${{ number_format($proposal->total_budget_usd, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Budget Availability Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <p class="text-sm text-blue-600 font-medium">Total Diminta</p>
            <p class="text-2xl font-bold text-blue-800">${{ number_format($totalRequested, 2) }}</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <p class="text-sm text-green-600 font-medium">Total Tersedia</p>
            <p class="text-2xl font-bold text-green-800">${{ number_format($totalAvailable, 2) }}</p>
        </div>
        <div class="{{ $allSufficient ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200' }} border rounded-xl p-4">
            <p class="text-sm {{ $allSufficient ? 'text-green-600' : 'text-yellow-600' }} font-medium">Status Budget</p>
            <p class="text-xl font-bold {{ $allSufficient ? 'text-green-800' : 'text-yellow-800' }}">
                @if($allSufficient)
                    <i class="fas fa-check-circle mr-1"></i> Semua Cukup
                @else
                    <i class="fas fa-exclamation-triangle mr-1"></i> Ada Kekurangan
                @endif
            </p>
        </div>
    </div>
    
    <!-- Budget Details with Availability -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="font-semibold">Detail Budget & Ketersediaan</h3>
            <p class="text-sm text-gray-500">Info budget tersedia per Exp Code dan Place Code</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Desa / Place Code</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Exp Code</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Diminta (USD)</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Tersedia (USD)</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($proposal->budgetDetails as $detail)
                    <tr class="{{ !$detail->is_sufficient ? 'bg-yellow-50' : '' }}">
                        <td class="px-4 py-3">
                            <span class="font-medium">{{ $detail->village?->village_name ?? '-' }}</span>
                            @if($detail->place_code)
                                <br><span class="text-xs text-gray-500 font-mono">{{ $detail->place_code }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-mono">{{ $detail->exp_code }}</td>
                        <td class="px-4 py-3 text-right font-medium">${{ number_format($detail->requested_usd ?? 0, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            @if($detail->budget_exists)
                                <span class="{{ $detail->is_sufficient ? 'text-green-600' : 'text-red-600' }} font-medium">
                                    ${{ number_format($detail->available_usd, 2) }}
                                </span>
                                <br><span class="text-xs text-gray-500">Rp {{ number_format($detail->available_idr) }}</span>
                            @else
                                <span class="text-red-500 text-xs">
                                    <i class="fas fa-exclamation-circle"></i> Budget belum diset
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if(!$detail->budget_exists)
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                    <i class="fas fa-times"></i> Tidak Ada
                                </span>
                            @elseif($detail->is_sufficient)
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                    <i class="fas fa-check"></i> Cukup
                                </span>
                            @else
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">
                                    <i class="fas fa-exclamation"></i> Kurang
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Warning if insufficient budget -->
    @if(!$allSufficient)
    <div class="bg-yellow-50 border border-yellow-300 rounded-xl p-4">
        <div class="flex items-start">
            <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 mr-3"></i>
            <div>
                <h4 class="font-semibold text-yellow-800">Perhatian: Budget Tidak Mencukupi</h4>
                <p class="text-sm text-yellow-700 mt-1">
                    Beberapa item proposal membutuhkan budget lebih dari yang tersedia. 
                    Anda tetap dapat menyetujui proposal ini, tetapi budget untuk exp/place code terkait akan menjadi minus.
                </p>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Approval Actions -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-4">Keputusan Approval</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Approve Form -->
            <form method="POST" action="{{ route('proposals.approve-fm', $proposal->id_proposal) }}"
                  class="p-4 border border-green-200 rounded-lg bg-green-50">
                @csrf
                <h4 class="font-semibold text-green-800 mb-3">
                    <i class="fas fa-check-circle mr-1"></i> Setujui Proposal
                </h4>
                <p class="text-sm text-green-700 mb-3">
                    Menyetujui akan mencairkan dana ke Buku Bank dan Buku Piutang.
                </p>
                <div class="mb-4">
                    <label class="block text-sm text-gray-700 mb-1">Catatan (Opsional)</label>
                    <textarea name="catatan_fm" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                              placeholder="Tambahkan catatan..."></textarea>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                    <i class="fas fa-check mr-1"></i> Setujui & Cairkan Dana
                </button>
            </form>
            
            <!-- Reject Form -->
            <form method="POST" action="{{ route('proposals.reject', $proposal->id_proposal) }}"
                  class="p-4 border border-red-200 rounded-lg bg-red-50">
                @csrf
                <h4 class="font-semibold text-red-800 mb-3">
                    <i class="fas fa-times-circle mr-1"></i> Tolak Proposal
                </h4>
                <p class="text-sm text-red-700 mb-3">
                    Menolak akan mengembalikan proposal ke PM untuk revisi.
                </p>
                <div class="mb-4">
                    <label class="block text-sm text-gray-700 mb-1">Alasan Penolakan *</label>
                    <textarea name="catatan" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                              placeholder="Jelaskan alasan penolakan..."></textarea>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                    <i class="fas fa-times mr-1"></i> Tolak Proposal
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
