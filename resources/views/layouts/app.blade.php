<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Unified Manager ERP')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        secondary: '#64748b',
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
</head>
<body class="bg-gray-50" x-data="{ sidebarOpen: false }">
    
    <!-- Sidebar -->
    <aside 
        x-cloak
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-white transform lg:translate-x-0 transition-transform duration-300 ease-in-out overflow-y-auto"
    >
        <!-- Logo -->
        <div class="flex items-center justify-between h-16 px-6 bg-gradient-to-r from-blue-600 to-blue-700">
            <div class="flex items-center space-x-3">
                <i class="bi bi-shop text-2xl"></i>
                <h1 class="text-xl font-bold">Unified Manager</h1>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden text-white hover:text-gray-300">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="px-4 py-6 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300' }}">
                <i class="bi bi-speedometer2 text-lg"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <!-- Ventas Section -->
            @can('ver_ordenes_entrega')
            <div class="pt-4 pb-2">
                <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Ventas</p>
            </div>
            
            <a href="{{ route('ordenes-entrega.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('ordenes-entrega.*') ? 'bg-slate-800 text-white' : 'text-slate-300' }}">
                <i class="bi bi-file-earmark-text text-lg"></i>
                <span class="font-medium">Órdenes de Entrega</span>
            </a>
            @endcan

            @can('ver_facturas')
            <a href="{{ route('facturas.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('facturas.*') ? 'bg-slate-800 text-white' : 'text-slate-300' }}">
                <i class="bi bi-receipt text-lg"></i>
                <span class="font-medium">Facturas</span>
            </a>
            @endcan

            @can('ver_notas_credito')
            <a href="{{ route('notas-credito.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('notas-credito.*') ? 'bg-slate-800 text-white' : 'text-slate-300' }}">
                <i class="bi bi-arrow-return-left text-lg"></i>
                <span class="font-medium">Notas de Crédito</span>
            </a>
            @endcan

            @can('ver_cobranza')
            <a href="{{ route('cobros.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('cobros.*') ? 'bg-slate-800 text-white' : 'text-slate-300' }}">
                <i class="bi bi-cash-coin text-lg"></i>
                <span class="font-medium">Cobros</span>
            </a>
            @endcan

            @can('ver_rutas')
            <a href="{{ route('rutas-diarias.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('rutas-diarias.*') ? 'bg-slate-800 text-white' : 'text-slate-300' }}">
                <i class="bi bi-truck text-lg"></i>
                <span class="font-medium">Rutas Diarias</span>
            </a>
            @endcan

            <!-- Compras Section -->
            @can('ver_ordenes_compra')
            <div class="pt-4 pb-2">
                <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Compras</p>
            </div>

            <a href="{{ route('ordenes-compra.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('ordenes-compra.*') ? 'bg-slate-800 text-white' : 'text-slate-300' }}">
                <i class="bi bi-cart-plus text-lg"></i>
                <span class="font-medium">Órdenes de Compra</span>
            </a>
            @endcan

            <!-- Catálogos -->
            <div class="pt-4 pb-2">
                <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Catálogos</p>
            </div>

            <a href="{{ route('clientes.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('clientes.*') ? 'bg-slate-800 text-white' : 'text-slate-300' }}">
                <i class="bi bi-people text-lg"></i>
                <span class="font-medium">Clientes</span>
            </a>

            <a href="{{ route('productos.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('productos.*') ? 'bg-slate-800 text-white' : 'text-slate-300' }}">
                <i class="bi bi-box-seam text-lg"></i>
                <span class="font-medium">Productos</span>
            </a>

            <!-- Reportes -->
            @can('ver_reportes_ventas')
            <div class="pt-4 pb-2">
                <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Reportes</p>
            </div>

            <a href="{{ route('reportes.ventas') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('reportes.ventas') ? 'bg-slate-800 text-white' : 'text-slate-300' }}">
                <i class="bi bi-graph-up text-lg"></i>
                <span class="font-medium">Ventas</span>
            </a>

            <a href="{{ route('reportes.estado-resultados') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('reportes.estado-resultados') ? 'bg-slate-800 text-white' : 'text-slate-300' }}">
                <i class="bi bi-file-earmark-bar-graph text-lg"></i>
                <span class="font-medium">Estado de Resultados</span>
            </a>

            <a href="{{ route('reportes.cuentas-cobrar') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('reportes.cuentas-cobrar') ? 'bg-slate-800 text-white' : 'text-slate-300' }}">
                <i class="bi bi-wallet2 text-lg"></i>
                <span class="font-medium">Cuentas por Cobrar</span>
            </a>

            <a href="{{ route('reportes.inventario') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('reportes.inventario') ? 'bg-slate-800 text-white' : 'text-slate-300' }}">
                <i class="bi bi-boxes text-lg"></i>
                <span class="font-medium">Inventario</span>
            </a>
            @endcan
        </nav>
    </aside>

    <!-- Overlay (Mobile) -->
    <div 
        x-show="sidebarOpen" 
        @click="sidebarOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
    ></div>

    <!-- Main Content -->
    <div class="lg:ml-72 min-h-screen">
        <!-- Top Navbar -->
        <header class="bg-white shadow-sm sticky top-0 z-30">
            <div class="flex items-center justify-between px-4 py-4 lg:px-8">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-600 hover:text-slate-900">
                    <i class="bi bi-list text-2xl"></i>
                </button>

                <div class="hidden lg:block">
                    <h2 class="text-xl font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h2>
                </div>

                <div class="flex items-center space-x-4" x-data="{ userMenuOpen: false }">
                    <!-- Notifications -->
                    <button class="relative text-slate-600 hover:text-slate-900">
                        <i class="bi bi-bell text-xl"></i>
                        <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <!-- User Menu -->
                    <div class="relative">
                        <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-3 hover:bg-gray-100 rounded-lg px-3 py-2 transition">
                            <div class="w-9 h-9 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-medium text-slate-800">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500">{{ auth()->user()->getRoleNames()->first() ?? 'Usuario' }}</p>
                            </div>
                            <i class="bi bi-chevron-down text-slate-600"></i>
                        </button>

                        <!-- Dropdown -->
                        <div 
                            x-show="userMenuOpen" 
                            @click.away="userMenuOpen = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50"
                        >
                            <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-gray-100">
                                <i class="bi bi-person mr-2"></i> Mi Perfil
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-gray-100">
                                <i class="bi bi-gear mr-2"></i> Configuración
                            </a>
                            <hr class="my-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i class="bi bi-box-arrow-right mr-2"></i> Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-4 lg:p-8">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="bi bi-check-circle text-green-500 text-xl mr-3"></i>
                            <p class="text-green-700 font-medium">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="text-green-500 hover:text-green-700">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="bi bi-exclamation-circle text-red-500 text-xl mr-3"></i>
                            <p class="text-red-700 font-medium">{{ session('error') }}</p>
                        </div>
                        <button @click="show = false" class="text-red-500 hover:text-red-700">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>