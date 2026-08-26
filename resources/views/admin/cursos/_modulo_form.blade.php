@php
    $esEdicion = isset($modulo) && $modulo !== null;

    $accion = $esEdicion
        ? route('admin.cursos.modulos.update', [$curso, $modulo])
        : route('admin.cursos.modulos.store', $curso);
@endphp

<form action="{{ $accion }}" method="POST" class="space-y-5">
    @csrf

    @if ($esEdicion)
        @method('PUT')
    @endif

    <div>
        <label class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
            Título del módulo
        </label>

        <input
            name="modulo[titulo]"
            type="text"
            value="{{ $esEdicion ? $modulo->titulo : old('modulo.titulo') }}"
            required
            maxlength="255"
            placeholder="Ej. Introducción y fundamentos"
            class="h-11 w-full rounded-[10px] border border-zinc-300 bg-white px-4 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
        >

        @if (! $esEdicion)
            @error('modulo.titulo')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        @endif
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
            Descripción
        </label>

        <textarea
            name="modulo[descripcion]"
            rows="4"
            maxlength="5000"
            placeholder="Describe brevemente el contenido del módulo..."
            class="w-full rounded-[10px] border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
        >{{ $esEdicion ? $modulo->descripcion : old('modulo.descripcion') }}</textarea>

        @if (! $esEdicion)
            @error('modulo.descripcion')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        @endif
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                Estado
            </label>

            @php
                $estadoSeleccionado = $esEdicion
                    ? $modulo->estado
                    : old('modulo.estado', 'borrador');
            @endphp

            <select
                name="modulo[estado]"
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

            @if (! $esEdicion)
                @error('modulo.estado')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            @endif
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                Orden
            </label>

            <input
                name="modulo[orden]"
                type="number"
                value="{{ $esEdicion ? $modulo->orden : old('modulo.orden') }}"
                min="0"
                max="9999"
                placeholder="Automático"
                class="h-11 w-full rounded-[10px] border border-zinc-300 bg-white px-4 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
            >

            @if (! $esEdicion)
                @error('modulo.orden')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            @endif
        </div>
    </div>

    <button
        type="submit"
        class="inline-flex h-11 items-center justify-center rounded-[10px] bg-[#102A56] px-5 text-sm font-semibold text-white transition hover:bg-[#173B72] focus:outline-none focus:ring-4 focus:ring-blue-500/20"
    >
        {{ $esEdicion ? 'Guardar módulo' : 'Agregar módulo' }}
    </button>
</form>