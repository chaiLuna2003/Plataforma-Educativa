<div class="flex w-full flex-col gap-6">
    {{-- Bienvenida --}}
    <section class="relative overflow-hidden rounded-[10px] bg-cucs-navy p-6 text-white shadow-sm sm:p-8">
        <div
            aria-hidden="true"
            class="absolute -right-20 -top-24 size-72 rounded-full bg-cucs-aqua/20 blur-3xl"
        ></div>

        <div
            aria-hidden="true"
            class="absolute -bottom-24 left-1/3 size-64 rounded-full bg-cucs-blue/30 blur-3xl"
        ></div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-cyan-200">
                    {{ $isAdmin ? 'Panel administrativo' : 'Plataforma Educativa' }}
                </p>

                <h1 class="mt-2 text-3xl font-bold">
                    Hola, {{ explode(' ', trim(auth()->user()->name))[0] }}
                </h1>

                <p class="mt-3 max-w-2xl leading-7 text-blue-100">
                    @if ($isAdmin)
                        Consulta el estado general de las cuentas y administra la plataforma.
                    @else
                        Desde aquí podrás acceder a tus cursos y continuar con tu formación.
                    @endif
                </p>
            </div>

            <div class="w-fit rounded-[10px] border border-white/15 bg-white/10 px-4 py-3 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-200">
                    Hoy
                </p>

                <p class="mt-1 font-semibold">
                    {{ now()->translatedFormat('d \d\e F \d\e Y') }}
                </p>
            </div>
        </div>
    </section>

    @if ($isAdmin)
        {{-- Métricas --}}
        @php
            $cards = [
                [
                    'label' => 'Usuarios registrados',
                    'value' => $statistics['total'],
                    'description' => 'Total de cuentas',
                    'color' => 'bg-cucs-sky text-cucs-blue dark:bg-blue-950 dark:text-blue-200',
                ],
                [
                    'label' => 'Estudiantes',
                    'value' => $statistics['students'],
                    'description' => 'Cuentas de estudiantes',
                    'color' => 'bg-cucs-mint text-cucs-aqua dark:bg-cyan-950 dark:text-cyan-200',
                ],
                [
                    'label' => 'Cuentas activas',
                    'value' => $statistics['active'],
                    'description' => 'Con acceso permitido',
                    'color' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-200',
                ],
                [
                    'label' => 'Invitaciones pendientes',
                    'value' => $statistics['pendingInvitations'],
                    'description' => 'Sin contraseña establecida',
                    'color' => 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-200',
                ],
            ];
        @endphp

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($cards as $card)
                <article class="rounded-[10px] border border-cucs-border bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                {{ $card['label'] }}
                            </p>

                            <p class="mt-3 text-3xl font-bold text-cucs-navy dark:text-white">
                                {{ $card['value'] }}
                            </p>
                        </div>

                        <span class="flex size-10 items-center justify-center rounded-[10px] {{ $card['color'] }}">
                            <span class="size-2.5 rounded-full bg-current"></span>
                        </span>
                    </div>

                    <p class="mt-3 text-xs text-slate-400">
                        {{ $card['description'] }}
                    </p>
                </article>
            @endforeach
        </section>

        <div class="grid gap-6 xl:grid-cols-[0.75fr_1.25fr]">
            {{-- Acciones rápidas --}}
            <section class="rounded-[10px] border border-cucs-border bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.14em] text-cucs-aqua">
                        Accesos
                    </p>

                    <h2 class="mt-1 text-xl font-bold text-cucs-navy dark:text-white">
                        Acciones rápidas
                    </h2>
                </div>

                <div class="mt-5 grid gap-3">
                    <a
                        href="{{ route('admin.users.create') }}"
                        wire:navigate
                        class="group flex items-center justify-between rounded-[10px] border border-cucs-border bg-cucs-sky/60 p-4 transition hover:border-cucs-blue hover:bg-cucs-sky dark:border-slate-700 dark:bg-slate-800 dark:hover:border-cucs-aqua"
                    >
                        <span>
                            <span class="block font-semibold text-cucs-navy dark:text-white">
                                Crear una cuenta
                            </span>

                            <span class="mt-1 block text-sm text-slate-500 dark:text-slate-400">
                                Invitar estudiante o administrador
                            </span>
                        </span>

                        <span class="text-xl text-cucs-blue transition group-hover:translate-x-1 dark:text-cucs-aqua-light">
                            →
                        </span>
                    </a>

                    <a
                        href="{{ route('admin.users.index') }}"
                        wire:navigate
                        class="group flex items-center justify-between rounded-[10px] border border-cucs-border p-4 transition hover:border-cucs-aqua hover:bg-cucs-mint/50 dark:border-slate-700 dark:hover:border-cucs-aqua dark:hover:bg-slate-800"
                    >
                        <span>
                            <span class="block font-semibold text-cucs-navy dark:text-white">
                                Administrar usuarios
                            </span>

                            <span class="mt-1 block text-sm text-slate-500 dark:text-slate-400">
                                Buscar, filtrar y controlar accesos
                            </span>
                        </span>

                        <span class="text-xl text-cucs-aqua transition group-hover:translate-x-1">
                            →
                        </span>
                    </a>
                </div>
            </section>

            {{-- Usuarios recientes --}}
            <section class="overflow-hidden rounded-[10px] border border-cucs-border bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between border-b border-cucs-border px-6 py-5 dark:border-slate-800">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.14em] text-cucs-aqua">
                            Actividad
                        </p>

                        <h2 class="mt-1 text-xl font-bold text-cucs-navy dark:text-white">
                            Usuarios recientes
                        </h2>
                    </div>

                    <a
                        href="{{ route('admin.users.index') }}"
                        wire:navigate
                        class="text-sm font-semibold text-cucs-blue hover:underline dark:text-cucs-aqua-light"
                    >
                        Ver todos
                    </a>
                </div>

                <div class="divide-y divide-cucs-border dark:divide-slate-800">
                    @forelse ($recentUsers as $user)
                        <div class="flex items-center justify-between gap-4 px-6 py-4">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-[10px] bg-cucs-sky font-bold text-cucs-navy dark:bg-slate-800 dark:text-white">
                                    {{ $user->initials() }}
                                </span>

                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-slate-800 dark:text-slate-100">
                                        {{ $user->name }}
                                    </p>

                                    <p class="truncate text-sm text-slate-500 dark:text-slate-400">
                                        {{ $user->email }}
                                    </p>
                                </div>
                            </div>

                            <div class="shrink-0 text-right">
                                <span class="text-xs font-semibold {{ $user->isActive() ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $user->isActive() ? 'Activo' : 'Inactivo' }}
                                </span>

                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center text-sm text-slate-500">
                            Todavía no hay usuarios registrados.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    @else
        {{-- Dashboard del estudiante --}}
        <section class="rounded-[10px] border border-cucs-border bg-white p-8 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-12">
            <span class="mx-auto flex size-16 items-center justify-center rounded-[10px] bg-cucs-mint text-3xl text-cucs-aqua dark:bg-cyan-950 dark:text-cyan-200">
                ▶
            </span>

            <h2 class="mt-6 text-2xl font-bold text-cucs-navy dark:text-white">
                Tus cursos aparecerán aquí
            </h2>

            <p class="mx-auto mt-3 max-w-xl leading-7 text-slate-500 dark:text-slate-400">
                Cuando un administrador te asigne contenido, podrás comenzar tus cursos,
                consultar las lecciones y continuar desde el punto donde te quedaste.
            </p>

            <a
                href="{{ route('settings.profile') }}"
                wire:navigate
                class="mt-7 inline-flex h-11 items-center justify-center rounded-[10px] bg-cucs-navy px-5 text-sm font-semibold text-white transition hover:bg-cucs-blue focus:outline-none focus:ring-4 focus:ring-cucs-blue/20"
            >
                Revisar mi perfil
            </a>
        </section>
    @endif
</div>