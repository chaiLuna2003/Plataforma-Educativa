<x-layouts.app :title="$curso->titulo">
    <div class="flex w-full flex-col gap-6">
        {{-- Navegación --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <a
                href="{{ route('admin.cursos.index') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-zinc-500 transition hover:text-[#102A56] dark:text-zinc-400 dark:hover:text-blue-300"
            >
                <span aria-hidden="true">←</span>
                Volver a cursos
            </a>

            <a
                href="{{ route('admin.cursos.edit', $curso) }}"
                class="inline-flex h-11 items-center justify-center rounded-[10px] bg-[#102A56] px-5 text-sm font-semibold text-white transition hover:bg-[#173B72]"
            >
                Editar curso
            </a>
        </div>

        {{-- Información principal --}}
        <section class="overflow-hidden rounded-[10px] border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="grid lg:grid-cols-[380px_minmax(0,1fr)]">
                <div class="bg-zinc-100 dark:bg-zinc-800">
                    @if ($curso->imagen_path)
                        <img
                            src="{{ asset('storage/'.$curso->imagen_path) }}"
                            alt="Portada de {{ $curso->titulo }}"
                            class="aspect-video h-full min-h-64 w-full object-cover lg:aspect-auto"
                        >
                    @else
                        <div class="flex min-h-64 items-center justify-center text-zinc-400">
                            Sin portada
                        </div>
                    @endif
                </div>

                <div class="p-6 lg:p-8">
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold capitalize text-slate-700 dark:bg-zinc-800 dark:text-zinc-200">
                            {{ $curso->nivel }}
                        </span>

                        <span @class([
                            'rounded-full px-3 py-1 text-xs font-semibold',
                            'bg-emerald-100 text-emerald-700' => $curso->estado === 'publicado',
                            'bg-amber-100 text-amber-700' => $curso->estado === 'borrador',
                            'bg-zinc-200 text-zinc-600' => $curso->estado === 'archivado',
                        ])>
                            {{ ucfirst($curso->estado) }}
                        </span>
                    </div>

                    <p class="mt-6 text-sm font-semibold uppercase tracking-[0.16em] text-cyan-600">
                        Curso
                    </p>

                    <h1 class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">
                        {{ $curso->titulo }}
                    </h1>

                    <div class="mt-5 whitespace-pre-line text-sm leading-7 text-zinc-600 dark:text-zinc-300">{{ $curso->descripcion ?: 'Este curso todavía no tiene descripción.' }}</div>

                    <dl class="mt-8 grid gap-4 border-t border-zinc-200 pt-6 text-sm sm:grid-cols-3 dark:border-zinc-700">
                        <div>
                            <dt class="text-zinc-500">Módulos</dt>
                            <dd class="mt-1 font-bold text-zinc-900 dark:text-white">
                                {{ $curso->modulos->count() }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-zinc-500">Creado por</dt>
                            <dd class="mt-1 font-bold text-zinc-900 dark:text-white">
                                {{ $curso->creador?->name ?? 'No disponible' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-zinc-500">Última actualización</dt>
                            <dd class="mt-1 font-bold text-zinc-900 dark:text-white">
                                {{ $curso->updated_at->format('d/m/Y') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </section>

        {{-- Contenido --}}
        <section class="rounded-[10px] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div>
                <h2 class="text-xl font-bold text-zinc-900 dark:text-white">
                    Contenido del curso
                </h2>

                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Módulos y lecciones que conforman el programa.
                </p>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($curso->modulos as $modulo)
                    <article class="rounded-[10px] border border-zinc-200 p-5 dark:border-zinc-700">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-cyan-600">
                                    Módulo {{ $loop->iteration }}
                                </p>

                                <h3 class="mt-1 font-bold text-zinc-900 dark:text-white">
                                    {{ $modulo->titulo }}
                                </h3>
                            </div>

                            <span class="text-sm text-zinc-500">
                                {{ $modulo->lecciones_count }}
                                {{ $modulo->lecciones_count === 1 ? 'lección' : 'lecciones' }}
                            </span>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[10px] border border-dashed border-zinc-300 px-6 py-12 text-center dark:border-zinc-700">
                        <p class="font-semibold text-zinc-700 dark:text-zinc-200">
                            El curso todavía no contiene módulos
                        </p>

                        <p class="mt-1 text-sm text-zinc-500">
                            Agrégalos desde la pantalla de edición.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts.app>