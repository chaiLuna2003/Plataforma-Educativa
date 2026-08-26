<x-layouts.app title="Editar curso">
    <div class="flex w-full flex-col gap-6">
        {{-- Encabezado --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <a
                    href="{{ route('admin.cursos.index') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-zinc-500 transition hover:text-[#102A56] dark:text-zinc-400 dark:hover:text-blue-300"
                >
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
                class="inline-flex h-11 items-center justify-center rounded-[10px] border border-zinc-300 px-5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800"
            >
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
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            @include('admin.cursos._form', [
                'curso' => $curso,
                'textoBoton' => 'Guardar cambios',
            ])
        </form>

        {{-- Estructura del curso --}}
        <section class="rounded-[10px] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-zinc-900 dark:text-white">
                        Módulos y lecciones
                    </h2>

                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Organiza aquí el contenido académico del curso.
                    </p>
                </div>

                <button
                    type="button"
                    disabled
                    class="inline-flex h-11 cursor-not-allowed items-center justify-center rounded-[10px] bg-zinc-200 px-5 text-sm font-semibold text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400"
                >
                    Agregar módulo
                </button>
            </div>

            <div class="mt-6">
                @forelse ($curso->modulos as $modulo)
                    <article class="rounded-[10px] border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="font-semibold text-zinc-900 dark:text-white">
                                    {{ $modulo->titulo }}
                                </p>

                                <p class="mt-1 text-sm text-zinc-500">
                                    {{ $modulo->lecciones_count }}
                                    {{ $modulo->lecciones_count === 1 ? 'lección' : 'lecciones' }}
                                </p>
                            </div>

                            <span class="text-sm text-zinc-500">
                                Orden {{ $modulo->orden }}
                            </span>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[10px] border border-dashed border-zinc-300 px-6 py-10 text-center dark:border-zinc-700">
                        <p class="font-semibold text-zinc-700 dark:text-zinc-200">
                            Este curso todavía no contiene módulos
                        </p>

                        <p class="mt-1 text-sm text-zinc-500">
                            En el siguiente bloque habilitaremos su creación.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts.app>