<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-cucs-surface text-cucs-ink antialiased dark:bg-slate-950 dark:text-slate-100">
    <flux:sidebar
        sticky
        stashable
        class="border-r border-cucs-border bg-white dark:border-slate-800 dark:bg-[#0b1f3f]">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a
            href="{{ route('dashboard') }}"
            class="mb-5 flex items-center gap-3 rounded-[10px] p-2 transition hover:bg-cucs-sky dark:hover:bg-white/10"
            wire:navigate>
            <span class="flex size-11 shrink-0 items-center justify-center rounded-[10px] bg-white p-1.5 shadow-sm ring-1 ring-cucs-border">
                <img
                    src="{{ asset('images/cucs-logo.png') }}"
                    alt="Logotipo de CUCS"
                    class="size-full object-contain">
            </span>

            <span class="min-w-0">
                <span class="block text-base font-bold tracking-wide text-cucs-navy dark:text-white">
                    CUCS
                </span>

                <span class="block truncate text-xs text-slate-500 dark:text-blue-200">
                    Plataforma Educativa
                </span>
            </span>
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group heading="Plataforma" class="grid gap-1">
                <flux:navlist.item
                    icon="home"
                    :href="route('dashboard')"
                    :current="request()->routeIs('dashboard')"
                    class="rounded-[10px] transition hover:bg-cucs-sky/80 data-current:bg-cucs-sky data-current:text-cucs-navy dark:hover:bg-white/10 dark:data-current:bg-white/15 dark:data-current:text-white"
                    wire:navigate>
                    Inicio
                </flux:navlist.item>

          @if (auth()->user()->isAdmin())
    <flux:navlist.item
        icon="users"
        :href="route('admin.users.index')"
        :current="request()->routeIs('admin.users.*')"
        class="rounded-[10px] transition hover:bg-cucs-sky/80 data-current:bg-cucs-sky data-current:text-cucs-navy dark:hover:bg-white/10 dark:data-current:bg-white/15 dark:data-current:text-white"
        wire:navigate
    >
        Usuarios
    </flux:navlist.item>

    <flux:navlist.item
        icon="book-open"
        :href="route('admin.cursos.index')"
        :current="request()->routeIs('admin.cursos.*')"
        class="rounded-[10px] transition hover:bg-cucs-sky/80 data-current:bg-cucs-sky data-current:text-cucs-navy dark:hover:bg-white/10 dark:data-current:bg-white/15 dark:data-current:text-white"
        wire:navigate
    >
        Cursos
    </flux:navlist.item>

    <flux:navlist.item
        icon="rectangle-stack"
        :href="route('admin.planes.index')"
        :current="request()->routeIs('admin.planes.*')"
        class="rounded-[10px] transition hover:bg-cucs-sky/80 data-current:bg-cucs-sky data-current:text-cucs-navy dark:hover:bg-white/10 dark:data-current:bg-white/15 dark:data-current:text-white"
        wire:navigate
    >
        Planes
    </flux:navlist.item>
@endif
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />



        <!-- Desktop User Menu -->
        <flux:dropdown position="bottom" align="start">
         <flux:profile
    :name="auth()->user()->name"
    :initials="auth()->user()->initials()"
    icon-trailing="chevrons-up-down"
    class="rounded-[10px] transition hover:bg-cucs-sky dark:hover:bg-white/10"
/>

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                <span class="mt-1 w-fit rounded-full bg-cucs-mint px-2 py-0.5 text-[11px] font-semibold text-cucs-aqua dark:bg-cyan-900/50 dark:text-cyan-200">
    {{ auth()->user()->isAdmin() ? 'Administrador' : 'Estudiante' }}
</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item href="/settings/profile" icon="cog" wire:navigate>Configuración</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        Cerrar Sesión
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile
                :initials="auth()->user()->initials()"
                icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item href="/settings/profile" icon="cog" wire:navigate>Configuración</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>