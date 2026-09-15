<div class="mx-auto flex w-full max-w-6xl flex-col gap-6">
    {{-- Navegación --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <a
            href="{{ route('admin.users.index') }}"
            wire:navigate
            class="inline-flex items-center gap-2 text-sm font-semibold text-cyan-600 transition hover:text-cyan-700">
            <svg
                class="size-4"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
            </svg>

            Volver a usuarios
        </a>

        <a
            href="{{ route('admin.users.edit', $user) }}"
            wire:navigate
            class="inline-flex h-11 items-center justify-center rounded-[10px] bg-[#102A56] px-5 text-sm font-semibold text-white transition hover:bg-[#173B72] focus:outline-none focus:ring-4 focus:ring-blue-500/20">
            Editar plan
        </a>
    </div>

    @if (session('status'))
    <div class="rounded-[10px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300">
        {{ session('status') }}
    </div>
    @endif

    {{-- Estudiante --}}
    <section class="rounded-[10px] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 sm:p-8">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
            <span class="flex size-16 shrink-0 items-center justify-center rounded-[14px] bg-blue-100 text-xl font-bold text-[#102A56] dark:bg-blue-950 dark:text-blue-200">
                {{ $user->initials() }}
            </span>

            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-cyan-600">
                    Progreso del estudiante
                </p>

                <h1 class="mt-1 truncate text-2xl font-bold text-zinc-900 dark:text-white">
                    {{ $user->name }}
                </h1>

                <p class="mt-1 truncate text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $user->email }}
                </p>
            </div>

            <div>
                @if ($user->isActive())
                <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-sm font-semibold text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
                    <span class="size-2 rounded-full bg-emerald-500"></span>
                    Cuenta activa
                </span>
                @else
                <span class="inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1.5 text-sm font-semibold text-red-700 dark:bg-red-950/50 dark:text-red-300">
                    <span class="size-2 rounded-full bg-red-500"></span>
                    Cuenta inactiva
                </span>
                @endif
            </div>
        </div>
    </section>

    {{-- Alertas del plan --}}
    @if (! $plan)
    <div class="rounded-[10px] border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200">
        Este estudiante no tiene un plan asignado y actualmente no puede acceder a cursos.
    </div>
    @elseif (! $plan->estaActivo())
    <div class="rounded-[10px] border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/40 dark:text-red-200">
        El plan <strong>{{ $plan->nombre }}</strong> está inactivo. El estudiante no puede acceder a sus cursos hasta que se le asigne un plan activo.
    </div>
    @endif

    {{-- Resumen --}}
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-[10px] border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                Plan actual
            </p>

            <p class="mt-2 truncate text-lg font-bold text-zinc-900 dark:text-white">
                {{ $plan?->nombre ?? 'Sin plan' }}
            </p>

            @if ($plan)
            <p class="mt-1 text-xs font-semibold {{ $plan->estaActivo() ? 'text-emerald-600' : 'text-red-600' }}">
                {{ $plan->estaActivo() ? 'Activo' : 'Inactivo' }}
            </p>
            @endif
        </article>

        <article class="rounded-[10px] border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                Cursos disponibles
            </p>

            <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">
                {{ $totalCursos }}
            </p>

            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                Cursos publicados del plan
            </p>
        </article>

        <article class="rounded-[10px] border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                Progreso general
            </p>

            <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">
                {{ $porcentajeGeneral }}%
            </p>

            <div class="mt-3 h-2 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                <div
                    class="h-full rounded-full bg-cyan-500 transition-all"
                    style="width: {{ $porcentajeGeneral }}%"></div>
            </div>
        </article>

        <article class="rounded-[10px] border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                Lecciones completadas
            </p>

            <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">
                {{ $leccionesCompletadas }}
                <span class="text-base font-medium text-zinc-400">
                    / {{ $totalLecciones }}
                </span>
            </p>

            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                @if ($ultimaActividad)
                Último avance:
                {{ \Illuminate\Support\Carbon::parse($ultimaActividad)->format('d/m/Y H:i') }}
                @else
                Todavía no registra avances
                @endif
            </p>
        </article>
    </section>

    {{-- Cursos --}}
    <section>
        <div class="mb-4">
            <h2 class="text-xl font-bold text-zinc-900 dark:text-white">
                Cursos incluidos en el plan
            </h2>

            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                El progreso considera únicamente módulos y lecciones publicados.
            </p>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            @forelse ($cursos as $curso)
            <article
                wire:key="curso-progreso-{{ $curso->id }}"
                class="rounded-[10px] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wider text-cyan-600">
                            {{ $curso->nivel ?: 'Curso' }}
                        </p>

                        <h3 class="mt-1 truncate text-lg font-bold text-zinc-900 dark:text-white">
                            {{ $curso->titulo }}
                        </h3>
                    </div>

                    <span class="shrink-0 rounded-full bg-blue-50 px-3 py-1 text-sm font-bold text-[#102A56] dark:bg-blue-950 dark:text-blue-200">
                        {{ $curso->porcentaje_progreso }}%
                    </span>
                </div>

                <div class="mt-5 h-2.5 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                    <div
                        class="h-full rounded-full bg-cyan-500 transition-all"
                        style="width: {{ $curso->porcentaje_progreso }}%"></div>
                </div>

                <div class="mt-3 flex items-center justify-between gap-4 text-sm text-zinc-500 dark:text-zinc-400">
                  <span>{{ $curso->lecciones_completadas_count }} de {{ $curso->lecciones_publicadas_count }} lecciones</span>

                    @if ($curso->porcentaje_progreso === 100)
                    <span class="font-semibold text-emerald-600">
                        Completado
                    </span>
                    @elseif ($curso->porcentaje_progreso > 0)
                    <span class="font-semibold text-cyan-600">
                        En progreso
                    </span>
                    @else
                    <span class="font-semibold text-zinc-500">
                        Sin comenzar
                    </span>
                    @endif
                </div>
            </article>
            @empty
            <div class="rounded-[10px] border border-dashed border-zinc-300 bg-zinc-50 px-6 py-12 text-center dark:border-zinc-700 dark:bg-zinc-900/50 lg:col-span-2">
                <p class="font-semibold text-zinc-700 dark:text-zinc-200">
                    No hay cursos disponibles
                </p>

                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    @if (! $plan)
                    Asigna un plan activo para habilitar los cursos.
                    @elseif (! $plan->estaActivo())
                    Cambia el plan inactivo por uno disponible.
                    @else
                    Este plan todavía no contiene cursos publicados.
                    @endif
                </p>
            </div>
            @endforelse
        </div>
    </section>
</div>