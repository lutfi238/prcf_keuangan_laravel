@extends('layouts.auth')

@section('title', 'Menunggu Aktivasi - PRCF Keuangan')

@section('content')
<!-- Logo & Header -->
<div class="text-center mb-6">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-yellow-500 shadow-lg mb-3">
        <i class="fas fa-hourglass-half text-2xl text-white"></i>
    </div>
    <h1 class="text-xl font-bold text-gray-800">Menunggu Aktivasi</h1>
    <p class="text-gray-500 text-sm">Akun Anda sedang dalam proses verifikasi</p>
</div>

<!-- Pending Card -->
<div class="bg-white rounded-xl shadow-lg p-6 text-center">
    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-6">
        <p class="text-yellow-700 text-sm">
            <i class="fas fa-info-circle mr-2"></i>
            Administrator akan segera mengaktifkan akun Anda. Silakan cek email secara berkala.
        </p>
    </div>
    
    <p class="text-gray-600 text-sm mb-6">
        Setelah akun diaktifkan, Anda akan dapat login dan menggunakan semua fitur sistem PRCF Keuangan.
    </p>
    
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" 
                class="w-full py-3 px-4 bg-gray-100 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </button>
    </form>
</div>
@endsection