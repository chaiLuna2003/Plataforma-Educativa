<div class="mx-auto flex w-full max-w-3xl flex-col gap-6">
    {{-- Navegación --}}
    <div>
        <a
            href="{{ route('admin.users.show', $user) }}"
            wire:navigate
            class="inline-flex items-center gap-2 text-sm font-semibold text-cyan-600 transition hover:text-cyan-700"
        >
            <svg
                class="size-4"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
                aria-hidden="true"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
            </svg>

            Volver al progreso
        </a>

        <p class="mt-6 text-sm font-semibold uppercase tracking-[0.16em] text-cyan-600">
            Administración
        </p>

        <h1 class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">
            Editar plan del estudiante
        </h1>

        <p class="mt-2 text-sm leading-6 text-zinc-500 dark:text-zinc-400">
            El cambio modificará inmediatamente los cursos disponibles para esta cuenta.
        </p>
    </div>

    {{-- Estudiante --}}
    <section class="rounded-[10px] border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex items-center gap-4">
            <span class="flex size-12 shrink-0 items-center justify-center rounded-[10px] bg-blue-100 font-bold text-[#102A56] dark:bg-blue-950 dark:text-blue-200">
                {{ $user->initials() }}
            </span>

            <div class="min-w-0 flex-1">
                <p class="truncate font-bold text-zinc-900 dark:text-white">
                    {{ $user->name }}
                </p>

                <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $user->email }}
                </p>
            </div>
        </div>
    </section>

    {{-- Formulario --}}
    <section class="overflow-hidden rounded-[10px] border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="space-y-6 p-6 sm:p-8">
            <div>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                    Plan asignado actualmente
                </p>

                <p class="mt-1 text-lg font-bold text-zinc-900 dark:text-white">
                    {{ $user->plan?->nombre ?? 'Sin plan asignado' }}
                </p>

                @if ($user->plan && ! $user->plan->estaActivo())
                    <p class="mt-2 text-sm font-semibold text-red-600">
                        Este plan se encuentra inactivo.
                    </p>
                @endif
            </div>

            <div>
                <label
                    for="planId"
                    class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300"
                >
                    Nuevo plan de acceso
                </label>

                <select
                    wire:model.live="planId"
                    id="planId"
                    name="planId"
                    required
                    @disabled($planes->isEmpty())
                    class="h-12 w-full rounded-[10px] border border-zinc-300 bg-white px-4 text-zinc-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:cursor-not-allowed disabled:opacity-60 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                >
                    <option value="">Selecciona un plan activo</option>

                    @foreach ($planes as $plan)
                        <option value="{{ $plan->id }}">
                            {{ $plan->nombre }}
                            — {{ $plan->cursos_publicados_count }}
                            {{ $plan->cursos_publicados_count === 1 ? 'curso' : 'cursos' }}
                        </option>
                    @endforeach
                </select>

                @error('planId')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Resumen del plan seleccionado --}}
            @foreach ($planes as $plan)
                @if ((int) $planId === $plan->id)
                    <div
                        wire:key="plan-seleccionado-{{ $plan->id }}"
                        class="rounded-[10px] border border-cyan-200 bg-cyan-50 p-5 dark:border-cyan-900 dark:bg-cyan-950/40"
                    >
                        <p class="text-xs font-semibold uppercase tracking-wider text-cyan-700 dark:text-cyan-300">
                            Acceso seleccionado
                        </p>

                        <p class="mt-2 font-bold text-cyan-950 dark:text-cyan-100">
                            {{ $plan->nombre }}
                        </p>

                        @if ($plan->descripcion)
                            <p class="mt-2 text-sm leading-6 text-cyan-800 dark:text-cyan-200">
                                {{ $plan->descripcion }}
                            </p>
                        @endif

                        <p class="mt-3 text-sm font-semibold text-cyan-800 dark:text-cyan-200">
                            Incluye {{ $plan->cursos_publicados_count }}
                            {{ $plan->cursos_publicados_count === 1 ? 'curso publicado' : 'cursos publicados' }}.
                        </p>
                    </div>
                @endif
            @endforeach

            @if ($planes->isEmpty())
                <div class="rounded-[10px] border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-800 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200">
                    No existen planes activos disponibles. Activa o crea un plan antes de modificar esta cuenta.
                </div>
            @endif

            <div class="rounded-[10px] border border-zinc-200 bg-zinc-50 p-4 text-sm leading-6 text-zinc-600 dark:border-zinc-700 dark:bg-zinc-800/60 dark:text-zinc-300">
                El progreso histórico no será eliminado. Si el estudiante vuelve a obtener acceso a un curso anterior, conservará las lecciones que ya completó.
            </div>
        </div>

        {{-- Acciones --}}
        <div class="flex flex-col-reverse gap-3 border-t border-zinc-200 bg-zinc-50 px-6 py-4 sm:flex-row sm:justify-end dark:border-zinc-700 dark:bg-zinc-800/50">
            <a
                href="{{ route('admin.users.show', $user) }}"
                wire:navigate
                class="inline-flex h-11 items-center justify-center rounded-[10px] border border-zinc-300 px-5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-600 dark:text-zinc-200 dark:hover:bg-zinc-700"
            >
                Cancelar
            </a>

            <button
                type="button"
                wire:click="save"
                wire:confirm="¿Confirmas que deseas cambiar el plan de este estudiante?"
                wire:loading.attr="disabled"
                wire:target="save"
                @disabled($planes->isEmpty())
                class="inline-flex h-11 items-center justify-center gap-2 rounded-[10px] bg-[#102A56] px-5 text-sm font-semibold text-white transition hover:bg-[#173B72] focus:outline-none focus:ring-4 focus:ring-blue-500/20 disabled:cursor-not-allowed disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="save">
                    Guardar cambio
                </span>

                <span wire:loading.flex wire:target="save" class="items-center gap-2">
                    <svg
                        class="size-4 animate-spin"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>

                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"
                        ></path>
                    </svg>

                    Guardando...
                </span>
            </button>
        </div>
    </section>
</div>