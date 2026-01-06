@extends('layouts.app')

@section('title', 'Verifikasi Laporan Keuangan')
@section('page-title', 'Verifikasi Laporan (Staff Accountant)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="mb-6">
        <a href="{{ route('reports.show', $report->id_laporan_keu) }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Detail
        </a>
    </div>

    <!-- Info Wrap -->
    <div class="bg-white rounded-xl shadow-sm p-6 border">
        <h3 class="font-bold text-gray-800 mb-4">Ringkasan Laporan</h3>
        <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
            <dt class="text-gray-500">Kegiatan:</dt>
            <dd class="font-semibold text-gray-800">{{ $report->nama_kegiatan }}</dd>
            
            <dt class="text-gray-500">Proyek:</dt>
            <dd>{{ $report->kode_projek }} - {{ $report->nama_projek }}</dd>
            
            <dt class="text-gray-500">Total Transaksi:</dt>
            <dd class="font-bold text-blue-600">{{ $report->mata_uang }} {{ number_format($report->total_amount, 2) }}</dd>
            
            <dt class="text-gray-500">Pembuat:</dt>
            <dd>{{ $report->createdBy->nama }} ({{ $report->created_at->format('d/m/Y') }})</dd>
        </dl>
    </div>

    <!-- Verification Form -->
    <div class="bg-purple-50 rounded-xl shadow-sm p-8 border border-purple-200">
        <h3 class="text-lg font-bold text-purple-900 mb-4"><i class="fas fa-clipboard-check mr-2"></i> Form Verifikasi</h3>
        
        <form action="{{ route('reports.verify-sa.submit', $report->id_laporan_keu) }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-semibold text-purple-800 mb-2">Tindakan Verifikasi *</label>
                <div class="flex gap-4">
                    <label class="flex items-center px-4 py-3 bg-white border border-purple-300 rounded-lg cursor-pointer hover:bg-purple-100 transition w-1/2">
                        <input type="radio" name="status" value="verified" class="form-radio text-purple-600" required>
                        <span class="ml-3 font-bold text-green-700">LOLOS VERIFIKASI</span>
                    </label>
                    <label class="flex items-center px-4 py-3 bg-white border border-red-300 rounded-lg cursor-pointer hover:bg-red-50 transition w-1/2">
                        <input type="radio" name="status" value="revision" class="form-radio text-red-600" required>
                        <span class="ml-3 font-bold text-red-700">PERLU REVISI</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-purple-800 mb-2">Catatan Verifikasi (Notes) *</label>
                <textarea name="notes" rows="4" required
                          class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                          placeholder="Berikan alasan lolos atau poin-poin yang perlu direvisi..."></textarea>
                <p class="mt-1 text-xs text-purple-600 italic">Catatan ini akan dilihat oleh pembuat laporan.</p>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-10 py-3 bg-purple-600 text-white rounded-xl font-bold hover:bg-purple-700 shadow-lg transform active:scale-95 transition">
                    Submit Hasil Verifikasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
