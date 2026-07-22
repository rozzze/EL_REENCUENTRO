<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrarse | El Encuentro</title>

    <!-- Fuentes de Google -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,700,900" rel="stylesheet" />

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 antialiased selection:bg-orange-500 selection:text-white flex items-center justify-center min-h-screen overflow-x-hidden relative py-10">

    <!-- FONDOS ABSTRACTOS / EFECTO GLOW -->
    <div class="fixed inset-0 z-0 flex items-center justify-center pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-orange-600/30 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30rem] h-[30rem] bg-red-600/20 rounded-full blur-[120px]"></div>
    </div>

    <!-- TARJETA DE REGISTRO (GLASSMORPHISM) -->
    <div class="relative z-10 w-full max-w-md p-8 sm:p-10 bg-zinc-900/50 backdrop-blur-xl border border-zinc-800/80 rounded-3xl shadow-2xl mx-4">

        <!-- HEADER DEL FORMULARIO -->
        <div class="flex flex-col items-center mb-8 text-center">
            <svg class="w-12 h-12 text-orange-500 mb-4 drop-shadow-[0_0_15px_rgba(249,115,22,0.5)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
            </svg>
            <h2 class="text-3xl font-black text-white tracking-tight">Crear Cuenta</h2>
            <p class="text-zinc-400 text-sm mt-2">Ingresa tus datos para unirte al sistema</p>
        </div>

        <!-- Alertas de Sesión -->
        <x-auth-session-status class="mb-4 text-center text-sm font-bold text-green-400" :status="session('status')" />

        <!-- FORMULARIO -->
        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Nombre -->
            <div>
                <label for="name" class="block text-sm font-medium text-zinc-300 mb-1.5">Nombre Completo</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Ej. Juan Pérez"
                    class="w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-xl px-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all placeholder:text-zinc-600">
                @error('name')
                    <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-zinc-300 mb-1.5">Correo Electrónico</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" placeholder="correo@ejemplo.com"
                    class="w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-xl px-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all placeholder:text-zinc-600">
                @error('email')
                    <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <!-- Contraseña -->
            <div>
                <label for="password" class="block text-sm font-medium text-zinc-300 mb-1.5">Contraseña</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="••••••••"
                    class="w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-xl px-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all placeholder:text-zinc-600">
                @error('password')
                    <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirmar Contraseña -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-zinc-300 mb-1.5">Confirmar Contraseña</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="••••••••"
                    class="w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-xl px-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all placeholder:text-zinc-600">
                @error('password_confirmation')
                    <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <!-- Botón Submit -->
            <button type="submit" class="mt-4 w-full bg-gradient-to-r from-orange-500 to-red-600 text-white py-3.5 rounded-xl font-bold text-lg shadow-[0_0_20px_-5px_rgba(249,115,22,0.5)] hover:scale-[1.02] hover:shadow-[0_0_25px_-5px_rgba(249,115,22,0.6)] transition-all duration-300">
                Crear Cuenta
            </button>
        </form>

        <!-- Link a Login -->
        <div class="mt-8 text-center text-sm text-zinc-500">
            ¿Ya tienes una cuenta?
            <a href="{{ route('login') }}" class="text-white hover:text-orange-400 font-bold transition">Inicia sesión aquí</a>
        </div>
    </div>

    <!-- Botón Volver (Flotante) -->
    <a href="{{ url('/') }}" class="fixed top-6 left-6 z-50 flex items-center gap-2 text-zinc-500 hover:text-white transition font-medium text-sm bg-zinc-900/50 px-4 py-2 rounded-full backdrop-blur-md border border-zinc-800/50">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Inicio
    </a>

</body>
</html>
