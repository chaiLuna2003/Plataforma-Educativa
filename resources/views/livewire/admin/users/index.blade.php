<div class="flex w-full flex-col gap-6">
    {{-- Encabezado --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-cyan-600">
                Administración
            </p>

            <h1 class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">
                Gestión de usuarios
            </h1>

            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                Crea, consulta y controla el acceso de los usuarios.
            </p>
        </div>

        <a
            href="{{ route('admin.users.create') }}"
            wire:navigate
            class="inline-flex h-11 items-center justify-center gap-2 rounded-[10px] bg-[#102A56] px-5 text-sm font-semibold text-white transition hover:bg-[#173B72] focus:outline-none focus:ring-4 focus:ring-blue-500/20"
        >
            <svg
                class="size-5"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
                aria-hidden="true"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>

            Crear usuario
        </a>
    </div>

    {{-- Mensajes --}}
    @if (session('status'))
        <div class="rounded-[10px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    @error('account')
        <div class="rounded-[10px] border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $message }}
        </div>
    @enderror

    {{-- Filtros --}}
    <section class="rounded-[10px] border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="grid gap-4 md:grid-cols-[1fr_180px_180px_auto]">
            <div>
                <label for="search" class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Buscar
                </label>

                <input
                    wire:model.live.debounce.300ms="search"
                    id="search"
                    type="search"
                    placeholder="Nombre o correo electrónico"
                    class="h-11 w-full rounded-[10px] border border-zinc-300 bg-white px-4 text-sm text-zinc-900 outline-none transition placeholder:text-zinc-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                >
            </div>

            <div>
                <label for="role" class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Rol
                </label>

                <select
                    wire:model.live="role"
                    id="role"
                    class="h-11 w-full rounded-[10px] border border-zinc-300 bg-white px-3 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                >
                    <option value="all">Todos</option>
                    <option value="admin">Administradores</option>
                    <option value="estudiante">Estudiantes</option>
                </select>
            </div>

            <div>
                <label for="status" class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Estado
                </label>

                <select
                    wire:model.live="status"
                    id="status"
                    class="h-11 w-full rounded-[10px] border border-zinc-300 bg-white px-3 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                >
                    <option value="all">Todos</option>
                    <option value="active">Activos</option>
                    <option value="inactive">Inactivos</option>
                </select>
            </div>

            <button
                wire:click="resetFilters"
                type="button"
                class="mt-auto h-11 rounded-[10px] border border-zinc-300 px-4 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800"
            >
                Limpiar
            </button>
        </div>
    </section>

    {{-- Tabla --}}
    <section class="overflow-hidden rounded-[10px] border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] text-left">
                <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800/60">
                    <tr class="text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                        <th class="px-6 py-4 font-semibold">Usuario</th>
                        <th class="px-6 py-4 font-semibold">Rol</th>
                        <th class="px-6 py-4 font-semibold">Estado</th>
                        <th class="px-6 py-4 font-semibold">Registro</th>
                        <th class="px-6 py-4 text-right font-semibold">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="transition hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-[10px] bg-blue-100 text-sm font-bold text-[#102A56] dark:bg-blue-950 dark:text-blue-200">
                                        {{ $user->initials() }}
                                    </span>

                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-zinc-900 dark:text-white">
                                            {{ $user->name }}

                                            @if ($user->is(auth()->user()))
                                                <span class="ml-1 text-xs font-medium text-cyan-600">
                                                    Tú
                                                </span>
                                            @endif
                                        </p>

                                        <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $user->email }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 dark:bg-zinc-800 dark:text-zinc-200">
                                    {{ $user->isAdmin() ? 'Administrador' : 'Estudiante' }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                @if ($user->isActive())
                                    <span class="inline-flex items-center gap-2 text-sm font-medium text-emerald-600">
                                        <span class="size-2 rounded-full bg-emerald-500"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 text-sm font-medium text-red-600">
                                        <span class="size-2 rounded-full bg-red-500"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                @if (! $user->is(auth()->user()))
                                    <button
                                        wire:click="toggleStatus({{ $user->id }})"
                                        wire:confirm="¿Confirmas que deseas {{ $user->isActive() ? 'desactivar' : 'activar' }} esta cuenta?"
                                        type="button"
                                        class="text-sm font-semibold {{ $user->isActive() ? 'text-red-600 hover:text-red-700' : 'text-emerald-600 hover:text-emerald-700' }}"
                                    >
                                        {{ $user->isActive() ? 'Desactivar' : 'Activar' }}
                                    </button>
                                @else
                                    <span class="text-xs text-zinc-400">
                                        Cuenta actual
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-14 text-center">
                                <p class="font-semibold text-zinc-700 dark:text-zinc-200">
                                    No encontramos usuarios
                                </p>

                                <p class="mt-1 text-sm text-zinc-500">
                                    Cambia los filtros o crea una nueva cuenta.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-zinc-200 px-6 py-4 dark:border-zinc-700">
                {{ $users->links() }}
            </div>
        @endif
    </section>
</div>