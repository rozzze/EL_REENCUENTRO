<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ReportController;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    // 🚦 EL ENRUTADOR INTELIGENTE (Aquí caen todos al iniciar sesión)
    Route::get('/dashboard', function () {
        $rol = Auth::user()->role;

        // Si es cajero, lo mandamos directo a la caja
        if ($rol === 'cajero') {
            return redirect()->route('pos');
        }

        // Si es admin o almacenero, lo mandamos al inventario
        return redirect()->route('inventario');
    })->name('dashboard');

    // 1. INVENTARIO REAL: Administrador y Almacenero
    Route::view('/inventario', 'dashboard')
        ->middleware('role:admin,almacenero')
        ->name('inventario');

    // 2. POS (Ventas): Administrador y Cajero
    Route::get('/pos', function () {
        return view('pos-page');
    })->middleware('role:admin,cajero')
      ->name('pos');

    // 3. PROVEEDORES: Administrador y Almacenero
    Route::get('/proveedores', function () {
        return view('suppliers-page');
    })->middleware('role:admin,almacenero')
      ->name('proveedores');

    // 4. COMPRAS: Administrador y Almacenero
    Route::get('/compras', function () {
        return view('purchases-page');
    })->middleware('role:admin,almacenero')
      ->name('compras');

    // 5. REPORTES: Solo Administrador
    Route::get('/reportes', function () {
        return view('reports-page');
    })->middleware('role:admin')
      ->name('reportes');

    Route::get('/reportes/generar', [ReportController::class, 'generar'])
        ->middleware('role:admin')
        ->name('reportes.generar');

    // 6. EMPLEADOS: Solo Administrador
    Route::get('/empleados', function () {
        return view('employees-page');
    })->middleware('role:admin')
      ->name('empleados');

    // CLIENTES: Administrador y Cajero
    Route::get('/clientes', function () {
        return view('clients-page');
    })->middleware('role:admin,cajero')
      ->name('clientes');

    // RUTA PARA ABRIR EL TICKET PDF
    Route::get('/ticket/{id}', [App\Http\Controllers\ReportController::class, 'ticketVenta'])->middleware('auth')->name('ticket');

    // RUTA DEL HISTORIAL DE VENTAS (Solo Admin)
    Route::get('/historial-ventas', function () {
        return view('sales-history-page');
    })->middleware('role:admin')->name('historial-ventas');

});

require __DIR__.'/settings.php';
