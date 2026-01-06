@extends('layouts.app')

@section('title', 'Buat Laporan Donor')
@section('page-title', 'Buat Laporan Donor')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('reports.donor.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
        </a>
    </div>

    <form action="{{ route('reports.donor.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border overflow-hidden">
        @csrf
        
        <div class="p-8 space-y-6">
            <h3 class="text-lg font-bold text-gray-800 border-b pb-4 mb-6">Informasi Laporan</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Proyek *</label>
                    <select name="kode_proyek" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Proyek</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->kode_proyek }}">{{ $project->kode_proyek }} - {{ $project->nama_proyek }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Periode Laporan *</label>
                    <input type="text" name="periode" required placeholder="Contoh: Q1 2024 atau Jan-Mar 2024"
                           class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Total Anggaran (USD) *</label>
                    <input type="number" step="0.01" name="total_anggaran" required
                           class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Total Realisasi (USD) *</label>
                    <input type="number" step="0.01" name="total_realisasi" required
                           class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Unggah File Laporan (PDF/ZIP/DOC) *</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition cursor-pointer relative">
                    <div class="space-y-1 text-center text-gray-600">
                        <i class="fas fa-file-upload text-4xl mb-3 text-gray-400"></i>
                        <p class="text-sm">Klik untuk pilih file atau drag and drop</p>
                        <p class="text-xs text-gray-400">Max size 10MB</p>
                    </div>
                    <input type="file" name="file_laporan" required class="absolute inset-0 opacity-0 cursor-pointer">
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Ringkasan Realisasi Kegiatan</label>
                    <textarea name="realisasi_kegiatan" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Opsional..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Ringkasan Realisasi Keuangan</label>
                    <textarea name="realisasi_keuangan" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Opsional..."></textarea>
                </div>
            </div>
        </div>

        <div class="px-8 py-4 bg-gray-50 border-t flex justify-end">
            <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 shadow-lg transform active:scale-95 transition">
                Simpan & Posting Laporan
            </button>
        </div>
    </form>
</div>
@endsection
