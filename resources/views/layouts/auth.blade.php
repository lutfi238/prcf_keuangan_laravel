<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PRCF Keuangan') - PRCF Indonesia</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .flash-message {
            animation: fadeInOut 4s ease-in-out forwards;
        }
        @keyframes fadeInOut {
            0% { opacity: 0; transform: translateX(100%); }
            10% { opacity: 1; transform: translateX(0); }
            80% { opacity: 1; transform: translateX(0); }
            100% { opacity: 0; transform: translateX(100%); }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <!-- Flash Messages with auto-dismiss -->
    @if(session('success'))
    <div class="flash-message fixed top-4 right-4 max-w-sm p-4 bg-green-500 text-white rounded-lg shadow-xl flex items-center z-50">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div class="flash-message fixed top-4 right-4 max-w-sm p-4 bg-red-500 text-white rounded-lg shadow-xl flex items-center z-50">
        <i class="fas fa-exclamation-circle mr-2"></i>
        {{ session('error') }}
    </div>
    @endif

    <div class="w-full max-w-md">
        @yield('content')
        
        <!-- Footer -->
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} People Resources and Conservation Foundation</p>
        </div>
    </div>

    <script>
        // Auto-remove flash messages after animation
        document.addEventListener('DOMContentLoaded', function() {
            const flashMessages = document.querySelectorAll('.flash-message');
            flashMessages.forEach(function(msg) {
                setTimeout(function() {
                    msg.remove();
                }, 4000);
            });
        });
    </script>
</body>
</html>
