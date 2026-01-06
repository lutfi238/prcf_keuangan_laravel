<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode - PRCF</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-lg text-center px-6">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-tools text-4xl text-yellow-600"></i>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Sedang Maintenance</h1>
            
            <p class="text-gray-600 mb-6">
                Sistem sedang dalam pemeliharaan untuk meningkatkan layanan kami. 
                Mohon coba kembali dalam beberapa saat.
            </p>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Jika Anda adalah administrator, silakan login untuk mengakses sistem.
                </p>
            </div>
            
            <a href="{{ route('login') }}" class="inline-block px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition-colors">
                <i class="fas fa-sign-in-alt mr-2"></i> Login Admin
            </a>
        </div>
        
        <p class="text-gray-400 text-sm mt-6">
            PRCF Indonesia Financial Management System
        </p>
    </div>
</body>
</html>
