<x-layouts.app :title="$leccion->titulo">
    @php
        $lecciones = $curso->modulos
            ->flatMap(fn ($modulo) => $modulo->lecciones)
            ->values();

        $indiceActual = $lecciones->search(
            fn ($item) => $item->is($leccion)
        );

        $leccionAnterior = $indiceActual !== false && $indiceActual > 0
            ? $lecciones->get($indiceActual - 1)
            : null;

        $leccionSiguiente = $indiceActual !== false
            ? $lecciones->get($indiceActual + 1)
            : null;
    @endphp

    <div class="flex w-full flex-col gap-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <a
                href="{{ route('student.cursos.show', $curso) }}"
                class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-cucs-blue dark:text-slate-400 dark:hover:text-blue-300"
                wire:navigate
            >
                <span aria-hidden="true">←</span>
                Volver al curso
            </a>

            <span class="text-sm text-slate-500 dark:text-slate-400">
                {{ $curso->titulo }}
            </span>
        </div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
            <main class="min-w-0">
                <section class="overflow-hidden rounded-[10px] bg-black shadow-lg">
                    <div class="aspect-video">
                        @if ($leccion->vimeo_embed_url)
                            <iframe
                                src="{{ $leccion->vimeo_embed_url }}"
                                title="Video de la lección {{ $leccion->titulo }}"
                                class="size-full"
                                allow="autoplay; fullscreen; picture-in-picture; encrypted-media"
                                referrerpolicy="strict-origin-when-cross-origin"
                                allowfullscreen
                            ></iframe>
                        @else
                            <div class="flex size-full items-center justify-center px-6 text-center text-white">
                                <div>
                                    <p class="text-xl font-bold">
                                        Video no disponible
                                    </p>

                                    <p class="mt-2 text-sm text-slate-300">
                                        Esta lección todavía no tiene un video configurado.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </section>

                <section class="mt-6 rounded-[10px] border border-cucs-border bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.14em] text-cucs-aqua">
                        Lección
                    </p>

                    <h1 class="mt-2 text-3xl font-bold text-cucs-navy dark:text-white">
                        {{ $leccion->titulo }}
                    </h1>

                    <div class="mt-4 flex flex-wrap gap-3 text-sm text-slate-500 dark:text-slate-400">
                        @if ($leccion->duracion_segundos)
                            <span class="rounded-full bg-slate-100 px-3 py-1 dark:bg-slate-800">
                                {{ (int) ceil($leccion->duracion_segundos / 60) }} minutos
                            </span>
                        @endif

                        <span class="rounded-full bg-cucs-mint px-3 py-1 font-semibold text-cucs-aqua dark:bg-cyan-950 dark:text-cyan-200">
                            Video
                        </span>

                        @if ($leccion->es_muestra)
                            <span class="rounded-full bg-amber-100 px-3 py-1 font-semibold text-amber-700">
                                Lección de muestra
                            </span>
                        @endif
                    </div>

                    @if ($leccion->descripcion)
                        <div class="mt-6 whitespace-pre-line text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $leccion->descripcion }}</div>
                    @endif
                </section>

                <nav
                    aria-label="Navegación entre lecciones"
                    class="mt-6 grid gap-4 sm:grid-cols-2"
                >
                    @if ($leccionAnterior)
                        <a
                            href="{{ route('student.cursos.lecciones.show', [$curso, $leccionAnterior]) }}"
                            class="group rounded-[10px] border border-cucs-border bg-white p-5 shadow-sm transition hover:border-cucs-blue hover:bg-cucs-sky/40 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-blue-400 dark:hover:bg-slate-800"
                            wire:navigate
                        >
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                ← Anterior
                            </span>

                            <span class="mt-2 block font-bold text-cucs-navy group-hover:text-cucs-blue dark:text-white dark:group-hover:text-blue-300">
                                {{ $leccionAnterior->titulo }}
                            </span>
                        </a>
                    @else
                        <div></div>
                    @endif

                    @if ($leccionSiguiente)
                        <a
                            href="{{ route('student.cursos.lecciones.show', [$curso, $leccionSiguiente]) }}"
                            class="group rounded-[10px] border border-cucs-border bg-white p-5 text-right shadow-sm transition hover:border-cucs-blue hover:bg-cucs-sky/40 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-blue-400 dark:hover:bg-slate-800"
                            wire:navigate
                        >
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Siguiente →
                            </span>

                            <span class="mt-2 block font-bold text-cucs-navy group-hover:text-cucs-blue dark:text-white dark:group-hover:text-blue-300">
                                {{ $leccionSiguiente->titulo }}
                            </span>
                        </a>
                    @endif
                </nav>
            </main>

            <aside class="h-fit overflow-hidden rounded-[10px] border border-cucs-border bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 xl:sticky xl:top-6">
                <div class="border-b border-cucs-border p-5 dark:border-slate-800">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-cucs-aqua">
                        Contenido
                    </p>

                    <h2 class="mt-1 font-bold text-cucs-navy dark:text-white">
                        Programa del curso
                    </h2>

                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        {{ $lecciones->count() }}
                        {{ $lecciones->count() === 1 ? 'lección disponible' : 'lecciones disponibles' }}
                    </p>
                </div>

                <div class="max-h-[65vh] overflow-y-auto">
                    @foreach ($curso->modulos as $modulo)
                        <details
                            class="border-b border-cucs-border last:border-b-0 dark:border-slate-800"
                            @if ($modulo->id === $leccion->modulo_id) open @endif
                        >
                            <summary class="cursor-pointer list-none px-5 py-4 text-sm font-bold text-cucs-navy transition hover:bg-cucs-sky/50 dark:text-white dark:hover:bg-slate-800">
                                {{ $modulo->titulo }}
                            </summary>

                            <div class="border-t border-cucs-border dark:border-slate-800">
                                @foreach ($modulo->lecciones as $item)
                                    <a
                                        href="{{ route('student.cursos.lecciones.show', [$curso, $item]) }}"
                                        @class([
                                            'flex items-center gap-3 px-5 py-3 text-sm transition',
                                            'bg-cucs-sky font-semibold text-cucs-blue dark:bg-slate-800 dark:text-blue-300' => $item->is($leccion),
                                            'text-slate-600 hover:bg-cucs-sky/50 hover:text-cucs-blue dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-300' => ! $item->is($leccion),
                                        ])
                                        wire:navigate
                                    >
                                        <span @class([
                                            'flex size-7 shrink-0 items-center justify-center rounded-full text-xs',
                                            'bg-cucs-blue text-white' => $item->is($leccion),
                                            'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-300' => ! $item->is($leccion),
                                        ])>
                                            ▶
                                        </span>

                                        <span class="min-w-0 truncate">
                                            {{ $item->titulo }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </details>
                    @endforeach
                </div>
            </aside>
        </div>
    </div>
</x-layouts.app>