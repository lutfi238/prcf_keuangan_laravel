@extends('layouts.app')

@section('title', 'Persetujuan Laporan Keuangan')
@section('page-title', 'Approval Laporan (Finance Manager)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="mb-6">
        <a href="{{ route('reports.show', $report->id_laporan_keu) }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Detail
        </a>
    </div>

    <!-- Summary Box -->
    <div class="bg-white rounded-xl shadow-sm p-6 border">
        <h3 class="font-bold text-gray-800 mb-4">Data Laporan Terverifikasi</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Kegiatan:</span>
                <p class="font-bold text-gray-800 text-base">{{ $report->nama_kegiatan }}</p>
            </div>
            <div>
                <span class="text-gray-500">Nilai Laporan:</span>
                <p class="font-bold text-blue-700 text-base">
                    {{ $report->mata_uang }} {{ number_format($report->total_amount, 2) }}
                    <span class="text-xs font-normal text-gray-400 block">(Kurs: {{ number_format($report->exrate) }})</span>
                </p>
            </div>
            <div class="md:col-span-2 p-3 bg-purple-50 border border-purple-100 rounded-lg">
                <span class="text-xs font-bold text-purple-600 uppercase">Catatan Staff Accountant:</span>
                <p class="text-gray-700 italic mt-1">"{{ $report->notes_sa }}"</p>
            </div>
        </div>
    </div>

    <!-- Approval Form -->
    <div class="bg-green-50 rounded-xl shadow-sm p-8 border border-green-200">
        <h3 class="text-lg font-bold text-green-900 mb-4"><i class="fas fa-check-circle mr-2"></i> Keputusan Finance Manager</h3>
        
        <form action="{{ route('reports.approve-fm.submit', $report->id_laporan_keu) }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-semibold text-green-800 mb-2">Keputusan *</label>
                <div class="flex gap-4">
                    <label class="flex items-center px-4 py-3 bg-white border border-green-300 rounded-lg cursor-pointer hover:bg-green-100 transition w-1/2">
                        <input type="radio" name="status" value="approved" class="form-radio text-green-600" required>
                        <span class="ml-3 font-bold text-green-800"> SETUJU (APPROVE)</span>
                    </label>
                    <label class="flex items-center px-4 py-3 bg-white border border-red-300 rounded-lg cursor-pointer hover:bg-red-50 transition w-1/2">
                        <input type="radio" name="status" value="revision" class="form-radio text-red-600" required>
                        <span class="ml-3 font-bold text-red-800">TOLAK / REVISI</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-green-800 mb-2">Catatan Approval *</label>
                <textarea name="notes" rows="4" required
                          class="w-full px-4 py-3 border border-green-300 rounded-lg focus:ring-2 focus:ring-green-500"
                          placeholder="Berikan instruksi atau catatan persetujuan..."></textarea>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-10 py-3 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 shadow-lg transition">
                    Submit Keputusan Approval
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
