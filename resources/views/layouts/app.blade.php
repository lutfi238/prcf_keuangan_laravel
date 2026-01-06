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
    
    <!-- Custom Styles -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen">
    <div x-data="{ sidebarOpen: true }" class="flex min-h-screen">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" 
               class="bg-gradient-to-b from-blue-800 to-blue-900 text-white transition-all duration-300 fixed h-full z-30 flex flex-col">
            <div class="p-4 flex items-center justify-between border-b border-blue-700 flex-shrink-0">
                <div x-show="sidebarOpen" class="flex items-center space-x-2">
                    <i class="fas fa-coins text-yellow-400 text-2xl"></i>
                    <span class="font-bold text-lg">PRCF Keuangan</span>
                </div>
                <button @click="sidebarOpen = !sidebarOpen" class="text-white hover:bg-blue-700 p-2 rounded">
                    <i class="fas" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
                </button>
            </div>
            
            <nav class="mt-4 flex-1 overflow-y-auto pb-20">
                @auth
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-4 py-3 hover:bg-blue-700 {{ request()->routeIs('dashboard*') ? 'bg-blue-700' : '' }}">
                    <i class="fas fa-home w-6"></i>
                    <span x-show="sidebarOpen" class="ml-3">Dashboard</span>
                </a>
                
                <!-- Proposals -->
                <a href="{{ route('proposals.index') }}" 
                   class="flex items-center px-4 py-3 hover:bg-blue-700 {{ request()->routeIs('proposals*') ? 'bg-blue-700' : '' }}">
                    <i class="fas fa-file-alt w-6"></i>
                    <span x-show="sidebarOpen" class="ml-3">Proposal</span>
                </a>
                
                <!-- Reports -->
                <a href="{{ route('reports.index') }}" 
                   class="flex items-center px-4 py-3 hover:bg-blue-700 {{ request()->routeIs('reports.index') || request()->routeIs('reports.show') ? 'bg-blue-700' : '' }}">
                    <i class="fas fa-chart-bar w-6"></i>
                    <span x-show="sidebarOpen" class="ml-3">Laporan Keuangan</span>
                </a>

                <!-- Donor Reports -->
                <a href="{{ route('reports.donor.index') }}" 
                   class="flex items-center px-4 py-3 hover:bg-blue-700 {{ request()->routeIs('reports.donor*') ? 'bg-blue-700' : '' }}">
                    <i class="fas fa-globe-asia w-6"></i>
                    <span x-show="sidebarOpen" class="ml-3">Laporan Donor</span>
                </a>

                @if(auth()->user()->isStaffAccountant() || auth()->user()->isFinanceManager())
                <!-- Financial Books -->
                <div class="mt-4 border-t border-blue-700 pt-4">
                    <p x-show="sidebarOpen" class="px-4 text-xs text-blue-300 uppercase tracking-wider font-bold">Financial Books</p>
                    
                    <a href="{{ route('books.bank.index') }}" 
                       class="flex items-center px-4 py-3 hover:bg-blue-700 {{ request()->routeIs('books.bank*') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-university w-6"></i>
                        <span x-show="sidebarOpen" class="ml-3">Buku Bank</span>
                    </a>
                    
                    <a href="{{ route('books.receivables.index') }}" 
                       class="flex items-center px-4 py-3 hover:bg-blue-700 {{ request()->routeIs('books.receivables*') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-hand-holding-usd w-6"></i>
                        <span x-show="sidebarOpen" class="ml-3">Buku Piutang</span>
                    </a>
                </div>
                @endif

                @if(auth()->check() && auth()->user()->isFinanceManager())
                <!-- Finance Manager Menu -->
                <div class="mt-4 border-t border-blue-700 pt-4">
                    <p x-show="sidebarOpen" class="px-4 text-xs text-blue-300 uppercase tracking-wider font-bold">Finance</p>
                    
                    <a href="{{ route('finance.budgets.index') }}" 
                       class="flex items-center px-4 py-3 hover:bg-blue-700 {{ request()->routeIs('finance.budgets*') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-calculator w-6"></i>
                        <span x-show="sidebarOpen" class="ml-3">Kelola Budget</span>
                    </a>

                    <a href="{{ route('finance.villages.index') }}" 
                       class="flex items-center px-4 py-3 hover:bg-blue-700 {{ request()->routeIs('finance.villages*') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-map-marker-alt w-6"></i>
                        <span x-show="sidebarOpen" class="ml-3">Kelola Desa</span>
                    </a>
                    
                    <a href="{{ route('finance.projects.index') }}" 
                       class="flex items-center px-4 py-3 hover:bg-blue-700 {{ request()->routeIs('finance.projects*') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-project-diagram w-6"></i>
                        <span x-show="sidebarOpen" class="ml-3">Kelola Proyek</span>
                    </a>
                    
                    <a href="{{ route('finance.donors.index') }}" 
                       class="flex items-center px-4 py-3 hover:bg-blue-700 {{ request()->routeIs('finance.donors*') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-hand-holding-usd w-6"></i>
                        <span x-show="sidebarOpen" class="ml-3">Kelola Donor</span>
                    </a>
                    
                    <a href="{{ route('finance.expense-codes.index') }}" 
                       class="flex items-center px-4 py-3 hover:bg-blue-700 {{ request()->routeIs('finance.expense-codes*') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-tags w-6"></i>
                        <span x-show="sidebarOpen" class="ml-3">Expense Codes</span>
                    </a>
                </div>
                @endif
                @endauth
                
                @if(auth()->check() && auth()->user()->isAdmin())
                <!-- Admin Section -->
                <div class="mt-4 border-t border-blue-700 pt-4">
                    <p x-show="sidebarOpen" class="px-4 text-xs text-blue-300 uppercase tracking-wider">Admin</p>
                    
                    <a href="{{ route('admin.users.index') }}" 
                       class="flex items-center px-4 py-3 hover:bg-blue-700 {{ request()->routeIs('admin.users*') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-users w-6"></i>
                        <span x-show="sidebarOpen" class="ml-3">Kelola User</span>
                    </a>
                    
                    <a href="{{ route('admin.activity-log.index') }}" 
                       class="flex items-center px-4 py-3 hover:bg-blue-700 {{ request()->routeIs('admin.activity-log*') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-history w-6"></i>
                        <span x-show="sidebarOpen" class="ml-3">Log Aktivitas</span>
                    </a>
                    
                    <a href="{{ route('admin.system-control.index') }}" 
                       class="flex items-center px-4 py-3 hover:bg-blue-700 {{ request()->routeIs('admin.system-control*') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-cog w-6"></i>
                        <span x-show="sidebarOpen" class="ml-3">System Control</span>
                    </a>
                </div>
                @endif
            </nav>
        </aside>
        
        <!-- Main Content -->
        <div :class="sidebarOpen ? 'ml-64' : 'ml-20'" class="flex-1 transition-all duration-300">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm sticky top-0 z-20">
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        @auth
                        <!-- Notifications -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="relative text-gray-600 hover:text-gray-800">
                                <i class="fas fa-bell text-xl"></i>
                                @if(auth()->user()->unreadNotificationsCount() > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ auth()->user()->unreadNotificationsCount() }}
                                </span>
                                @endif
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-cloak
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg overflow-hidden z-50">
                                <div class="px-4 py-3 bg-gray-50 border-b">
                                    <span class="font-semibold text-gray-700">Notifikasi</span>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    @forelse(auth()->user()->recentNotifications(5) as $notification)
                                    <a href="{{ $notification->link ?? '#' }}" 
                                       class="block px-4 py-3 hover:bg-gray-50 {{ !$notification->is_read ? 'bg-blue-50' : '' }}">
                                        <p class="text-sm text-gray-800">{{ $notification->message }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                    </a>
                                    @empty
                                    <p class="px-4 py-3 text-sm text-gray-500">Tidak ada notifikasi</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Menu -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                    {{ substr(auth()->user()->nama, 0, 1) }}
                                </div>
                                <span class="hidden md:inline">{{ auth()->user()->nama }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-cloak
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg overflow-hidden z-50">
                                <div class="px-4 py-3 border-b">
                                    <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->nama }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->role->label() }}</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                        @else
                        <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">Login</a>
                        @endauth
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="p-6">
                <!-- Flash Messages -->
                @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
                @endif
                
                @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
                @endif
                
                @if(session('info'))
                <div class="mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded-lg flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    {{ session('info') }}
                </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>