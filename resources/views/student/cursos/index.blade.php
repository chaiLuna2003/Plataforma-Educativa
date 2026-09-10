<x-layouts.app title="Mis cursos">
    <div class="flex w-full flex-col gap-6">
        <section class="relative overflow-hidden rounded-[10px] bg-cucs-navy p-6 text-white shadow-sm sm:p-8">
            <div
                aria-hidden="true"
                class="absolute -right-20 -top-24 size-72 rounded-full bg-cucs-aqua/20 blur-3xl"
            ></div>

            <div class="relative flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-cyan-200">
                        Formación académica
                    </p>

                    <h1 class="mt-2 text-3xl font-bold">
                        Mis cursos
                    </h1>

                    <p class="mt-3 max-w-2xl leading-7 text-blue-100">
                        Consulta el contenido disponible en tu plan y continúa con tu formación.
                    </p>
                </div>

                @if ($plan)
                    <div class="w-fit rounded-[10px] border border-white/15 bg-white/10 px-4 py-3 backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-wider text-blue-200">
                            Plan actual
                        </p>

                        <p class="mt-1 font-semibold">
                            {{ $plan->nombre }}
                        </p>
                    </div>
                @endif
            </div>
        </section>

        @if ($cursos->isNotEmpty())
            <section>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.14em] text-cucs-aqua">
                            Biblioteca
                        </p>

                        <h2 class="mt-1 text-2xl font-bold text-cucs-navy dark:text-white">
                            Contenido disponible
                        </h2>
                    </div>

                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        {{ $cursos->total() }}
                        {{ $cursos->total() === 1 ? 'curso disponible' : 'cursos disponibles' }}
                    </p>
                </div>

                <div class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($cursos as $curso)
                        <article class="group overflow-hidden rounded-[10px] border border-cucs-border bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900">
                            <a
                                href="{{ route('student.cursos.show', $curso) }}"
                                class="block"
                                wire:navigate
                            >
                                <div class="relative aspect-video overflow-hidden bg-cucs-sky dark:bg-slate-800">
                                    @if ($curso->imagen_path)
                                        <img
                                            src="{{ asset('storage/'.$curso->imagen_path) }}"
                                            alt="Portada de {{ $curso->titulo }}"
                                            class="size-full object-cover transition duration-300 group-hover:scale-105"
                                        >
                                    @else
                                        <div class="flex size-full items-center justify-center">
                                            <svg
                                                class="size-14 text-cucs-blue/60 dark:text-blue-300"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.5"
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

                                    <span @class([
                                        'absolute left-4 top-4 rounded-full px-3 py-1 text-xs font-semibold capitalize shadow-sm',
                                        'bg-emerald-100 text-emerald-700' => $curso->nivel === 'basico',
                                        'bg-amber-100 text-amber-700' => $curso->nivel === 'intermedio',
                                        'bg-rose-100 text-rose-700' => $curso->nivel === 'avanzado',
                                    ])>
                                        {{ $curso->nivel }}
                                    </span>
                                </div>

                                <div class="p-6">
                                    <h3 class="text-xl font-bold text-cucs-navy transition group-hover:text-cucs-blue dark:text-white dark:group-hover:text-blue-300">
                                        {{ $curso->titulo }}
                                    </h3>

                                    <p class="mt-3 min-h-12 text-sm leading-6 text-slate-500 dark:text-slate-400">
                                        {{ str($curso->descripcion ?: 'Explora el contenido disponible de este curso.')->limit(130) }}
                                    </p>

                                    <div class="mt-6 flex items-center justify-between border-t border-cucs-border pt-4 dark:border-slate-800">
                                        <span class="text-sm text-slate-500 dark:text-slate-400">
                                            {{ $curso->modulos_count }}
                                            {{ $curso->modulos_count === 1 ? 'módulo' : 'módulos' }}
                                        </span>

                                        <span class="inline-flex items-center gap-2 text-sm font-semibold text-cucs-blue dark:text-blue-300">
                                            Ver curso
                                            <span
                                                aria-hidden="true"
                                                class="transition group-hover:translate-x-1"
                                            >
                                                →
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>

                @if ($cursos->hasPages())
                    <div class="mt-8">
                        {{ $cursos->links() }}
                    </div>
                @endif
            </section>
        @else
            <section class="rounded-[10px] border border-cucs-border bg-white px-6 py-14 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <span class="mx-auto flex size-16 items-center justify-center rounded-[10px] bg-cucs-mint text-3xl text-cucs-aqua dark:bg-cyan-950 dark:text-cyan-200">
                    ▶
                </span>

                <h2 class="mt-6 text-2xl font-bold text-cucs-navy dark:text-white">
                    Aún no tienes cursos disponibles
                </h2>

                <p class="mx-auto mt-3 max-w-xl leading-7 text-slate-500 dark:text-slate-400">
                    @if ($plan)
                        Tu plan todavía no contiene cursos publicados. El contenido aparecerá aquí cuando esté disponible.
                    @else
                        Tu cuenta no tiene un plan activo asignado. Contacta al administrador para revisar tu acceso.
                    @endif
                </p>

                <a
                    href="{{ route('dashboard') }}"
                    class="mt-7 inline-flex h-11 items-center justify-center rounded-[10px] border border-cucs-border px-5 text-sm font-semibold text-cucs-navy transition hover:bg-cucs-sky dark:border-slate-700 dark:text-white dark:hover:bg-slate-800"
                    wire:navigate
                >
                    Volver al inicio
                </a>
            </section>
        @endif
    </div>
</x-layouts.app>