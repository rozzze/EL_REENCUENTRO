<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-950 text-zinc-100 antialiased selection:bg-orange-500 selection:text-white flex">

        <!-- FONDOS ABSTRACTOS / EFECTO GLOW (Fijos de fondo) -->
        <div class="fixed inset-0 z-0 flex items-center justify-center pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-orange-600/20 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[30rem] h-[30rem] bg-red-600/10 rounded-full blur-[120px]"></div>
        </div>

        <!-- SIDEBAR (Efecto Glassmorphism) -->
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-800/80 bg-zinc-900/40 backdrop-blur-2xl z-20 shadow-2xl">

            <!-- HEADER DEL SIDEBAR (NUEVO LOGO PERSONALIZADO) -->
            <flux:sidebar.header class="flex items-center justify-between pb-4 border-b border-zinc-800/50">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 outline-none group">
                    <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-br from-orange-500 to-red-600 shadow-[0_0_15px_-3px_rgba(249,115,22,0.5)] group-hover:scale-105 group-hover:shadow-[0_0_20px_-3px_rgba(249,115,22,0.7)] transition-all duration-300">
                        <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z" />
                        </svg>
                    </div>
                    <span class="text-xl font-black tracking-tight text-white drop-shadow-md">
                        EL ENCUENTRO
                    </span>
                </a>
                <flux:sidebar.collapse class="lg:hidden text-zinc-400 hover:text-white" />
            </flux:sidebar.header>

            <flux:sidebar.group :heading="__('Plataforma')" class="grid mt-4">

                <!-- Inventario (Admin, Almacenero) -->
                @if(in_array(auth()->user()->role, ['admin', 'almacenero']))
                    <flux:sidebar.item icon="home" :href="route('inventario')" :current="request()->routeIs('inventario')" wire:navigate>
                        Inventario
                    </flux:sidebar.item>
                @endif

                <!-- Punto de Venta (Admin, Cajero) -->
                @if(in_array(auth()->user()->role, ['admin', 'cajero']))
                    <flux:sidebar.item icon="shopping-bag" href="/pos" :current="request()->is('pos')" wire:navigate>
                        Punto de Venta
                    </flux:sidebar.item>
                @endif

                <!-- Proveedores (Admin, Almacenero) -->
                @if(in_array(auth()->user()->role, ['admin', 'almacenero']))
                    <flux:sidebar.item icon="truck" href="/proveedores" :current="request()->is('proveedores')" wire:navigate>
                        Proveedores
                    </flux:sidebar.item>
                @endif

                <!-- Compras (Admin, Almacenero) -->
                @if(in_array(auth()->user()->role, ['admin', 'almacenero']))
                    <flux:sidebar.item icon="document-plus" href="/compras" :current="request()->is('compras')" wire:navigate>
                        Compras
                    </flux:sidebar.item>
                @endif

                <!-- Reportes (Solo Admin) -->
                @if(auth()->user()->role === 'admin')
                    <flux:sidebar.item icon="chart-bar" href="/reportes" :current="request()->is('reportes')" wire:navigate>
                        Reportes
                    </flux:sidebar.item>
                @endif

                <!-- Historial de Ventas (Solo Admin) -->
                @if(auth()->user()->role === 'admin')
                    <flux:sidebar.item icon="clipboard-document-list" href="/historial-ventas" :current="request()->is('historial-ventas')" wire:navigate>
                        Historial de Ventas
                    </flux:sidebar.item>
                @endif

                <!-- Empleados (Solo Admin) -->
                @if(auth()->user()->role === 'admin')
                    <flux:sidebar.item icon="users" href="/empleados" :current="request()->is('empleados')" wire:navigate>
                        Empleados
                    </flux:sidebar.item>
                @endif

                <!-- Clientes (Admin, Cajero) -->
                @if(in_array(auth()->user()->role, ['admin', 'cajero']))
                    <flux:sidebar.item icon="user-group" href="/clientes" :current="request()->is('clientes')" wire:navigate>
                        Clientes
                    </flux:sidebar.item>
                @endif

            </flux:sidebar.group>

            <flux:spacer />

            <!-- LINKS DE SOPORTE -->
            <flux:sidebar.nav>
                <flux:sidebar.item icon="question-mark-circle" href="#">
                    Centro de Ayuda
                </flux:sidebar.item>

                <flux:sidebar.item icon="chat-bubble-left-right" href="#">
                    Soporte Técnico
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block border-t border-zinc-800/50 pt-4 mt-2" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- HEADER MÓVIL (Glassmorphism) -->
        <flux:header class="lg:hidden border-b border-zinc-800/80 bg-zinc-900/40 backdrop-blur-2xl z-20">
            <flux:sidebar.toggle class="lg:hidden text-zinc-300 hover:text-white" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                    class="text-zinc-300 hover:text-white"
                />

                <flux:menu class="bg-zinc-900 border border-zinc-800 shadow-xl">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm text-zinc-200">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate font-bold text-white">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate text-zinc-400">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator class="bg-zinc-800/50" />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate class="hover:bg-zinc-800 hover:text-white text-zinc-300">
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator class="bg-zinc-800/50" />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer hover:bg-red-900/30 hover:text-red-400 text-zinc-300"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <!-- CONTENEDOR PRINCIPAL ($slot) -->
        <main class="relative z-10 flex-1 w-full flex flex-col min-h-screen">
            {{ $slot }}
        </main>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
