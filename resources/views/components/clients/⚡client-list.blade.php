<?php

use Livewire\Component;
use App\Models\Client;

new class extends Component {
    public bool $showModal = false;
    public $clientId = null;

    public string $name = '';
    public string $document_number = '';
    public string $phone = '';
    public string $email = '';

    public function create()
    {
        $this->reset(['clientId', 'name', 'document_number', 'phone', 'email']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);
        $this->clientId = $client->id;
        $this->name = $client->name;
        $this->document_number = $client->document_number ?? '';
        $this->phone = $client->phone ?? '';
        $this->email = $client->email ?? '';

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'document_number' => 'nullable|string|max:15',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $data = [
            'name' => $this->name,
            'document_number' => $this->document_number,
            'phone' => $this->phone,
            'email' => $this->email,
        ];

        if ($this->clientId) {
            Client::where('id', $this->clientId)->update($data);
        } else {
            Client::create($data);
        }

        $this->showModal = false;
    }

    public function delete($id)
    {
        Client::where('id', $id)->delete();
    }

    public function with(): array
    {
        return [
            'clients' => Client::latest()->get(),
        ];
    }
}; ?>

<div class="p-6 bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Directorio de Clientes</h2>
        <button wire:click="create" class="bg-gray-900 text-white px-5 py-2 rounded-lg font-medium shadow hover:bg-gray-700 transition-colors">
            + Nuevo Cliente
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                    <th class="p-3 border-b dark:border-gray-700">Nombre / Razón Social</th>
                    <th class="p-3 border-b dark:border-gray-700">DNI / RUC</th>
                    <th class="p-3 border-b dark:border-gray-700">Teléfono</th>
                    <th class="p-3 border-b dark:border-gray-700 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-800 dark:text-gray-200">
                        <td class="p-3 font-bold">{{ $client->name }}</td>
                        <td class="p-3">{{ $client->document_number ?: '—' }}</td>
                        <td class="p-3">{{ $client->phone ?: '—' }}</td>
                        <td class="p-3 text-center flex justify-center gap-4">
                            <button wire:click="edit({{ $client->id }})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium transition">Editar</button>
                            <button wire:click="delete({{ $client->id }})" class="text-red-600 hover:text-red-800 font-medium transition" onclick="confirm('¿Estás seguro de eliminar a este cliente?') || event.stopImmediatePropagation()">Eliminar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-gray-500 dark:text-gray-400">
                            Aún no hay clientes registrados.
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
                {{ $clientId ? 'Editar Cliente' : 'Registrar Cliente' }}
            </h3>

            <form wire:submit="save">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre Completo *</label>
                    <input type="text" wire:model="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">DNI o RUC</label>
                    <input type="text" wire:model="document_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono</label>
                    <input type="text" wire:model="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo Electrónico</label>
                    <input type="email" wire:model="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t dark:border-gray-700">
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow">
                        {{ $clientId ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
