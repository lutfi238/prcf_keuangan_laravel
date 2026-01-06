@extends('layouts.app')

@section('title', 'Kelola User')
@section('page-title', 'Kelola User')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-gray-600">Manajemen akun pengguna sistem</p>
        </div>
        <a href="{{ route('admin.users.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i> Tambah User
        </a>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                       placeholder="Cari nama atau email...">
            </div>
            <div class="w-48">
                <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Semua Role</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->value }}" {{ request('role') == $role->value ? 'selected' : '' }}>
                        {{ $role->label() }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="w-40">
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
        </form>
    </div>
    
    <!-- User List -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                {{ substr($user->nama, 0, 1) }}
                            </div>
                            <span class="ml-3 font-medium">{{ $user->nama }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            {{ $user->role->label() }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($user->status === 'active')
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                        @elseif($user->status === 'pending')
                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        @else
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $user->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.users.edit', $user) }}" 
                               class="text-gray-600 hover:text-yellow-600" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            @if($user->status === 'pending')
                            <form method="POST" action="{{ route('admin.users.activate', $user) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800" title="Aktivasi">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                            </form>
                            @elseif($user->status === 'active' && $user->id_user !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.deactivate', $user) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Nonaktifkan">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </form>
                            @endif
                            
                            @if($user->id_user !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline"
                                  onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-users text-4xl mb-4"></i>
                        <p>Belum ada user</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($users->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
