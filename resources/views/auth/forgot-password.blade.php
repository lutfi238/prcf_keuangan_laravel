@extends('layouts.auth')

@section('title', 'Lupa Password - PRCF Keuangan')

@section('content')
<!-- Logo & Header -->
<div class="text-center mb-6">
    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-yellow-500 shadow-lg mb-3">
        <i class="fas fa-key text-xl text-white"></i>
    </div>
    <h1 class="text-xl font-bold text-gray-800">Lupa Password?</h1>
    <p class="text-gray-500 text-sm">Masukkan email untuk reset password</p>
</div>

<!-- Forgot Password Card -->
<div class="bg-white rounded-xl shadow-lg p-6">
    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                   placeholder="nama@email.com">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <button type="submit" 
                class="w-full py-3 px-4 bg-yellow-500 text-white font-semibold rounded-lg shadow hover:bg-yellow-600 transition">
            <i class="fas fa-paper-plane mr-2"></i> Kirim Link Reset
        </button>
    </form>
    
    <div class="mt-4 text-center">
        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-800 text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
        </a>
    </div>
</div>
@endsection
