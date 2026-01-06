@extends('layouts.app')

@section('title', 'Kelola Expense Code')
@section('page-title', 'Daftar Expense Code')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <p class="text-gray-600">Kode pengeluaran untuk standarisasi laporan</p>
        <a href="{{ route('finance.expense-codes.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow">
            <i class="fas fa-plus mr-2"></i> Tambah Code
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden border">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Kode</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Deskripsi</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($codes as $code)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono font-bold text-blue-600">{{ $code->code }}</td>
                        <td class="px-6 py-4">{{ $code->description }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $code->category ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('finance.expense-codes.edit', $code->id) }}" class="text-yellow-600 hover:text-yellow-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('finance.expense-codes.destroy', $code->id) }}" method="POST" onsubmit="return confirm('Hapus expense code ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">Belum ada expense code</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($codes->hasPages())
            <div class="px-6 py-4 border-t">{{ $codes->links() }}</div>
        @endif
    </div>
</div>
@endsection
