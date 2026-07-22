<?php

use Livewire\Component;
use App\Models\Product;

new class extends Component {
    public bool $showModal = false;
    public $productId = null;

    public string $name = '';
    public string $category = '';
    public $price = 0; // NUEVO: Variable para el precio
    public int $stock = 0;
    public int $min_stock = 10;
    public string $expiration_date = '';

    public function create()
    {
        $this->reset(['productId', 'name', 'category', 'price', 'stock', 'min_stock', 'expiration_date']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->category = $product->category;
        $this->price = $product->price; // NUEVO: Cargar el precio
        $this->stock = $product->stock;
        $this->min_stock = $product->min_stock;
        $this->expiration_date = $product->expiration_date ?? '';

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0', // NUEVO: Validación del precio
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'expiration_date' => 'nullable|date',
        ]);

        $data = [
            'name' => $this->name,
            'category' => $this->category,
            'price' => $this->price, // NUEVO: Guardar el precio
            'stock' => $this->stock,
            'min_stock' => $this->min_stock,
            'expiration_date' => $this->expiration_date ?: null,
        ];

        if ($this->productId) {
            Product::where('id', $this->productId)->update($data);
        } else {
            $data['status'] = 'activo';
            Product::create($data);
        }

        $this->showModal = false;
    }

    public function changeStatus($id, $status)
    {
        Product::where('id', $id)->update(['status' => $status]);
    }

    public function with(): array
    {
        Product::whereNotNull('expiration_date')
            ->where('status', 'activo')
            ->whereDate('expiration_date', '<', now()->toDateString())
            ->update(['status' => 'vencido']);

        Product::whereNotNull('expiration_date')
            ->where('status', 'vencido')
            ->whereDate('expiration_date', '>=', now()->toDateString())
            ->update(['status' => 'activo']);

        return [
            'products' => Product::latest()->get(),
        ];
    }
}; ?>

<div class="p-6 bg-white dark:bg-gray-900 rounded-lg">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Inventario de Productos</h2>
        <button wire:click="create" class="bg-gray-900 text-white px-5 py-2 rounded-lg font-medium shadow hover:bg-gray-700 transition-colors">
            + Nuevo Producto
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                    <th class="p-3 border-b dark:border-gray-700">Nombre</th>
                    <th class="p-3 border-b dark:border-gray-700">Categoría</th>
                    <th class="p-3 border-b dark:border-gray-700 text-center">Precio</th>
                    <th class="p-3 border-b dark:border-gray-700 text-center">Stock</th>
                    <th class="p-3 border-b dark:border-gray-700 text-center">Estado</th>
                    <th class="p-3 border-b dark:border-gray-700 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-800 dark:text-gray-200">
                        <td class="p-3">{{ $product->name }}</td>
                        <td class="p-3">{{ $product->category }}</td>
                        <td class="p-3 text-center font-bold text-blue-600 dark:text-blue-400">S/ {{ number_format($product->price, 2) }}</td>
                        <td class="p-3 text-center">
                            <span class="{{ $product->stock <= $product->min_stock ? 'bg-red-100 text-red-700 px-2 py-1 rounded font-bold' : 'text-green-600 dark:text-green-400' }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            @php
                                $color = match($product->status) {
                                    'activo' => 'bg-green-100 text-green-700',
                                    'vencido' => 'bg-red-100 text-red-700',
                                    'malogrado' => 'bg-orange-100 text-orange-700',
                                    default => 'bg-gray-200 text-gray-700'
                                };
                            @endphp
                            <span class="{{ $color }} px-2 py-1 rounded text-xs font-bold uppercase">
                                {{ $product->status }}
                            </span>
                        </td>
                        <td class="p-3 text-center flex justify-center gap-2">
                            <button wire:click="edit({{ $product->id }})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">Editar</button>
                            @if($product->status == 'activo')
                                <button wire:click="changeStatus({{ $product->id }}, 'vencido')" class="text-red-600 hover:text-red-800 font-medium ml-2">Vencido</button>
                                <button wire:click="changeStatus({{ $product->id }}, 'malogrado')" class="text-orange-600 hover:text-orange-800 font-medium ml-2">Malogrado</button>
                            @else
                                <button wire:click="changeStatus({{ $product->id }}, 'activo')" class="text-green-600 hover:text-green-800 font-medium ml-2">Reactivar</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-gray-500 dark:text-gray-400">
                            No hay productos registrados en el inventario aún.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($showModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md">
            <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">
                {{ $productId ? 'Editar Producto' : 'Agregar Nuevo Producto' }}
            </h3>

            <form wire:submit="save">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                    <input type="text" wire:model="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoría</label>
                        <input type="text" wire:model="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio (S/)</label>
                        <input type="number" step="0.01" wire:model="price" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stock Actual</label>
                        <input type="number" wire:model="stock" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stock Mínimo</label>
                        <input type="number" wire:model="min_stock" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Vencimiento (Opcional)</label>
                    <input type="date" wire:model="expiration_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-white rounded hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        {{ $productId ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
