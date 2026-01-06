@extends('layouts.auth')

@section('title', 'Login - PRCF Keuangan')

@section('content')
<!-- Logo & Header -->
<div class="text-center mb-8">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-500 shadow-lg mb-4">
        <i class="fas fa-coins text-2xl text-white"></i>
    </div>
    <h1 class="text-2xl font-bold text-gray-800">
        PRCF Keuangan
    </h1>
    <p class="mt-1 text-gray-500 text-sm">
        Sistem Manajemen Keuangan Proyek
    </p>
</div>

<!-- Login Card -->
<div class="bg-white rounded-xl shadow-lg p-8">
    <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
        @csrf
        
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                Email
            </label>
            <input id="email" name="email" type="email" autocomplete="email" required 
                value="{{ old('email') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror"
                placeholder="nama@prcf.id">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                Password
            </label>
            <input id="password" name="password" type="password" autocomplete="current-password" required 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('password') border-red-500 @enderror"
                placeholder="••••••••">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center text-gray-600">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-green-500 focus:ring-green-500 mr-2">
                Ingat saya
            </label>
            <a href="{{ route('password.request') }}" class="text-green-600 hover:text-green-700">
                Lupa password?
            </a>
        </div>

        <button type="submit" 
            class="w-full py-3 px-4 bg-green-500 text-white font-semibold rounded-lg shadow hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition">
            <i class="fas fa-sign-in-alt mr-2"></i>
            Masuk
        </button>
    </form>
    
    <div class="mt-6 text-center border-t pt-6">
        <p class="text-gray-600 text-sm">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-green-600 hover:text-green-700 font-medium">
                Daftar sekarang
            </a>
        </p>
    </div>
</div>
@endsection