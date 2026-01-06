@extends('layouts.auth')

@section('title', 'Reset Password - PRCF Keuangan')

@section('content')
<!-- Logo & Header -->
<div class="text-center mb-6">
    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-green-500 shadow-lg mb-3">
        <i class="fas fa-lock-open text-xl text-white"></i>
    </div>
    <h1 class="text-xl font-bold text-gray-800">Reset Password</h1>
    <p class="text-gray-500 text-sm">Masukkan password baru Anda</p>
</div>

<!-- Reset Password Card -->
<div class="bg-white rounded-xl shadow-lg p-6">
    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">
        
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
            <input type="password" id="password" name="password" required
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   placeholder="Minimal 6 karakter">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   placeholder="Ulangi password baru">
        </div>
        
        <button type="submit" 
                class="w-full py-3 px-4 bg-green-500 text-white font-semibold rounded-lg shadow hover:bg-green-600 transition">
            <i class="fas fa-save mr-2"></i> Simpan Password Baru
        </button>
    </form>
</div>
@endsection
