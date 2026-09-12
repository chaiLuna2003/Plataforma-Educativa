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
                wire:navigate>
                <span aria-hidden="true">←</span>
                Volver al curso
            </a>

            <span class="text-sm text-slate-500 dark:text-slate-400">
                {{ $curso->titulo }}
            </span>
        </div>

        @if (session('status'))
        <div
            class="rounded-[10px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 shadow-sm dark:border-emerald-900 dark:bg-emerald-950/50 dark:text-emerald-300"
            role="status"
            x-data="{ visible: true }"
            x-show="visible"
            x-transition.opacity
            x-init="setTimeout(() => visible = false, 3500)">
            {{ session('status') }}
        </div>
        @endif

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
                            allowfullscreen></iframe>
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

                    <div
                        class="mt-7 border-t border-cucs-border pt-6 dark:border-slate-800"
                        x-data="{ confirmarDesmarcado: false }"
                        @keydown.escape.window="confirmarDesmarcado = false">
                        @if ($leccion->completada_por_estudiante)
                        <div class="flex flex-col gap-4 rounded-[10px] bg-emerald-50 p-4 sm:flex-row sm:items-center sm:justify-between dark:bg-emerald-950/50">
                            <div>
                                <p class="font-semibold text-emerald-700 dark:text-emerald-300">
                                    Lección completada
                                </p>

                                <p class="mt-1 text-sm text-emerald-600 dark:text-emerald-400">
                                    Este avance está guardado en tu cuenta.
                                </p>
                            </div>

                            <button
                                type="button"
                                class="inline-flex h-10 items-center justify-center rounded-[10px] border border-emerald-300 px-4 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100 dark:border-emerald-700 dark:text-emerald-300 dark:hover:bg-emerald-900"
                                @click="confirmarDesmarcado = true">
                                Marcar como pendiente
                            </button>
                        </div>

                        <div
                            x-cloak
                            x-show="confirmarDesmarcado"
                            x-transition.opacity
                            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4"
                            role="dialog"
                            aria-modal="true"
                            aria-labelledby="titulo-confirmar-desmarcado"
                            @click.self="confirmarDesmarcado = false">
                            <div class="w-full max-w-md rounded-[10px] bg-white p-6 shadow-2xl dark:bg-slate-900">
                                <h2
                                    id="titulo-confirmar-desmarcado"
                                    class="text-xl font-bold text-cucs-navy dark:text-white">
                                    ¿Marcar como pendiente?
                                </h2>

                                <p class="mt-3 text-sm leading-6 text-slate-500 dark:text-slate-400">
                                    Esta lección dejará de contar en el progreso del curso. Puedes completarla nuevamente cuando quieras.
                                </p>

                                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                                    <button
                                        type="button"
                                        class="inline-flex h-10 items-center justify-center rounded-[10px] border border-cucs-border px-4 text-sm font-semibold text-cucs-navy transition hover:bg-cucs-sky dark:border-slate-700 dark:text-white dark:hover:bg-slate-800"
                                        @click="confirmarDesmarcado = false">
                                        Cancelar
                                    </button>

                                    <form
                                        method="POST"
                                        action="{{ route('student.cursos.lecciones.progreso.destroy', [$curso, $leccion]) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="inline-flex h-10 w-full items-center justify-center rounded-[10px] bg-cucs-navy px-4 text-sm font-semibold text-white transition hover:bg-cucs-blue sm:w-auto dark:bg-cucs-blue dark:hover:bg-blue-500">
                                            Confirmar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="flex flex-col gap-4 rounded-[10px] bg-cucs-sky p-4 sm:flex-row sm:items-center sm:justify-between dark:bg-slate-800">
                            <div>
                                <p class="font-semibold text-cucs-navy dark:text-white">
                                    Lección pendiente
                                </p>

                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                    Márcala cuando hayas terminado de estudiar el contenido.
                                </p>
                            </div>

                            <form
                                method="POST"
                                action="{{ route('student.cursos.lecciones.progreso.update', [$curso, $leccion]) }}">
                                @csrf
                                @method('PUT')

                                <button
                                    type="submit"
                                    class="inline-flex h-10 w-full items-center justify-center rounded-[10px] bg-cucs-navy px-4 text-sm font-semibold text-white transition hover:bg-cucs-blue sm:w-auto dark:bg-cucs-blue dark:hover:bg-blue-500">
                                    Marcar como completada
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </section>

                <nav
                    aria-label="Navegación entre lecciones"
                    class="mt-6 grid gap-4 sm:grid-cols-2">
                    @if ($leccionAnterior)
                    <a
                        href="{{ route('student.cursos.lecciones.show', [$curso, $leccionAnterior]) }}"
                        class="group rounded-[10px] border border-cucs-border bg-white p-5 shadow-sm transition hover:border-cucs-blue hover:bg-cucs-sky/40 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-blue-400 dark:hover:bg-slate-800"
                        wire:navigate>
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
                        wire:navigate>
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

                    <div class="mt-4">
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <span class="text-slate-500 dark:text-slate-400">
                                {{ $leccionesCompletadas }} de {{ $totalLecciones }}
                            </span>

                            <span class="font-bold text-cucs-blue dark:text-blue-300">
                                {{ $porcentajeProgreso }}%
                            </span>
                        </div>

                        <div
                            class="mt-2 h-2 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700"
                            role="progressbar"
                            aria-label="Progreso del curso"
                            aria-valuemin="0"
                            aria-valuemax="100"
                            aria-valuenow="{{ $porcentajeProgreso }}">
                            <div
                                class="h-full rounded-full bg-cucs-aqua transition-all"
                                style="width: {{ $porcentajeProgreso }}%"></div>
                        </div>
                    </div>
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
                                @class([ 'flex items-center gap-3 px-5 py-3 text-sm transition' , 'bg-cucs-sky font-semibold text-cucs-blue dark:bg-slate-800 dark:text-blue-300'=> $item->is($leccion),
                                'text-slate-600 hover:bg-cucs-sky/50 hover:text-cucs-blue dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-300' => ! $item->is($leccion),
                                ])
                                wire:navigate
                                >
                                <span
                                    @class([ 'flex size-7 shrink-0 items-center justify-center rounded-full text-xs font-bold' , 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300'=> $item->completada_por_estudiante,
                                    'bg-cucs-blue text-white' => ! $item->completada_por_estudiante && $item->is($leccion),
                                    'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-300' => ! $item->completada_por_estudiante && ! $item->is($leccion),
                                    ])
                                    title="{{ $item->completada_por_estudiante ? 'Lección completada' : 'Lección pendiente' }}"
                                    >
                                    {{ $item->completada_por_estudiante ? '✓' : '▶' }}
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