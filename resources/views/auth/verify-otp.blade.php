@extends('layouts.app')

@section('title', 'Verifikasi OTP - PRCF Keuangan')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="flex justify-center">
                <svg class="h-16 w-16 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Verifikasi OTP
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Kode OTP telah dikirim ke email Anda
            </p>
            <p class="text-sm font-medium text-green-600">
                {{ session('otp_email', 'email Anda') }}
            </p>
        </div>
        
        <div class="bg-white py-8 px-6 shadow-lg rounded-lg">
            <form class="space-y-6" method="POST" action="{{ route('auth.verify-otp.submit') }}">
                @csrf
                
                <div>
                    <label for="otp" class="block text-sm font-medium text-gray-700 text-center">
                        Masukkan Kode OTP (6 digit)
                    </label>
                    <div class="mt-3">
                        <input id="otp" name="otp" type="text"
                            maxlength="6"
                            pattern="[0-9]{6}"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            required
                            placeholder="000000"
                            class="appearance-none block w-full px-3 py-4 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 text-center text-2xl tracking-widest font-mono @error('otp') border-red-500 @enderror">
                    </div>
                    @error('otp')
                        <p class="mt-2 text-sm text-red-600 text-center">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150">
                        Verifikasi
                    </button>
                </div>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 mb-3">
                    Tidak menerima kode?
                </p>
                <form method="POST" action="{{ route('auth.resend-otp') }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="text-sm font-medium text-green-600 hover:text-green-500 focus:outline-none">
                        Kirim Ulang OTP
                    </button>
                </form>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    ← Kembali ke Login
                </a>
            </div>
        </div>
        
        <div class="text-center text-xs text-gray-500">
            <p>Kode OTP berlaku selama 60 detik</p>
        </div>
    </div>
</div>
@endsection