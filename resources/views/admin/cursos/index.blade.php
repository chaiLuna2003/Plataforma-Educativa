<x-layouts.app title="Gestión de cursos">
    <div class="flex w-full flex-col gap-6">
        {{-- Encabezado --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-cyan-600">
                    Administración
                </p>

                <h1 class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">
                    Gestión de cursos
                </h1>

                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                    Crea, publica y organiza el contenido de la plataforma.
                </p>
            </div>

            <a
                href="{{ route('admin.cursos.create') }}"
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

                Crear curso
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
                    Total de cursos
                </p>

                <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">
                    {{ $cursos->total() }}
                </p>
            </div>

            <div class="rounded-[10px] border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Publicados en esta página
                </p>

                <p class="mt-2 text-3xl font-bold text-emerald-600">
                    {{ $cursos->where('estado', 'publicado')->count() }}
                </p>
            </div>

            <div class="rounded-[10px] border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    En borrador en esta página
                </p>

                <p class="mt-2 text-3xl font-bold text-amber-600">
                    {{ $cursos->where('estado', 'borrador')->count() }}
                </p>
            </div>
        </div>

        {{-- Tabla --}}
        <section class="overflow-hidden rounded-[10px] border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left">
                    <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800/60">
                        <tr class="text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            <th class="px-6 py-4 font-semibold">Curso</th>
                            <th class="px-6 py-4 font-semibold">Nivel</th>
                            <th class="px-6 py-4 font-semibold">Estado</th>
                            <th class="px-6 py-4 font-semibold">Módulos</th>
                            <th class="px-6 py-4 font-semibold">Orden</th>
                            <th class="px-6 py-4 text-right font-semibold">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse ($cursos as $curso)
                            <tr class="transition hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($curso->imagen_path)
                                            <img
                                                src="{{ asset('storage/'.$curso->imagen_path) }}"
                                                alt="Portada de {{ $curso->titulo }}"
                                                class="h-14 w-20 rounded-[10px] object-cover"
                                            >
                                        @else
                                            <div class="flex h-14 w-20 items-center justify-center rounded-[10px] bg-blue-50 text-[#102A56] dark:bg-blue-950 dark:text-blue-200">
                                                <svg
                                                    class="size-6"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                    aria-hidden="true"
                                                >
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a6 6 0 0 0-6-1.5v12a6 6 0 0 1 6 1.5m0-12a6 6 0 0 1 6-1.5v12a6 6 0 0 0-6 1.5m0-12v12" />
                                                </svg>
                                            </div>
                                        @endif

                                        <div class="min-w-0">
                                            <p class="font-semibold text-zinc-900 dark:text-white">
                                                {{ $curso->titulo }}
                                            </p>

                                            <p class="mt-1 max-w-md truncate text-sm text-zinc-500 dark:text-zinc-400">
                                                {{ $curso->descripcion ?: 'Sin descripción' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold capitalize text-slate-700 dark:bg-zinc-800 dark:text-zinc-200">
                                        {{ $curso->nivel }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    @if ($curso->estado === 'publicado')
                                        <span class="inline-flex items-center gap-2 text-sm font-medium text-emerald-600">
                                            <span class="size-2 rounded-full bg-emerald-500"></span>
                                            Publicado
                                        </span>
                                    @elseif ($curso->estado === 'archivado')
                                        <span class="inline-flex items-center gap-2 text-sm font-medium text-zinc-500">
                                            <span class="size-2 rounded-full bg-zinc-400"></span>
                                            Archivado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-2 text-sm font-medium text-amber-600">
                                            <span class="size-2 rounded-full bg-amber-500"></span>
                                            Borrador
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                    {{ $curso->modulos_count }}
                                </td>

                                <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                    {{ $curso->orden }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-3">
                                        <a
                                            href="{{ route('admin.cursos.show', $curso) }}"
                                            class="text-sm font-semibold text-cyan-600 transition hover:text-cyan-700"
                                        >
                                            Ver
                                        </a>

                                        <a
                                            href="{{ route('admin.cursos.edit', $curso) }}"
                                            class="text-sm font-semibold text-[#102A56] transition hover:text-[#173B72] dark:text-blue-300"
                                        >
                                            Editar
                                        </a>

                                        <form
                                            action="{{ route('admin.cursos.destroy', $curso) }}"
                                            method="POST"
                                            onsubmit="return confirm('¿Confirmas que deseas eliminar este curso?');"
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
                                        Todavía no hay cursos
                                    </p>

                                    <p class="mt-1 text-sm text-zinc-500">
                                        Crea el primer curso para comenzar a organizar el contenido.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($cursos->hasPages())
                <div class="border-t border-zinc-200 px-6 py-4 dark:border-zinc-700">
                    {{ $cursos->links() }}
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>