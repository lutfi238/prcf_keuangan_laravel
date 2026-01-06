@extends('layouts.app')

@section('title', $user ? 'Edit User' : 'Tambah User')
@section('page-title', $user ? 'Edit User' : 'Tambah User Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
        
        <form method="POST" 
              action="{{ $user ? route('admin.users.update', $user) : route('admin.users.store') }}">
            @csrf
            @if($user)
            @method('PUT')
            @endif
            
            <div class="space-y-6">
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                    <input type="text" id="nama" name="nama" 
                           value="{{ old('nama', $user?->nama) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('nama')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" id="email" name="email" 
                           value="{{ old('email', $user?->email) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="no_HP" class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                    <input type="text" id="no_HP" name="no_HP" 
                           value="{{ old('no_HP', $user?->no_HP) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('no_HP')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                    <select id="role" name="role" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @foreach($roles as $role)
                        <option value="{{ $role->value }}" 
                                {{ old('role', $user?->role?->value) == $role->value ? 'selected' : '' }}>
                            {{ $role->label() }}
                        </option>
                        @endforeach
                    </select>
                    @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="active" {{ old('status', $user?->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pending" {{ old('status', $user?->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="inactive" {{ old('status', $user?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password {{ $user ? '(kosongkan jika tidak ingin mengubah)' : '*' }}
                    </label>
                    <input type="password" id="password" name="password" 
                           {{ $user ? '' : 'required' }}
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Konfirmasi Password
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <div class="mt-6 flex items-center justify-end space-x-4">
                <a href="{{ route('admin.users.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
