<x-layouts.app title="Crear curso">
    <div class="flex w-full flex-col gap-6">
        <div>
            <a
                href="{{ route('admin.cursos.index') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-zinc-500 transition hover:text-[#102A56] dark:text-zinc-400 dark:hover:text-blue-300"
            >
                <span aria-hidden="true">←</span>
                Volver a cursos
            </a>

            <p class="mt-6 text-sm font-semibold uppercase tracking-[0.16em] text-cyan-600">
                Administración
            </p>

            <h1 class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">
                Crear curso
            </h1>

            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                Registra la información general. Después podrás agregar módulos y lecciones.
            </p>
        </div>

        <form
            action="{{ route('admin.cursos.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            @include('admin.cursos._form', [
                'curso' => null,
                'textoBoton' => 'Crear curso',
            ])
        </form>
    </div>
</x-layouts.app>