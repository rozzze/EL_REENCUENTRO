<?php

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

new class extends Component {
    public bool $showModal = false;
    public $userId = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'cajero';

    public function create()
    {
        $this->reset(['userId', 'name', 'email', 'password', 'role']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role ?? 'cajero';
        $this->password = ''; // La dejamos vacía por seguridad, solo se llena si quiere cambiarla

        $this->showModal = true;
    }

    public function save()
    {
        // Reglas de validación
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'role' => 'required|in:admin,cajero,almacenero',
        ];

        // Si es usuario nuevo, la contraseña es obligatoria. Si es edición, es opcional.
        if (!$this->userId || !empty($this->password)) {
            $rules['password'] = 'required|min:6';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        // Solo encriptamos y guardamos la contraseña si escribió una
        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->userId) {
            User::where('id', $this->userId)->update($data);
        } else {
            User::create($data);
        }

        $this->showModal = false;
    }

    public function delete($id)
    {
        // Evitamos que el usuario activo se elimine a sí mismo
        if (auth()->id() == $id) {
            session()->flash('error', 'No puedes eliminar tu propia cuenta.');
            return;
        }
        User::where('id', $id)->delete();
    }

    public function with(): array
    {
        return [
            // Listamos a todos los usuarios
            'users' => User::latest()->get(),
        ];
    }
}; ?>

<div class="p-6 bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Gestión de Personal</h2>
        <button wire:click="create" class="bg-gray-900 text-white px-5 py-2 rounded-lg font-medium shadow hover:bg-gray-700 transition-colors">
            + Nuevo Empleado
        </button>
    </div>

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg font-bold">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                    <th class="p-3 border-b dark:border-gray-700">Nombre</th>
                    <th class="p-3 border-b dark:border-gray-700">Correo (Usuario)</th>
                    <th class="p-3 border-b dark:border-gray-700 text-center">Rol</th>
                    <th class="p-3 border-b dark:border-gray-700 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-800 dark:text-gray-200">
                        <td class="p-3 font-bold">{{ $user->name }}</td>
                        <td class="p-3">{{ $user->email }}</td>
                        <td class="p-3 text-center">
                            @php
                                $color = match($user->role) {
                                    'admin' => 'bg-purple-100 text-purple-700',
                                    'almacenero' => 'bg-blue-100 text-blue-700',
                                    default => 'bg-green-100 text-green-700'
                                };
                            @endphp
                            <span class="{{ $color }} px-3 py-1 rounded-full text-xs font-black uppercase">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="p-3 text-center flex justify-center gap-4">
                            <button wire:click="edit({{ $user->id }})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium transition">Editar</button>
                            <button wire:click="delete({{ $user->id }})" class="text-red-600 hover:text-red-800 font-medium transition" onclick="confirm('¿Seguro que deseas eliminar este acceso?') || event.stopImmediatePropagation()">Eliminar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-gray-500 dark:text-gray-400">
                            No hay empleados registrados.
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
                {{ $userId ? 'Editar Empleado' : 'Registrar Nuevo Empleado' }}
            </h3>

            <form wire:submit="save">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre Completo *</label>
                    <input type="text" wire:model="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo Electrónico *</label>
                    <input type="email" wire:model="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Contraseña {{ $userId ? '(Dejar vacía para no cambiarla)' : '*' }}
                    </label>
                    <input type="password" wire:model="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nivel de Acceso (Rol) *</label>
                    <select wire:model="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <option value="cajero">Cajero (Solo Ventas)</option>
                        <option value="almacenero">Almacenero (Inventario y Compras)</option>
                        <option value="admin">Administrador (Control Total)</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t dark:border-gray-700">
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow">
                        {{ $userId ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
