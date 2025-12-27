<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Unified Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 text-white rounded-xl mb-4">
                <i class="bi bi-shop text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-slate-800">Unified Manager</h1>
            <p class="text-slate-600 mt-2">Sistema ERP de Gestión Integral</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">Iniciar Sesión</h2>

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="bi bi-exclamation-circle text-red-600 text-xl mr-3 mt-0.5"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p class="text-sm text-red-700">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                        Correo Electrónico
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-slate-400">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            required 
                            autofocus
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="usuario@empresa.com"
                        >
                    </div>
                </div>

                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                        Contraseña
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-slate-400">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="••••••••"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-slate-600">Recordarme</span>
                    </label>
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-700">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-lg transition shadow-sm hover:shadow-md"
                >
                    <i class="bi bi-box-arrow-in-right mr-2"></i>
                    Iniciar Sesión
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-slate-600 text-sm mt-6">
            &copy; {{ date('Y') }} Unified Manager. Todos los derechos reservados.
        </p>
    </div>
</body>
</html>