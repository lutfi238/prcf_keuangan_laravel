@extends('layouts.auth')

@section('title', 'Daftar - PRCF Keuangan')

@section('content')
<!-- Logo & Header -->
<div class="text-center mb-6">
    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-green-500 shadow-lg mb-3">
        <i class="fas fa-user-plus text-xl text-white"></i>
    </div>
    <h1 class="text-xl font-bold text-gray-800">Daftar Akun Baru</h1>
    <p class="text-gray-500 text-sm">PRCF Keuangan</p>
</div>

<!-- Register Card -->
<div class="bg-white rounded-xl shadow-lg p-6">
    @if ($errors->any())
    <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-lg text-sm">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <form method="POST" action="{{ route('register.submit') }}" class="space-y-4">
        @csrf
        
        <div>
            <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   placeholder="Masukkan nama lengkap">
        </div>
        
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   placeholder="nama@email.com">
        </div>
        
        <div>
            <label for="no_HP" class="block text-sm font-medium text-gray-700 mb-1">No. HP (Opsional)</label>
            <input type="text" id="no_HP" name="no_HP" value="{{ old('no_HP') }}"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   placeholder="08xxxxxxxxxx">
        </div>
        
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" id="password" name="password" required
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   placeholder="Minimal 6 karakter">
        </div>
        
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   placeholder="Ulangi password">
        </div>
        
        <button type="submit" 
                class="w-full py-3 px-4 bg-green-500 text-white font-semibold rounded-lg shadow hover:bg-green-600 transition">
            <i class="fas fa-user-plus mr-2"></i> Daftar
        </button>
    </form>
    
    <div class="mt-4 text-center border-t pt-4">
        <p class="text-gray-600 text-sm">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-green-600 hover:text-green-700 font-medium">Login disini</a>
        </p>
    </div>
</div>
@endsection
