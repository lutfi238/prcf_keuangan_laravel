@extends('layouts.app')

@section('title', isset($village) ? 'Edit Desa' : 'Tambah Desa')
@section('page-title', isset($village) ? 'Edit Desa' : 'Tambah Desa Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('finance.villages.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ isset($village) ? route('finance.villages.update', $village->id_village) : route('finance.villages.store') }}" method="POST">
            @csrf
            @if(isset($village))
                @method('PUT')
            @endif

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ID Desa (3 Digit Angka) *</label>
                    <input type="text" name="id_village" value="{{ old('id_village', $village->id_village ?? '') }}" 
                           required maxlength="3" pattern="\d{3}" {{ isset($village) ? 'readonly bg-gray-50' : '' }}
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="cth: 001">
                    <p class="mt-1 text-xs text-gray-500">ID unik untuk kode akuntansi desa.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Desa *</label>
                    <input type="text" name="village_name" value="{{ old('village_name', $village->village_name ?? '') }}" 
                           required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="pt-4 border-t flex justify-end space-x-3">
                    <button type="button" onclick="history.back()" class="px-6 py-2 border rounded-lg">Batal</button>
                    <button type="submit" class="px-8 py-2 bg-blue-600 text-white rounded-lg font-bold">
                        <i class="fas fa-save mr-2"></i> {{ isset($village) ? 'Simpan Perubahan' : 'Tambah Desa' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
