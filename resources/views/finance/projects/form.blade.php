@extends('layouts.app')

@section('title', isset($project) ? 'Edit Proyek' : 'Tambah Proyek')
@section('page-title', isset($project) ? 'Edit Proyek' : 'Tambah Proyek Baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('finance.projects.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ isset($project) ? route('finance.projects.update', $project->kode_proyek) : route('finance.projects.store') }}" method="POST">
            @csrf
            @if(isset($project))
                @method('PUT')
            @endif

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Proyek *</label>
                        <input type="text" name="kode_proyek" value="{{ old('kode_proyek', $project->kode_proyek ?? '') }}" 
                               required {{ isset($project) ? 'readonly bg-gray-50' : '' }}
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="cth: TF23-01">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status Proyek *</label>
                        <select name="status_proyek" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="ongoing" {{ old('status_proyek', $project->status_proyek ?? '') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="completed" {{ old('status_proyek', $project->status_proyek ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Proyek *</label>
                    <input type="text" name="nama_proyek" value="{{ old('nama_proyek', $project->nama_proyek ?? '') }}" 
                           required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ old('start_date', isset($project) && $project->start_date ? $project->start_date->format('Y-m-d') : '') }}" 
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berakhir</label>
                        <input type="date" name="end_date" value="{{ old('end_date', isset($project) && $project->end_date ? $project->end_date->format('Y-m-d') : '') }}" 
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Budget (USD) *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                        <input type="number" step="0.01" name="total_budget_usd" value="{{ old('total_budget_usd', $project->total_budget_usd ?? '0') }}" 
                               required class="w-full pl-8 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-right">
                    </div>
                </div>

                <div class="pt-4 border-t flex justify-end space-x-3">
                    <button type="button" onclick="history.back()" class="px-6 py-2 border rounded-lg">Batal</button>
                    <button type="submit" class="px-8 py-2 bg-blue-600 text-white rounded-lg font-bold">
                        <i class="fas fa-save mr-2"></i> {{ isset($project) ? 'Simpan Proyek' : 'Tambah Proyek' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
