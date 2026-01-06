@extends('layouts.app')

@section('title', isset($code->id) ? 'Edit Expense Code' : 'Tambah Expense Code')
@section('page-title', isset($code->id) ? 'Edit Expense Code' : 'Tambah Expense Code')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('finance.expense-codes.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border">
        <form action="{{ isset($code->id) ? route('finance.expense-codes.update', $code->id) : route('finance.expense-codes.store') }}" method="POST">
            @csrf
            @if(isset($code->id))
                @method('PUT')
            @endif

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode *</label>
                    <input type="text" name="code" value="{{ old('code', $code->code) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="contoh: 5310">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi *</label>
                    <input type="text" name="description" value="{{ old('description', $code->description) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="contoh: Travel & Transportation">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <input type="text" name="category" value="{{ old('category', $code->category) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="contoh: Operational">
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('finance.expense-codes.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
