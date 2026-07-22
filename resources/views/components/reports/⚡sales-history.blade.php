<?php

use Livewire\Component;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public $fechaInicio;
    public $fechaFin;
    public $clienteId = '';

    public function mount()
    {
        // Por defecto mostramos las ventas de hoy
        $this->fechaInicio = now()->toDateString();
        $this->fechaFin = now()->toDateString();
    }

    public function with(): array
    {
        $query = DB::table('orders')
            ->leftJoin('clients', 'orders.client_id', '=', 'clients.id')
            ->select('orders.*', 'clients.name as cliente_nombre', 'clients.document_number')
            ->orderByDesc('orders.created_at');

        if ($this->fechaInicio) {
            $query->whereDate('orders.created_at', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $query->whereDate('orders.created_at', '<=', $this->fechaFin);
        }
        if ($this->clienteId) {
            $query->where('orders.client_id', $this->clienteId);
        }

        $ventas = $query->get();

        return [
            'ventas' => $ventas,
            'totalFiltrado' => $ventas->sum('total'),
            'clientes' => Client::orderBy('name')->get(),
        ];
    }
}; ?>

<div class="p-6 bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">Registro de Ventas</h2>
        <p class="text-gray-500 dark:text-gray-400">Filtra y revisa el historial completo de ventas de tu negocio.</p>
    </div>

    <!-- PANEL DE FILTROS -->
    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 mb-6 flex flex-col md:flex-row gap-4 items-end">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Desde</label>
            <input type="date" wire:model.live="fechaInicio" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hasta</label>
            <input type="date" wire:model.live="fechaFin" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filtrar por Cliente</label>
            <select wire:model.live="clienteId" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">Todos los clientes</option>
                @foreach($clientes as $cli)
                    <option value="{{ $cli->id }}">{{ $cli->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="bg-blue-100 text-blue-700 px-6 py-2.5 rounded-lg font-black text-lg border border-blue-200">
            Total: S/ {{ number_format($totalFiltrado, 2) }}
        </div>
    </div>

    <!-- TABLA DE RESULTADOS -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                    <th class="p-3 border-b dark:border-gray-700">Ticket #</th>
                    <th class="p-3 border-b dark:border-gray-700">Fecha y Hora</th>
                    <th class="p-3 border-b dark:border-gray-700">Cliente</th>
                    <th class="p-3 border-b dark:border-gray-700 text-right">Monto</th>
                    <th class="p-3 border-b dark:border-gray-700 text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventas as $venta)
                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-800 dark:text-gray-200">
                        <td class="p-3 font-bold">#{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</td>
                        <td class="p-3">{{ \Carbon\Carbon::parse($venta->created_at)->format('d/m/Y h:i A') }}</td>
                        <td class="p-3">
                            @if($venta->cliente_nombre)
                                <span class="font-medium">{{ $venta->cliente_nombre }}</span>
                                <span class="text-xs text-gray-500 block">DNI: {{ $venta->document_number }}</span>
                            @else
                                <span class="text-gray-500 italic">Público General</span>
                            @endif
                        </td>
                        <td class="p-3 text-right font-black text-blue-600 dark:text-blue-400">S/ {{ number_format($venta->total, 2) }}</td>
                        <td class="p-3 text-center">
                            <a href="/ticket/{{ $venta->id }}" target="_blank" class="bg-gray-800 text-white px-3 py-1.5 rounded text-sm hover:bg-gray-600 transition">
                                Reimprimir
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500 dark:text-gray-400">
                            No se encontraron ventas con los filtros seleccionados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
