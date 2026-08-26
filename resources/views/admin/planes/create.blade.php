<x-layouts.app title="Crear plan">
    <div class="flex w-full flex-col gap-6">
        <div>
            <a
                href="{{ route('admin.planes.index') }}"
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
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 19-7-7 7-7" />
                </svg>

                Volver a planes
            </a>

            <h1 class="mt-4 text-2xl font-bold text-zinc-900 dark:text-white">
                Crear plan
            </h1>

            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                Configura un nuevo nivel de acceso y selecciona sus cursos.
            </p>
        </div>

        @php
            $plan = null;
        @endphp

        <form
            action="{{ route('admin.planes.store') }}"
            method="POST"
        >
            @csrf

            @include('admin.planes._form', [
                'textoBoton' => 'Crear plan',
            ])
        </form>
    </div>
</x-layouts.app>