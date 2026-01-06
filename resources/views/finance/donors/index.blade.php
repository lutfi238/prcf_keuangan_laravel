@extends('layouts.app')

@section('title', 'Kelola Donor')
@section('page-title', 'Daftar Donor')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <p class="text-gray-600">Data pemberi dana/grant untuk proyek</p>
        <a href="{{ route('finance.donors.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow">
            <i class="fas fa-plus mr-2"></i> Tambah Donor
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden border">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Kode</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Nama Donor</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Negara</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($donors as $donor)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono font-bold">{{ $donor->kode_donor }}</td>
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $donor->nama_donor }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $donor->negara ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $donor->email ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('finance.donors.edit', $donor->id_donor) }}" class="text-yellow-600 hover:text-yellow-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('finance.donors.destroy', $donor->id_donor) }}" method="POST" onsubmit="return confirm('Hapus donor ini?')">
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
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">Belum ada data donor</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($donors->hasPages())
            <div class="px-6 py-4 border-t">{{ $donors->links() }}</div>
        @endif
    </div>
</div>
@endsection
