<?php

use Livewire\Component;
use App\Models\Supplier;

new class extends Component {
    public bool $showModal = false;
    public $supplierId = null;

    public string $name = '';
    public string $contact_person = '';
    public string $phone = '';
    public string $email = '';
    public string $address = '';

    public function create()
    {
        $this->reset(['supplierId', 'name', 'contact_person', 'phone', 'email', 'address']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        $this->supplierId = $supplier->id;
        $this->name = $supplier->name;
        $this->contact_person = $supplier->contact_person ?? '';
        $this->phone = $supplier->phone ?? '';
        $this->email = $supplier->email ?? '';
        $this->address = $supplier->address ?? '';

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $data = [
            'name' => $this->name,
            'contact_person' => $this->contact_person,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
        ];

        if ($this->supplierId) {
            Supplier::where('id', $this->supplierId)->update($data);
        } else {
            Supplier::create($data);
        }

        $this->showModal = false;
    }

    public function delete($id)
    {
        Supplier::where('id', $id)->delete();
    }

    public function with(): array
    {
        return [
            'suppliers' => Supplier::latest()->get(),
        ];
    }
}; ?>

<div class="p-6 bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Directorio de Proveedores</h2>
        <button wire:click="create" class="bg-gray-900 text-white px-5 py-2 rounded-lg font-medium shadow hover:bg-gray-700 transition-colors">
            + Nuevo Proveedor
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                    <th class="p-3 border-b dark:border-gray-700">Empresa</th>
                    <th class="p-3 border-b dark:border-gray-700">Contacto</th>
                    <th class="p-3 border-b dark:border-gray-700">Teléfono</th>
                    <th class="p-3 border-b dark:border-gray-700">Correo</th>
                    <th class="p-3 border-b dark:border-gray-700 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-800 dark:text-gray-200">
                        <td class="p-3 font-bold">{{ $supplier->name }}</td>
                        <td class="p-3">{{ $supplier->contact_person ?: '—' }}</td>
                        <td class="p-3">{{ $supplier->phone ?: '—' }}</td>
                        <td class="p-3">{{ $supplier->email ?: '—' }}</td>
                        <td class="p-3 text-center flex justify-center gap-4">
                            <button wire:click="edit({{ $supplier->id }})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium transition">Editar</button>
                            <button wire:click="delete({{ $supplier->id }})" class="text-red-600 hover:text-red-800 font-medium transition" onclick="confirm('¿Estás seguro de eliminar este proveedor?') || event.stopImmediatePropagation()">Eliminar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500 dark:text-gray-400">
                            Aún no hay proveedores registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MODAL -->
    @if($showModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-2xl w-full max-w-md border border-neutral-200 dark:border-neutral-700">
            <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">
                {{ $supplierId ? 'Editar Proveedor' : 'Registrar Proveedor' }}
            </h3>

            <form wire:submit="save">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre de la Empresa *</label>
                    <input type="text" wire:model="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre del Contacto (Opcional)</label>
                    <input type="text" wire:model="contact_person" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono (Opcional)</label>
                        <input type="text" wire:model="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo (Opcional)</label>
                        <input type="email" wire:model="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dirección (Opcional)</label>
                    <input type="text" wire:model="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t dark:border-gray-700">
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow">
                        {{ $supplierId ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
