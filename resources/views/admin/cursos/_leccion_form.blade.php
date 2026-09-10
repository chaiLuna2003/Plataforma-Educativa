@php
    $esEdicion = isset($leccion) && $leccion !== null;

    $accion = $esEdicion
        ? route(
            'admin.cursos.modulos.lecciones.update',
            [$curso, $modulo, $leccion]
        )
        : route(
            'admin.cursos.modulos.lecciones.store',
            [$curso, $modulo]
        );

    $vimeoUrl = null;

    if ($esEdicion && $leccion->vimeo_video_id) {
        $vimeoUrl = 'https://vimeo.com/'.$leccion->vimeo_video_id;

        if ($leccion->vimeo_video_hash) {
            $vimeoUrl .= '/'.$leccion->vimeo_video_hash;
        }
    }

    $duracionMinutos = $esEdicion && $leccion->duracion_segundos
        ? (int) ceil($leccion->duracion_segundos / 60)
        : null;

    $esMuestra = $esEdicion
        ? $leccion->es_muestra
        : old('leccion.es_muestra', false);

    $estadoSeleccionado = $esEdicion
        ? $leccion->estado
        : old('leccion.estado', 'borrador');
@endphp

<form action="{{ $accion }}" method="POST" class="space-y-5">
    @csrf

    @if ($esEdicion)
        @method('PUT')
    @endif

    {{-- Título --}}
    <div>
        <label class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
            Título de la lección
        </label>

        <input
            name="leccion[titulo]"
            type="text"
            value="{{ $esEdicion ? $leccion->titulo : old('leccion.titulo') }}"
            required
            maxlength="255"
            placeholder="Ej. ¿Qué es el ácido alfa lipoico?"
            class="h-11 w-full rounded-[10px] border border-zinc-300 bg-white px-4 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
        >

        @if (! $esEdicion)
            @error('leccion.titulo')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        @endif
    </div>

    {{-- URL de Vimeo --}}
    <div>
        <label class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
            URL del video en Vimeo
        </label>

        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                <svg
                    class="size-5 text-cyan-600"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 8 6 4-6 4V8Z" />
                    <rect width="18" height="14" x="3" y="5" rx="2" />
                </svg>
            </div>

            <input
                name="leccion[vimeo_url]"
                type="url"
                value="{{ $esEdicion ? $vimeoUrl : old('leccion.vimeo_url') }}"
                required
                placeholder="https://vimeo.com/123456789"
                class="h-11 w-full rounded-[10px] border border-zinc-300 bg-white pl-11 pr-4 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
            >
        </div>

        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
            Puedes pegar un enlace normal, privado no listado o del reproductor de Vimeo.
        </p>

        @if (! $esEdicion)
            @error('leccion.vimeo_url')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        @endif
    </div>

    {{-- Descripción --}}
    <div>
        <label class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
            Descripción
        </label>

        <textarea
            name="leccion[descripcion]"
            rows="4"
            maxlength="5000"
            placeholder="Describe brevemente lo que aprenderá el estudiante..."
            class="w-full rounded-[10px] border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
        >{{ $esEdicion ? $leccion->descripcion : old('leccion.descripcion') }}</textarea>

        @if (! $esEdicion)
            @error('leccion.descripcion')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        @endif
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        {{-- Duración --}}
        <div>
            <label class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                Duración
            </label>

            <div class="relative">
                <input
                    name="leccion[duracion_minutos]"
                    type="number"
                    value="{{ $esEdicion ? $duracionMinutos : old('leccion.duracion_minutos') }}"
                    min="1"
                    max="600"
                    placeholder="60"
                    class="h-11 w-full rounded-[10px] border border-zinc-300 bg-white px-4 pr-16 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                >

                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-xs text-zinc-500">
                    min
                </span>
            </div>

            @if (! $esEdicion)
                @error('leccion.duracion_minutos')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            @endif
        </div>

        {{-- Estado --}}
        <div>
            <label class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                Estado
            </label>

            <select
                name="leccion[estado]"
                required
                class="h-11 w-full rounded-[10px] border border-zinc-300 bg-white px-3 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
            >
                <option value="borrador" @selected($estadoSeleccionado === 'borrador')>
                    Borrador
                </option>

                <option value="publicado" @selected($estadoSeleccionado === 'publicado')>
                    Publicado
                </option>
            </select>
        </div>

        {{-- Orden --}}
        <div>
            <label class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                Orden
            </label>

            <input
                name="leccion[orden]"
                type="number"
                value="{{ $esEdicion ? $leccion->orden : old('leccion.orden') }}"
                min="0"
                max="9999"
                placeholder="Automático"
                class="h-11 w-full rounded-[10px] border border-zinc-300 bg-white px-4 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
            >
        </div>
    </div>

    {{-- Muestra gratuita --}}
    <label class="flex cursor-pointer items-start gap-3 rounded-[10px] border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
        <input
            type="hidden"
            name="leccion[es_muestra]"
            value="0"
        >

        <input
            type="checkbox"
            name="leccion[es_muestra]"
            value="1"
            @checked($esMuestra)
            class="mt-0.5 size-4 rounded border-zinc-300 text-[#102A56] focus:ring-blue-500"
        >

        <span>
            <span class="block text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                Permitir como lección de muestra
            </span>

            <span class="mt-1 block text-xs leading-5 text-zinc-500 dark:text-zinc-400">
                Podrá mostrarse como vista previa aunque el usuario todavía no tenga acceso al curso.
            </span>
        </span>
    </label>

    <button
        type="submit"
        class="inline-flex h-11 items-center justify-center rounded-[10px] bg-[#102A56] px-5 text-sm font-semibold text-white transition hover:bg-[#173B72] focus:outline-none focus:ring-4 focus:ring-blue-500/20"
    >
        {{ $esEdicion ? 'Guardar lección' : 'Agregar lección' }}
    </button>
</form>