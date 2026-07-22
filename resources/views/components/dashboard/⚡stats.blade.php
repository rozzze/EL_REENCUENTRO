<?php

use Livewire\Component;
use App\Models\Order;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public function with(): array
    {
        $hoy = now()->toDateString();

        // 1. Sumar todas las ventas de hoy
        $ventasHoy = Order::whereDate('created_at', $hoy)
                        ->where('status', 'completado')
                        ->sum('total');

        // 2. Sumar todas las compras a proveedores de hoy
        $comprasHoy = Purchase::whereDate('created_at', $hoy)
                           ->where('status', 'completado')
                           ->sum('total');

        // 3. Balance del día (Ganancia o Pérdida)
        $balance = $ventasHoy - $comprasHoy;

        // 4. Ranking de los 5 productos más vendidos históricamente
        $topProductos = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->select('products.name', 'products.category', DB::raw('SUM(order_details.quantity) as total_vendido'))
            ->groupBy('products.id', 'products.name', 'products.category')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get();

        return [
            'ventasHoy' => $ventasHoy,
            'comprasHoy' => $comprasHoy,
            'balance' => $balance,
            'topProductos' => $topProductos,
        ];
    }
}; ?>

<div class="space-y-6">
    <!-- CABECERA -->
    <div>
        <h2 class="text-3xl font-black text-gray-800 dark:text-gray-100">Resumen del Día</h2>
        <p class="text-gray-500 dark:text-gray-400">Estadísticas en tiempo real de tu negocio.</p>
    </div>

    <!-- TARJETAS DE INDICADORES -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Tarjeta de Ingresos -->
        <div class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-neutral-200 dark:border-neutral-700 shadow-sm flex items-center gap-4">
            <div class="p-4 bg-green-100 text-green-600 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ventas de Hoy</p>
                <p class="text-3xl font-bold text-gray-800 dark:text-white">S/ {{ number_format($ventasHoy, 2) }}</p>
            </div>
        </div>

        <!-- Tarjeta de Egresos -->
        <div class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-neutral-200 dark:border-neutral-700 shadow-sm flex items-center gap-4">
            <div class="p-4 bg-red-100 text-red-600 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Compras (Gastos)</p>
                <p class="text-3xl font-bold text-gray-800 dark:text-white">S/ {{ number_format($comprasHoy, 2) }}</p>
            </div>
        </div>

        <!-- Tarjeta de Balance -->
        <div class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-neutral-200 dark:border-neutral-700 shadow-sm flex items-center gap-4">
            <div class="p-4 rounded-full {{ $balance >= 0 ? 'bg-blue-100 text-blue-600' : 'bg-orange-100 text-orange-600' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Balance Neto</p>
                <p class="text-3xl font-bold {{ $balance >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-orange-600 dark:text-orange-400' }}">
                    S/ {{ number_format($balance, 2) }}
                </p>
            </div>
        </div>
    </div>

    <!-- RANKING DE PRODUCTOS -->
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-neutral-200 dark:border-neutral-700">
            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">Top 5 Platos Más Vendidos</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="p-4 font-medium">Posición</th>
                        <th class="p-4 font-medium">Producto</th>
                        <th class="p-4 font-medium">Categoría</th>
                        <th class="p-4 font-medium text-right">Cantidad Vendida</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($topProductos as $index => $producto)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="p-4">
                                <span class="bg-blue-100 text-blue-700 font-bold px-3 py-1 rounded-full text-sm">
                                    #{{ $index + 1 }}
                                </span>
                            </td>
                            <td class="p-4 font-bold text-gray-800 dark:text-gray-200">{{ $producto->name }}</td>
                            <td class="p-4 text-gray-600 dark:text-gray-400">{{ $producto->category }}</td>
                            <td class="p-4 text-right font-bold text-gray-800 dark:text-white">{{ $producto->total_vendido }} unid.</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-500">
                                Aún no hay ventas registradas para armar el ranking.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- GENERADOR DE REPORTES PDF -->
    <div class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-neutral-200 dark:border-neutral-700 shadow-sm mt-6">
        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-blue-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            Generador de Reportes PDF
        </h3>
        <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm">Selecciona el tipo de informe que deseas exportar para auditoría o control contable. El documento se generará en formato A4 listo para impresión.</p>

        <!-- Usamos un form tradicional con GET para abrir en otra pestaña -->
        <form action="{{ route('reportes.generar') }}" method="GET" target="_blank" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Seleccionar Tipo de Datos</label>
                <select name="tipo" class="w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    <option value="productos">Balance de Ventas por Producto</option>
                    <option value="proveedores">Historial de Compras por Proveedor</option>
                </select>
            </div>

            <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-8 py-2.5 rounded-lg font-bold shadow hover:bg-blue-700 transition-colors flex justify-center items-center gap-2">
                Generar Documento
            </button>
        </form>
    </div>
</div>
