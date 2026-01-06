@extends('layouts.app')

@section('title', 'Detail Laporan Donor')
@section('page-title', 'Detail Laporan Donor')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between font-medium">
        <a href="{{ route('reports.donor.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
        </a>
        <div class="flex gap-2">
            @if($report->file_laporan)
                <a href="{{ route('reports.donor.download', $report->id_donor) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-sm transition">
                    <i class="fas fa-download mr-2"></i> Download File
                </a>
            @endif
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="p-8 border-b bg-gray-50 flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $report->periode }}</h2>
                <p class="text-gray-500">{{ $report->kode_proyek }} - {{ $report->proyek->nama_proyek ?? '' }}</p>
                <p class="mt-2 text-sm">
                    <span class="text-gray-400">Diposting oleh:</span> 
                    <span class="font-bold text-gray-700">{{ $report->createdBy->nama ?? 'Unknown' }}</span>
                    <span class="mx-2 text-gray-300">|</span>
                    <span class="text-gray-400">Tanggal:</span> 
                    <span class="font-bold text-gray-700">{{ $report->created_at->format('d/m/Y') }}</span>
                </p>
            </div>
            <span class="px-4 py-2 text-sm rounded-full bg-blue-100 text-blue-700 font-bold">
                {{ strtoupper($report->status) }}
            </span>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 pb-8 border-b">
                <div class="bg-gray-50 p-4 rounded-xl border border-dashed text-center">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Anggaran (USD)</p>
                    <p class="text-3xl font-black text-gray-700">${{ number_format($report->total_anggaran, 2) }}</p>
                </div>
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 text-center">
                    <p class="text-xs font-bold text-blue-400 uppercase tracking-wider mb-1">Total Realisasi (USD)</p>
                    <p class="text-3xl font-black text-blue-700">${{ number_format($report->total_realisasi, 2) }}</p>
                </div>
            </div>

            <div class="space-y-8">
                <div>
                    <h4 class="flex items-center text-gray-800 font-bold mb-3">
                        <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3">
                            <i class="fas fa-tasks"></i>
                        </span>
                        Realisasi Kegiatan
                    </h4>
                    <div class="pl-11 text-gray-600 leading-relaxed italic">
                        {{ $report->realisasi_kegiatan ?: 'Tidak ada ringkasan kegiatan.' }}
                    </div>
                </div>

                <div>
                    <h4 class="flex items-center text-gray-800 font-bold mb-3">
                        <span class="w-8 h-8 rounded-lg bg-green-100 text-green-600 flex items-center justify-center mr-3">
                            <i class="fas fa-coins"></i>
                        </span>
                        Realisasi Keuangan
                    </h4>
                    <div class="pl-11 text-gray-600 leading-relaxed italic">
                        {{ $report->realisasi_keuangan ?: 'Tidak ada ringkasan keuangan.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
