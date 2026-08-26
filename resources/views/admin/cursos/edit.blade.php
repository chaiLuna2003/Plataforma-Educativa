<x-layouts.app title="Editar curso">
    <div class="flex w-full flex-col gap-6">
        {{-- Encabezado --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <a
                    href="{{ route('admin.cursos.index') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-zinc-500 transition hover:text-[#102A56] dark:text-zinc-400 dark:hover:text-blue-300">
                    <span aria-hidden="true">←</span>
                    Volver a cursos
                </a>

                <p class="mt-6 text-sm font-semibold uppercase tracking-[0.16em] text-cyan-600">
                    Administración
                </p>

                <h1 class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">
                    Editar curso
                </h1>

                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $curso->titulo }}
                </p>
            </div>

            <a
                href="{{ route('admin.cursos.show', $curso) }}"
                class="inline-flex h-11 items-center justify-center rounded-[10px] border border-zinc-300 px-5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                Ver contenido
            </a>
        </div>

        {{-- Mensajes --}}
        @if (session('success'))
        <div class="rounded-[10px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
        @endif

        {{-- Formulario --}}
        <form
            action="{{ route('admin.cursos.update', $curso) }}"
            method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @include('admin.cursos._form', [
            'curso' => $curso,
            'textoBoton' => 'Guardar cambios',
            ])
        </form>

        {{-- Estructura del curso --}}
        
        <section
            id="modulos"
            class="scroll-mt-6 rounded-[10px] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-zinc-900 dark:text-white">
                        Módulos y lecciones
                    </h2>

                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Organiza la estructura académica del curso.
                    </p>
                </div>

                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-[#102A56] dark:bg-blue-950 dark:text-blue-200">
                    {{ $curso->modulos->count() }}
                    {{ $curso->modulos->count() === 1 ? 'módulo' : 'módulos' }}
                </span>
            </div>

            @if (session('error'))
            <div class="mt-6 rounded-[10px] border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
            @endif

            {{-- Crear módulo --}}
            <details
                class="group mt-6 rounded-[10px] border border-cyan-200 bg-cyan-50/50 dark:border-cyan-900 dark:bg-cyan-950/20"
                @if ($errors->has('modulo.*')) open @endif
                >
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4">
                    <div>
                        <p class="font-semibold text-[#102A56] dark:text-blue-200">
                            Agregar nuevo módulo
                        </p>

                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            Crea una nueva sección dentro de este curso.
                        </p>
                    </div>

                    <span class="flex size-9 items-center justify-center rounded-[10px] bg-[#102A56] text-xl text-white transition group-open:rotate-45">
                        +
                    </span>
                </summary>

                <div class="border-t border-cyan-200 p-5 dark:border-cyan-900">
                    @include('admin.cursos._modulo_form', [
                    'curso' => $curso,
                    'modulo' => null,
                    ])
                </div>
            </details>

            {{-- Listado de módulos --}}
            <div class="mt-6 space-y-4">
                @forelse ($curso->modulos as $modulo)
                <article class="overflow-hidden rounded-[10px] border border-zinc-200 dark:border-zinc-700">
                    <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-start gap-4">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-[10px] bg-blue-50 text-sm font-bold text-[#102A56] dark:bg-blue-950 dark:text-blue-200">
                                {{ $loop->iteration }}
                            </span>

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-bold text-zinc-900 dark:text-white">
                                        {{ $modulo->titulo }}
                                    </h3>

                                    @if ($modulo->estado === 'publicado')
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                        Publicado
                                    </span>
                                    @else
                                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                        Borrador
                                    </span>
                                    @endif
                                </div>

                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $modulo->lecciones_count }}
                                    {{ $modulo->lecciones_count === 1 ? 'lección' : 'lecciones' }}
                                    · Orden {{ $modulo->orden }}
                                </p>

                                @if ($modulo->descripcion)
                                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">
                                    {{ $modulo->descripcion }}
                                </p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-3 sm:justify-end">
                            <details class="group">
                                <summary class="cursor-pointer list-none text-sm font-semibold text-[#102A56] transition hover:text-[#173B72] dark:text-blue-300">
                                    Editar
                                </summary>

                                <div class="mt-4 rounded-[10px] border border-zinc-200 bg-zinc-50 p-5 sm:absolute sm:right-10 sm:z-20 sm:w-[520px] sm:shadow-xl dark:border-zinc-700 dark:bg-zinc-900">
                                    <div class="mb-4 flex items-center justify-between">
                                        <p class="font-bold text-zinc-900 dark:text-white">
                                            Editar módulo
                                        </p>
                                    </div>

                                    @include('admin.cursos._modulo_form', [
                                    'curso' => $curso,
                                    'modulo' => $modulo,
                                    ])
                                </div>
                            </details>

                            <form
                                action="{{ route('admin.cursos.modulos.destroy', [$curso, $modulo]) }}"
                                method="POST"
                                onsubmit="return confirm('¿Confirmas que deseas eliminar este módulo?');">
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="text-sm font-semibold text-red-600 transition hover:text-red-700">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="border-t border-zinc-100 bg-zinc-50 px-5 py-3 dark:border-zinc-800 dark:bg-zinc-800/40">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            Las lecciones de este módulo se administrarán en el siguiente bloque.
                        </p>
                    </div>
                </article>
                @empty
                <div class="rounded-[10px] border border-dashed border-zinc-300 px-6 py-10 text-center dark:border-zinc-700">
                    <p class="font-semibold text-zinc-700 dark:text-zinc-200">
                        Este curso todavía no contiene módulos
                    </p>

                    <p class="mt-1 text-sm text-zinc-500">
                        Usa el formulario superior para crear el primero.
                    </p>
                </div>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts.app>