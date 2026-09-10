<x-layouts.app :title="$curso->titulo">
    <div class="flex w-full flex-col gap-6">
        <a
            href="{{ route('student.cursos.index') }}"
            class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-cucs-blue dark:text-slate-400 dark:hover:text-blue-300"
            wire:navigate
        >
            <span aria-hidden="true">←</span>
            Volver a mis cursos
        </a>

        <section class="overflow-hidden rounded-[10px] border border-cucs-border bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="grid lg:grid-cols-[380px_minmax(0,1fr)]">
                <div class="bg-cucs-sky dark:bg-slate-800">
                    @if ($curso->imagen_path)
                        <img
                            src="{{ asset('storage/'.$curso->imagen_path) }}"
                            alt="Portada de {{ $curso->titulo }}"
                            class="aspect-video size-full min-h-64 object-cover lg:aspect-auto"
                        >
                    @else
                        <div class="flex min-h-64 items-center justify-center">
                            <svg
                                class="size-20 text-cucs-blue/50 dark:text-blue-300"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.3"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25A8.966 8.966 0 0118 3.75c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"
                                />
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="p-6 sm:p-8">
                    <div class="flex flex-wrap gap-2">
                        <span @class([
                            'rounded-full px-3 py-1 text-xs font-semibold capitalize',
                            'bg-emerald-100 text-emerald-700' => $curso->nivel === 'basico',
                            'bg-amber-100 text-amber-700' => $curso->nivel === 'intermedio',
                            'bg-rose-100 text-rose-700' => $curso->nivel === 'avanzado',
                        ])>
                            Nivel {{ $curso->nivel }}
                        </span>

                        <span class="rounded-full bg-cucs-mint px-3 py-1 text-xs font-semibold text-cucs-aqua dark:bg-cyan-950 dark:text-cyan-200">
                            Disponible
                        </span>
                    </div>

                    <p class="mt-6 text-sm font-semibold uppercase tracking-[0.16em] text-cucs-aqua">
                        Curso
                    </p>

                    <h1 class="mt-2 text-3xl font-bold text-cucs-navy dark:text-white">
                        {{ $curso->titulo }}
                    </h1>

                    <div class="mt-5 whitespace-pre-line text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $curso->descripcion ?: 'Este curso todavía no tiene una descripción disponible.' }}</div>

                    @php
                        $totalLecciones = $curso->modulos
                            ->sum(fn ($modulo) => $modulo->lecciones->count());
                    @endphp

                    <dl class="mt-8 grid gap-4 border-t border-cucs-border pt-6 text-sm sm:grid-cols-2 dark:border-slate-800">
                        <div>
                            <dt class="text-slate-500 dark:text-slate-400">
                                Módulos disponibles
                            </dt>

                            <dd class="mt-1 text-lg font-bold text-cucs-navy dark:text-white">
                                {{ $curso->modulos->count() }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-slate-500 dark:text-slate-400">
                                Lecciones disponibles
                            </dt>

                            <dd class="mt-1 text-lg font-bold text-cucs-navy dark:text-white">
                                {{ $totalLecciones }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </section>

        <section>
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.14em] text-cucs-aqua">
                    Contenido
                </p>

                <h2 class="mt-1 text-2xl font-bold text-cucs-navy dark:text-white">
                    Programa del curso
                </h2>

                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    Selecciona una lección para comenzar o continuar con el contenido.
                </p>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($curso->modulos as $modulo)
                    <details
                        class="group overflow-hidden rounded-[10px] border border-cucs-border bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
                        @if ($loop->first) open @endif
                    >
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-5 transition hover:bg-cucs-sky/50 dark:hover:bg-slate-800">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-cucs-aqua">
                                    Módulo {{ $loop->iteration }}
                                </p>

                                <h3 class="mt-1 truncate text-lg font-bold text-cucs-navy dark:text-white">
                                    {{ $modulo->titulo }}
                                </h3>

                                @if ($modulo->descripcion)
                                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                                        {{ $modulo->descripcion }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex shrink-0 items-center gap-3">
                                <span class="text-sm text-slate-500 dark:text-slate-400">
                                    {{ $modulo->lecciones->count() }}
                                    {{ $modulo->lecciones->count() === 1 ? 'lección' : 'lecciones' }}
                                </span>

                                <span
                                    aria-hidden="true"
                                    class="text-xl text-cucs-blue transition group-open:rotate-180 dark:text-blue-300"
                                >
                                    ↓
                                </span>
                            </div>
                        </summary>

                        <div class="border-t border-cucs-border dark:border-slate-800">
                            @forelse ($modulo->lecciones as $leccion)
                                <a
                                    href="{{ route('student.cursos.lecciones.show', [$curso, $leccion]) }}"
                                    class="group/lesson flex items-center gap-4 border-b border-cucs-border px-5 py-4 transition last:border-b-0 hover:bg-cucs-sky/50 dark:border-slate-800 dark:hover:bg-slate-800"
                                    wire:navigate
                                >
                                    <span class="flex size-11 shrink-0 items-center justify-center rounded-[10px] bg-cucs-mint text-cucs-aqua dark:bg-cyan-950 dark:text-cyan-200">
                                        ▶
                                    </span>

                                    <span class="min-w-0 flex-1">
                                        <span class="block font-semibold text-cucs-navy transition group-hover/lesson:text-cucs-blue dark:text-white dark:group-hover/lesson:text-blue-300">
                                            {{ $leccion->titulo }}
                                        </span>

                                        <span class="mt-1 block text-sm text-slate-500 dark:text-slate-400">
                                            @if ($leccion->duracion_segundos)
                                                {{ (int) ceil($leccion->duracion_segundos / 60) }} minutos
                                            @else
                                                Duración no disponible
                                            @endif

                                            @if ($leccion->es_muestra)
                                                · Lección de muestra
                                            @endif
                                        </span>
                                    </span>

                                    <span
                                        aria-hidden="true"
                                        class="text-xl text-cucs-blue transition group-hover/lesson:translate-x-1 dark:text-blue-300"
                                    >
                                        →
                                    </span>
                                </a>
                            @empty
                                <div class="px-5 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                    Este módulo todavía no tiene lecciones disponibles.
                                </div>
                            @endforelse
                        </div>
                    </details>
                @empty
                    <div class="rounded-[10px] border border-cucs-border bg-white px-6 py-12 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <h3 class="text-xl font-bold text-cucs-navy dark:text-white">
                            El contenido estará disponible próximamente
                        </h3>

                        <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-500 dark:text-slate-400">
                            Este curso todavía no contiene módulos publicados.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts.app>