<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión | El Encuentro</title>

    <!-- Fuentes de Google -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,700,900" rel="stylesheet" />

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 antialiased overflow-hidden selection:bg-orange-500 selection:text-white flex items-center justify-center min-h-screen">

    <!-- FONDOS ABSTRACTOS / EFECTO GLOW -->
    <div class="absolute inset-0 z-0 flex items-center justify-center pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-orange-600/30 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30rem] h-[30rem] bg-red-600/20 rounded-full blur-[120px]"></div>
    </div>

    <!-- TARJETA DE LOGIN (GLASSMORPHISM) -->
    <div class="relative z-10 w-full max-w-md p-8 sm:p-10 bg-zinc-900/50 backdrop-blur-xl border border-zinc-800/80 rounded-3xl shadow-2xl mx-4">

        <!-- HEADER DEL FORMULARIO -->
        <div class="flex flex-col items-center mb-8 text-center">
            <svg class="w-12 h-12 text-orange-500 mb-4 drop-shadow-[0_0_15px_rgba(249,115,22,0.5)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z" />
            </svg>
            <h2 class="text-3xl font-black text-white tracking-tight">Bienvenido</h2>
            <p class="text-zinc-400 text-sm mt-2">Ingresa tus credenciales para continuar</p>
        </div>

        <!-- Alertas de Sesión -->
        <x-auth-session-status class="mb-4 text-center text-sm font-bold text-green-400" :status="session('status')" />

        <!-- Componente nativo de Passkey del Starter Kit -->
        <x-passkey-verify />

        <!-- FORMULARIO -->
        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-zinc-300 mb-1.5">Correo Electrónico</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="admin@elencuentro.com"
                    class="w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-xl px-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all placeholder:text-zinc-600">
                @error('email')
                    <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <!-- Contraseña -->
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label for="password" class="block text-sm font-medium text-zinc-300">Contraseña</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs text-orange-500 hover:text-orange-400 font-medium transition">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>
                <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="••••••••"
                    class="w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-xl px-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all placeholder:text-zinc-600">
                @error('password')
                    <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <!-- Recordarme -->
            <div class="flex items-center mt-1">
                <input id="remember" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                    class="w-4 h-4 rounded bg-zinc-950 border-zinc-700 text-orange-500 focus:ring-orange-500/50 focus:ring-offset-zinc-900 transition cursor-pointer">
                <label for="remember" class="ms-2 text-sm text-zinc-400 cursor-pointer select-none">Mantener sesión iniciada</label>
            </div>

            <!-- Botón Submit -->
            <button type="submit" class="mt-4 w-full bg-gradient-to-r from-orange-500 to-red-600 text-white py-3.5 rounded-xl font-bold text-lg shadow-[0_0_20px_-5px_rgba(249,115,22,0.5)] hover:scale-[1.02] hover:shadow-[0_0_25px_-5px_rgba(249,115,22,0.6)] transition-all duration-300">
                Iniciar Sesión
            </button>
        </form>

        <!-- Link a Registro -->
        @if (Route::has('register'))
            <div class="mt-8 text-center text-sm text-zinc-500">
                ¿No tienes una cuenta?
                <a href="{{ route('register') }}" class="text-white hover:text-orange-400 font-bold transition">Regístrate aquí</a>
            </div>
        @endif
    </div>

    <!-- Botón Volver (Flotante) -->
    <a href="{{ url('/') }}" class="absolute top-6 left-6 flex items-center gap-2 text-zinc-500 hover:text-white transition font-medium text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Volver al inicio
    </a>

</body>
</html>
