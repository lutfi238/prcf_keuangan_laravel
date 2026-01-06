@extends('layouts.app')

@section('title', 'Kelola Desa')
@section('page-title', 'Daftar Desa')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <p class="text-gray-600">Master data desa intervensi</p>
        <a href="{{ route('finance.villages.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i> Tambah Desa
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">ID Desa</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Nama Desa</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($villages as $village)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono text-sm">{{ $village->id_village }}</td>
                        <td class="px-6 py-4 font-medium">{{ $village->village_name }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('finance.villages.edit', $village->id_village) }}" class="text-yellow-600 hover:text-yellow-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('finance.villages.destroy', $village->id_village) }}" method="POST" onsubmit="return confirm('Hapus desa ini?')">
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
                        <td colspan="3" class="px-6 py-10 text-center text-gray-500">Data desa kosong</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($villages->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $villages->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
