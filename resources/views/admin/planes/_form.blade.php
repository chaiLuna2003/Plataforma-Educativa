@php
    $cursosSeleccionados = collect(
        old('cursos', $plan?->cursos->pluck('id')->all() ?? [])
    )
        ->map(fn ($id) => (int) $id)
        ->all();
@endphp

<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
    <div class="space-y-6">
        {{-- Información principal --}}
        <section class="rounded-[10px] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div>
                <h2 class="text-lg font-bold text-zinc-900 dark:text-white">
                    Información del plan
                </h2>

                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Define el nombre y propósito de este nivel de acceso.
                </p>
            </div>

            <div class="mt-6 space-y-6">
                <div>
                    <label for="nombre" class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                        Nombre del plan
                    </label>

                    <input
                        id="nombre"
                        name="nombre"
                        type="text"
                        value="{{ old('nombre', $plan?->nombre) }}"
                        maxlength="120"
                        required
                        autofocus
                        placeholder="Ej. Plan profesional"
                        class="h-11 w-full rounded-[10px] border bg-white px-4 text-sm text-zinc-900 outline-none transition placeholder:text-zinc-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:bg-zinc-800 dark:text-white
                            @error('nombre') border-red-400 @else border-zinc-300 dark:border-zinc-700 @enderror"
                    >

                    @error('nombre')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="descripcion" class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                        Descripción
                    </label>

                    <textarea
                        id="descripcion"
                        name="descripcion"
                        rows="5"
                        maxlength="2000"
                        placeholder="Describe a quién está dirigido y qué acceso proporciona..."
                        class="w-full rounded-[10px] border bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition placeholder:text-zinc-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:bg-zinc-800 dark:text-white
                            @error('descripcion') border-red-400 @else border-zinc-300 dark:border-zinc-700 @enderror"
                    >{{ old('descripcion', $plan?->descripcion) }}</textarea>

                    @error('descripcion')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Cursos --}}
        <section class="rounded-[10px] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div>
                <h2 class="text-lg font-bold text-zinc-900 dark:text-white">
                    Cursos incluidos
                </h2>

                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Selecciona cada curso que estará disponible para este plan.
                </p>
            </div>

            @error('cursos')
                <p class="mt-4 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @error('cursos.*')
                <p class="mt-4 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                @forelse ($cursos as $curso)
                    <label
                        for="curso-{{ $curso->id }}"
                        class="flex cursor-pointer items-start gap-3 rounded-[10px] border border-zinc-200 p-4 transition hover:border-blue-300 hover:bg-blue-50/50 dark:border-zinc-700 dark:hover:border-blue-700 dark:hover:bg-blue-950/20"
                    >
                        <input
                            id="curso-{{ $curso->id }}"
                            name="cursos[]"
                            type="checkbox"
                            value="{{ $curso->id }}"
                            @checked(in_array($curso->id, $cursosSeleccionados, true))
                            class="mt-1 size-4 rounded border-zinc-300 text-[#102A56] focus:ring-blue-500"
                        >

                        <span class="min-w-0">
                            <span class="block font-semibold text-zinc-900 dark:text-white">
                                {{ $curso->titulo }}
                            </span>

                            <span class="mt-1 block text-xs capitalize text-zinc-500 dark:text-zinc-400">
                                Estado: {{ $curso->estado }}
                            </span>
                        </span>
                    </label>
                @empty
                    <div class="col-span-full rounded-[10px] border border-dashed border-zinc-300 px-5 py-8 text-center dark:border-zinc-700">
                        <p class="font-semibold text-zinc-700 dark:text-zinc-200">
                            No hay cursos disponibles
                        </p>

                        <a
                            href="{{ route('admin.cursos.create') }}"
                            class="mt-2 inline-block text-sm font-semibold text-cyan-600 hover:text-cyan-700"
                        >
                            Crear el primer curso
                        </a>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    {{-- Configuración --}}
    <aside class="space-y-6">
        <section class="rounded-[10px] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <h2 class="text-lg font-bold text-zinc-900 dark:text-white">
                Configuración
            </h2>

            <div class="mt-6 space-y-5">
                <div>
                    <label for="estado" class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                        Estado
                    </label>

                    <select
                        id="estado"
                        name="estado"
                        required
                        class="h-11 w-full rounded-[10px] border border-zinc-300 bg-white px-3 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    >
                        <option value="activo" @selected(old('estado', $plan?->estado ?? 'activo') === 'activo')>
                            Activo
                        </option>

                        <option value="inactivo" @selected(old('estado', $plan?->estado) === 'inactivo')>
                            Inactivo
                        </option>
                    </select>

                    @error('estado')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="orden" class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                        Orden
                    </label>

                    <input
                        id="orden"
                        name="orden"
                        type="number"
                        value="{{ old('orden', $plan?->orden ?? 0) }}"
                        min="0"
                        max="9999"
                        class="h-11 w-full rounded-[10px] border border-zinc-300 bg-white px-4 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    >

                    <p class="mt-2 text-xs text-zinc-500">
                        Los números menores aparecen primero.
                    </p>

                    @error('orden')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Acciones --}}
        <section class="rounded-[10px] border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <button
                type="submit"
                class="inline-flex h-11 w-full items-center justify-center rounded-[10px] bg-[#102A56] px-5 text-sm font-semibold text-white transition hover:bg-[#173B72] focus:outline-none focus:ring-4 focus:ring-blue-500/20"
            >
                {{ $textoBoton }}
            </button>

            <a
                href="{{ route('admin.planes.index') }}"
                class="mt-3 inline-flex h-11 w-full items-center justify-center rounded-[10px] border border-zinc-300 px-5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800"
            >
                Cancelar
            </a>
        </section>
    </aside>
</div>