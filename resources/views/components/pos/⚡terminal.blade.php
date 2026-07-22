<?php

use Livewire\Component;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public array $cart = [];
    public $client_id = ''; // Variable para almacenar al cliente seleccionado

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        if (!$product || $product->stock <= 0) return;

        if (isset($this->cart[$productId])) {
            // Validamos que no supere el stock disponible
            if ($this->cart[$productId]['quantity'] < $product->stock) {
                $this->cart[$productId]['quantity']++;
            }
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'stock' => $product->stock,
            ];
        }
    }

    public function increaseQty($productId)
    {
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] < $this->cart[$productId]['stock']) {
                $this->cart[$productId]['quantity']++;
            }
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
        $this->client_id = ''; // Limpiamos también al cliente
    }

    public function getTotalProperty()
    {
        return array_reduce($this->cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);
    }

    public function checkout()
    {
        if (empty($this->cart)) return;

        $orderId = null;

        DB::transaction(function () use (&$orderId) {
            $order = Order::create([
                'total' => $this->total,
                'status' => 'completado',
                'client_id' => empty($this->client_id) ? null : $this->client_id,
            ]);

            $orderId = $order->id;

            foreach ($this->cart as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                ]);

                Product::where('id', $item['id'])->decrement('stock', $item['quantity']);
            }
        });

        // Magia: Abrimos el ticket en una nueva pestaña
        $this->js("window.open('/ticket/{$orderId}', '_blank')");

        $this->clearCart();
        session()->flash('message', '¡Venta registrada y Ticket generado!');
    }

    public function with(): array
    {
        return [
            // Solo mostramos productos que tengan al menos 1 en stock
            'products' => Product::where('stock', '>', 0)->get(),
            // Traemos todos los clientes ordenados alfabéticamente
            'clients' => Client::orderBy('name')->get(),
        ];
    }
}; ?>

<div class="flex h-full gap-4">
    <!-- PANEL IZQUIERDO: PRODUCTOS -->
    <div class="w-2/3 bg-white dark:bg-gray-900 rounded-xl p-6 border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-y-auto h-[calc(100vh-10rem)]">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100">Punto de Venta</h2>

        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg font-bold shadow-sm">
                {{ session('message') }}
            </div>
        @endif

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($products as $product)
                <div wire:click="addToCart({{ $product->id }})" class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-700 transition shadow-sm flex flex-col items-center text-center">
                    <span class="font-bold text-gray-800 dark:text-gray-200 block text-lg mb-1">{{ $product->name }}</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ $product->category }}</span>
                    <span class="text-blue-600 dark:text-blue-400 font-black text-xl">S/ {{ number_format($product->price, 2) }}</span>
                    <span class="text-xs text-gray-400 mt-2">Stock: {{ $product->stock }}</span>
                </div>
            @empty
                <div class="col-span-4 text-center text-gray-500 py-10">
                    No hay productos con stock disponible.
                </div>
            @endforelse
        </div>
    </div>

    <!-- PANEL DERECHO: TICKET DE VENTA -->
    <div class="w-1/3 bg-white dark:bg-gray-900 rounded-xl p-6 border border-neutral-200 dark:border-neutral-700 shadow-sm flex flex-col h-[calc(100vh-10rem)]">

        <!-- CABECERA: CLIENTE Y LIMPIAR -->
        <div class="mb-4 border-b pb-4 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4 flex justify-between items-center">
                Orden Actual
                @if(!empty($cart))
                    <button wire:click="clearCart" class="text-sm font-bold text-red-500 hover:text-red-700 transition flex items-center gap-1">
                        Vaciar
                    </button>
                @endif
            </h2>

            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cliente (Opcional)</label>
            <select wire:model="client_id" class="w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">-- Público en General --</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }} {{ $client->document_number ? '('.$client->document_number.')' : '' }}</option>
                @endforeach
            </select>
        </div>

        <!-- LISTA DE PRODUCTOS EN EL CARRITO -->
        <div class="flex-1 overflow-y-auto mb-4 pr-2">
            @forelse($cart as $id => $item)
                <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg mb-2 border border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-start mb-2">
                        <div class="font-bold text-gray-800 dark:text-gray-200 leading-tight">{{ $item['name'] }}</div>
                        <div class="font-black text-gray-800 dark:text-gray-200 whitespace-nowrap ml-2">S/ {{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500 dark:text-gray-400">S/ {{ number_format($item['price'], 2) }} c/u</span>
                        <div class="flex items-center gap-2">
                            <button wire:click="decreaseQty({{ $id }})" class="bg-red-100 text-red-600 w-7 h-7 rounded-full font-bold hover:bg-red-200 flex items-center justify-center">-</button>
                            <span class="font-bold dark:text-white text-sm w-4 text-center">{{ $item['quantity'] }}</span>
                            <button wire:click="increaseQty({{ $id }})" class="bg-green-100 text-green-600 w-7 h-7 rounded-full font-bold hover:bg-green-200 flex items-center justify-center">+</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-400 mt-10 flex flex-col items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mb-2 opacity-50">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                    Añade platos para cobrar
                </div>
            @endforelse
        </div>

        <!-- SECCIÓN DE COBRO -->
        <div class="border-t pt-4 dark:border-gray-700">
            <div class="flex justify-between text-2xl font-black mb-6 text-gray-800 dark:text-white">
                <span>Total:</span>
                <span class="text-blue-600 dark:text-blue-400">S/ {{ number_format($this->total, 2) }}</span>
            </div>

            <button wire:click="checkout" class="w-full bg-blue-600 text-white font-bold text-xl py-4 rounded-xl shadow-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed" @if(empty($cart)) disabled @endif>
                Cobrar Pedido
            </button>
        </div>
    </div>
</div>
