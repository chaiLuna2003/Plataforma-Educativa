<x-layouts.app title="Gestión de planes">
    <div class="flex w-full flex-col gap-6">
        {{-- Encabezado --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-cyan-600">
                    Administración
                </p>

                <h1 class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">
                    Gestión de planes
                </h1>

                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                    Define qué cursos puede consultar cada tipo de usuario.
                </p>
            </div>

            <a
                href="{{ route('admin.planes.create') }}"
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

                Crear plan
            </a>
        </div>

        {{-- Mensajes --}}
        @if (session('success'))
            <div class="rounded-[10px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-[10px] border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        {{-- Resumen --}}
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-[10px] border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Total de planes
                </p>

                <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">
                    {{ $planes->total() }}
                </p>
            </div>

            <div class="rounded-[10px] border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Activos en esta página
                </p>

                <p class="mt-2 text-3xl font-bold text-emerald-600">
                    {{ $planes->where('estado', 'activo')->count() }}
                </p>
            </div>

            <div class="rounded-[10px] border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Inactivos en esta página
                </p>

                <p class="mt-2 text-3xl font-bold text-zinc-500">
                    {{ $planes->where('estado', 'inactivo')->count() }}
                </p>
            </div>
        </div>

        {{-- Tabla --}}
        <section class="overflow-hidden rounded-[10px] border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[850px] text-left">
                    <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800/60">
                        <tr class="text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            <th class="px-6 py-4 font-semibold">Plan</th>
                            <th class="px-6 py-4 font-semibold">Estado</th>
                            <th class="px-6 py-4 font-semibold">Cursos</th>
                            <th class="px-6 py-4 font-semibold">Usuarios</th>
                            <th class="px-6 py-4 font-semibold">Orden</th>
                            <th class="px-6 py-4 text-right font-semibold">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse ($planes as $plan)
                            <tr class="transition hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-zinc-900 dark:text-white">
                                        {{ $plan->nombre }}
                                    </p>

                                    <p class="mt-1 max-w-md truncate text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $plan->descripcion ?: 'Sin descripción' }}
                                    </p>
                                </td>

                                <td class="px-6 py-4">
                                    @if ($plan->estado === 'activo')
                                        <span class="inline-flex items-center gap-2 text-sm font-medium text-emerald-600">
                                            <span class="size-2 rounded-full bg-emerald-500"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-2 text-sm font-medium text-zinc-500">
                                            <span class="size-2 rounded-full bg-zinc-400"></span>
                                            Inactivo
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                    {{ $plan->cursos_count }}
                                </td>

                                <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                    {{ $plan->usuarios_count }}
                                </td>

                                <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                    {{ $plan->orden }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-3">
                                        <a
                                            href="{{ route('admin.planes.edit', $plan) }}"
                                            class="text-sm font-semibold text-[#102A56] transition hover:text-[#173B72] dark:text-blue-300"
                                        >
                                            Editar
                                        </a>

                                        <form
                                            action="{{ route('admin.planes.destroy', $plan) }}"
                                            method="POST"
                                            onsubmit="return confirm('¿Confirmas que deseas eliminar este plan?');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="text-sm font-semibold text-red-600 transition hover:text-red-700"
                                            >
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-14 text-center">
                                    <p class="font-semibold text-zinc-700 dark:text-zinc-200">
                                        Todavía no hay planes
                                    </p>

                                    <p class="mt-1 text-sm text-zinc-500">
                                        Crea el primer plan para comenzar a controlar el acceso.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($planes->hasPages())
                <div class="border-t border-zinc-200 px-6 py-4 dark:border-zinc-700">
                    {{ $planes->links() }}
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>