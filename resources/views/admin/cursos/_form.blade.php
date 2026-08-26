<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
    {{-- Información principal --}}
    <section class="rounded-[10px] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div>
            <h2 class="text-lg font-bold text-zinc-900 dark:text-white">
                Información del curso
            </h2>

            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Proporciona los datos que identificarán el curso.
            </p>
        </div>

        <div class="mt-6 space-y-6">
            {{-- Título --}}
            <div>
                <label for="titulo" class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                    Título del curso
                </label>

                <input
                    id="titulo"
                    name="titulo"
                    type="text"
                    value="{{ old('titulo', $curso?->titulo) }}"
                    maxlength="255"
                    required
                    autofocus
                    placeholder="Ej. Fundamentos de nutrición clínica"
                    class="h-11 w-full rounded-[10px] border bg-white px-4 text-sm text-zinc-900 outline-none transition placeholder:text-zinc-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:bg-zinc-800 dark:text-white
                        @error('titulo') border-red-400 @else border-zinc-300 dark:border-zinc-700 @enderror"
                >

                @error('titulo')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Descripción --}}
            <div>
                <label for="descripcion" class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                    Descripción
                </label>

                <textarea
                    id="descripcion"
                    name="descripcion"
                    rows="7"
                    maxlength="10000"
                    placeholder="Explica qué aprenderán los estudiantes..."
                    class="w-full rounded-[10px] border bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition placeholder:text-zinc-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:bg-zinc-800 dark:text-white
                        @error('descripcion') border-red-400 @else border-zinc-300 dark:border-zinc-700 @enderror"
                >{{ old('descripcion', $curso?->descripcion) }}</textarea>

                @error('descripcion')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Portada --}}
            <div>
                <label for="imagen" class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                    Portada
                </label>

                @if ($curso?->imagen_path)
                    <img
                        src="{{ asset('storage/'.$curso->imagen_path) }}"
                        alt="Portada actual"
                        class="mb-4 aspect-video w-full max-w-md rounded-[10px] object-cover"
                    >
                @endif

                <input
                    id="imagen"
                    name="imagen"
                    type="file"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    class="block w-full rounded-[10px] border border-zinc-300 bg-white text-sm text-zinc-600 file:mr-4 file:border-0 file:bg-[#102A56] file:px-4 file:py-3 file:font-semibold file:text-white hover:file:bg-[#173B72] dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300"
                >

                <p class="mt-2 text-xs text-zinc-500">
                    JPG, PNG o WEBP. Tamaño máximo: 4 MB.
                </p>

                @error('imagen')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    {{-- Configuración --}}
    <aside class="space-y-6">
        <section class="rounded-[10px] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <h2 class="text-lg font-bold text-zinc-900 dark:text-white">
                Configuración
            </h2>

            <div class="mt-6 space-y-5">
                {{-- Nivel --}}
                <div>
                    <label for="nivel" class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                        Nivel
                    </label>

                    <select
                        id="nivel"
                        name="nivel"
                        required
                        class="h-11 w-full rounded-[10px] border border-zinc-300 bg-white px-3 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    >
                        <option value="basico" @selected(old('nivel', $curso?->nivel ?? 'basico') === 'basico')>
                            Básico
                        </option>
                        <option value="intermedio" @selected(old('nivel', $curso?->nivel) === 'intermedio')>
                            Intermedio
                        </option>
                        <option value="avanzado" @selected(old('nivel', $curso?->nivel) === 'avanzado')>
                            Avanzado
                        </option>
                    </select>

                    @error('nivel')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Estado --}}
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
                        <option value="borrador" @selected(old('estado', $curso?->estado ?? 'borrador') === 'borrador')>
                            Borrador
                        </option>
                        <option value="publicado" @selected(old('estado', $curso?->estado) === 'publicado')>
                            Publicado
                        </option>
                        <option value="archivado" @selected(old('estado', $curso?->estado) === 'archivado')>
                            Archivado
                        </option>
                    </select>

                    @error('estado')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Orden --}}
                <div>
                    <label for="orden" class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                        Orden
                    </label>

                    <input
                        id="orden"
                        name="orden"
                        type="number"
                        value="{{ old('orden', $curso?->orden ?? 0) }}"
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
                href="{{ route('admin.cursos.index') }}"
                class="mt-3 inline-flex h-11 w-full items-center justify-center rounded-[10px] border border-zinc-300 px-5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800"
            >
                Cancelar
            </a>
        </section>
    </aside>
</div>