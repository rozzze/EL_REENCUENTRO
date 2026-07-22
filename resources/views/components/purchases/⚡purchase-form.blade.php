<?php

use Livewire\Component;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public array $cart = [];
    public $supplier_id = '';

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        if (!$product) return;

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'cost' => 0, // Inicia en 0 para que el usuario ponga el costo de la factura
                'quantity' => 1,
            ];
        }
    }

    public function increaseQty($productId)
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        }
    }

    public function decreaseQty($productId)
    {
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] > 1) {
                $this->cart[$productId]['quantity']--;
            } else {
                unset($this->cart[$productId]);
            }
        }
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->supplier_id = '';
    }

    // Calcula el total sumando (Costo * Cantidad)
    public function getTotalProperty()
    {
        return array_reduce($this->cart, function ($carry, $item) {
            $cost = is_numeric($item['cost']) ? $item['cost'] : 0;
            return $carry + ($cost * $item['quantity']);
        }, 0);
    }

    public function checkout()
    {
        // Validamos que sí o sí haya elegido un proveedor
        $this->validate([
            'supplier_id' => 'required',
        ], [
            'supplier_id.required' => 'Debes seleccionar un proveedor de la lista.',
        ]);

        if (empty($this->cart)) return;

        DB::transaction(function () {
            // 1. Guardamos la compra general
            $purchase = Purchase::create([
                'supplier_id' => $this->supplier_id,
                'total' => $this->total,
                'status' => 'completado',
            ]);

            // 2. Guardamos el detalle y SUMAMOS el stock
            foreach ($this->cart as $item) {
                $cost = is_numeric($item['cost']) ? $item['cost'] : 0;

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $cost,
                ]);

                // Aquí está la magia: increment suma al stock actual
                Product::where('id', $item['id'])->increment('stock', $item['quantity']);
            }
        });

        $this->clearCart();
        session()->flash('message', '¡Compra registrada y stock actualizado con éxito!');
    }

    public function with(): array
    {
        return [
            'products' => Product::all(), // Mostramos todos los productos para poder abastecerlos
            'suppliers' => Supplier::all(), // Cargamos los proveedores
        ];
    }
}; ?>

<div class="flex h-full gap-4">
    <!-- PANEL IZQUIERDO: PRODUCTOS A COMPRAR -->
    <div class="w-2/3 bg-white dark:bg-gray-900 rounded-xl p-6 border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-y-auto">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100">Catálogo de Insumos</h2>

        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg font-bold shadow-sm">
                {{ session('message') }}
            </div>
        @endif

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @forelse($products as $product)
                <div wire:click="addToCart({{ $product->id }})" class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-700 transition shadow-sm flex flex-col items-center text-center">
                    <span class="font-bold text-gray-800 dark:text-gray-200 block text-lg mb-1">{{ $product->name }}</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ $product->category }}</span>
                    <span class="text-xs text-blue-600 dark:text-blue-400 font-bold mt-2">Stock Actual: {{ $product->stock }}</span>
                </div>
            @empty
                <div class="col-span-3 text-center text-gray-500 py-10">
                    No hay productos registrados en el sistema.
                </div>
            @endforelse
        </div>
    </div>

    <!-- PANEL DERECHO: DETALLE DE FACTURA / ORDEN DE COMPRA -->
    <div class="w-1/3 bg-white dark:bg-gray-900 rounded-xl p-6 border border-neutral-200 dark:border-neutral-700 shadow-sm flex flex-col">

        <!-- CABECERA DE LA COMPRA -->
        <div class="mb-4 border-b pb-4 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4 flex justify-between items-center">
                Registro de Ingreso
                @if(!empty($cart))
                    <button wire:click="clearCart" class="text-sm font-bold text-red-500 hover:text-red-700 transition flex items-center gap-1">
                        Vaciar
                    </button>
                @endif
            </h2>

            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Proveedor *</label>
            <select wire:model="supplier_id" class="w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">-- Seleccionar Proveedor --</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
            @error('supplier_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- LISTA DE INGREDIENTES A INGRESAR -->
        <div class="flex-1 overflow-y-auto mb-4">
            @forelse($cart as $id => $item)
                <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg mb-2 border border-gray-200 dark:border-gray-700">
                    <div class="font-bold text-gray-800 dark:text-gray-200 mb-2">{{ $item['name'] }}</div>

                    <div class="flex justify-between items-center gap-2">
                        <!-- Input para el costo de compra -->
                        <div class="flex items-center gap-1">
                            <span class="text-sm text-gray-500 dark:text-gray-400">S/</span>
                            <input type="number" step="0.01" wire:model.live="cart.{{ $id }}.cost" class="w-20 rounded border-gray-300 p-1 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Costo">
                        </div>

                        <div class="flex items-center gap-2">
                            <button wire:click="decreaseQty({{ $id }})" class="bg-red-100 text-red-600 w-7 h-7 rounded-full font-bold hover:bg-red-200">-</button>
                            <span class="font-bold dark:text-white text-sm">{{ $item['quantity'] }}</span>
                            <button wire:click="increaseQty({{ $id }})" class="bg-green-100 text-green-600 w-7 h-7 rounded-full font-bold hover:bg-green-200">+</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-400 mt-10">Agrega productos para registrar la compra.</div>
            @endforelse
        </div>

        <!-- SECCIÓN DE GUARDADO -->
        <div class="border-t pt-4 dark:border-gray-700">
            <div class="flex justify-between text-2xl font-black mb-6 text-gray-800 dark:text-white">
                <span>Total Gasto:</span>
                <span class="text-red-500">S/ {{ number_format($this->total, 2) }}</span>
            </div>

            <button wire:click="checkout" class="w-full bg-gray-900 text-white font-bold text-xl py-4 rounded-xl shadow-lg hover:bg-gray-700 transition disabled:opacity-50 disabled:cursor-not-allowed" @if(empty($cart)) disabled @endif>
                Procesar Compra
            </button>
        </div>
    </div>
</div>
