<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>El Encuentro | ERP Gastronómico</title>

    <!-- Fuentes de Google -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,700,900" rel="stylesheet" />

    <!-- Tailwind CSS (Asegura que tu Vite esté corriendo) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 antialiased overflow-hidden selection:bg-orange-500 selection:text-white">

    <!-- FONDOS ABSTRACTOS / EFECTO GLOW -->
    <div class="absolute inset-0 z-0 flex items-center justify-center pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-orange-600/30 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30rem] h-[30rem] bg-red-600/20 rounded-full blur-[120px]"></div>
    </div>

    <!-- NAVEGACIÓN SUPERIOR -->
    <header class="relative z-50 w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
        <!-- Logo Textual -->
        <div class="flex items-center gap-2">
            <svg class="w-8 h-8 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z" />
            </svg>
            <span class="text-xl font-black tracking-tight text-white">EL ENCUENTRO</span>
        </div>

        <!-- Botones de Login / Auth -->
        @if (Route::has('login'))
            <nav class="flex gap-4 items-center">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-sm font-bold text-zinc-300 hover:text-white transition">Ir al Panel</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-bold text-zinc-300 hover:text-white transition">Iniciar Sesión</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-sm font-bold bg-white/10 hover:bg-white/20 border border-white/10 px-5 py-2 rounded-full transition backdrop-blur-md">
                            Registrarse
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    <!-- SECCIÓN PRINCIPAL (HERO) -->
    <main class="relative z-10 flex flex-col items-center justify-center min-h-[calc(100vh-100px)] text-center px-4">

        <!-- Etiqueta de versión -->
        <div class="mb-8 inline-flex items-center gap-2 px-4 py-2 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-400 text-sm font-medium backdrop-blur-sm shadow-xl">
            <span class="flex w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
            Sistema de Gestión Activo v2.0
        </div>

        <!-- Título Principal -->
        <h1 class="text-5xl md:text-7xl font-black tracking-tighter mb-6 text-transparent bg-clip-text bg-gradient-to-br from-white to-zinc-500 max-w-4xl">
            Controla tu restaurante con la <span class="bg-clip-text text-transparent bg-gradient-to-r from-orange-400 to-red-600">receta del éxito.</span>
        </h1>

        <!-- Subtítulo -->
        <p class="text-lg md:text-xl text-zinc-400 mb-10 max-w-2xl font-medium leading-relaxed">
            Olvídate del papel y el desorden. Toma pedidos al instante, controla tu inventario de insumos en tiempo real y analiza tus ganancias con un clic.
        </p>

        <!-- Botones de Acción Grandes -->
        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
            @auth
                <a href="{{ route('dashboard') }}" class="group relative flex justify-center items-center gap-2 bg-gradient-to-r from-orange-500 to-red-600 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-[0_0_40px_-10px_rgba(249,115,22,0.6)] hover:scale-105 transition-all duration-300">
                    Abrir Caja Registradora
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 group-hover:translate-x-1 transition-transform">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            @else
                <a href="{{ route('login') }}" class="group relative flex justify-center items-center gap-2 bg-gradient-to-r from-orange-500 to-red-600 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-[0_0_40px_-10px_rgba(249,115,22,0.6)] hover:scale-105 transition-all duration-300">
                    Ingresar al Sistema
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 group-hover:translate-x-1 transition-transform">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            @endauth
        </div>

        <!-- Mocks de características rápidos abajo -->
        <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-8 border-t border-zinc-800 pt-10 text-zinc-400">
            <div class="flex flex-col items-center gap-2">
                <svg class="w-6 h-6 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                <span class="text-sm font-semibold">Punto de Venta</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <svg class="w-6 h-6 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <span class="text-sm font-semibold">Stock en Vivo</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <svg class="w-6 h-6 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="text-sm font-semibold">Reportes PDF</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <svg class="w-6 h-6 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <span class="text-sm font-semibold">Sistema Seguro</span>
            </div>
        </div>

    </main>
</body>
</html>
