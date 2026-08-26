<div class="mx-auto flex w-full max-w-3xl flex-col gap-6">
    {{-- Encabezado --}}
    <div>
        <a
            href="{{ route('admin.users.index') }}"
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

            Volver a usuarios
        </a>

        <p class="mt-6 text-sm font-semibold uppercase tracking-[0.16em] text-cyan-600">
            Administración
        </p>

        <h1 class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">
            Crear una cuenta
        </h1>

        <p class="mt-2 text-sm leading-6 text-zinc-500 dark:text-zinc-400">
            El usuario recibirá un correo con un enlace seguro para establecer su contraseña.
        </p>
    </div>

    <form
        wire:submit="save"
        class="overflow-hidden rounded-[10px] border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900"
    >
        <div class="space-y-6 p-6 sm:p-8">
            {{-- Nombre --}}
            <div>
                <label for="name" class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                    Nombre completo
                </label>

                <input
                    wire:model="name"
                    id="name"
                    name="name"
                    type="text"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Nombre del usuario"
                    class="h-12 w-full rounded-[10px] border border-zinc-300 bg-white px-4 text-zinc-900 outline-none transition placeholder:text-zinc-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                >

                @error('name')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Correo --}}
            <div>
                <label for="email" class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                    Correo electrónico
                </label>

                <input
                    wire:model="email"
                    id="email"
                    name="email"
                    type="email"
                    required
                    autocomplete="email"
                    placeholder="usuario@ejemplo.com"
                    class="h-12 w-full rounded-[10px] border border-zinc-300 bg-white px-4 text-zinc-900 outline-none transition placeholder:text-zinc-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                >

                @error('email')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Rol --}}
            <div>
                <label for="role" class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                    Tipo de cuenta
                </label>

                <select
                    wire:model="role"
                    id="role"
                    name="role"
                    required
                    class="h-12 w-full rounded-[10px] border border-zinc-300 bg-white px-4 text-zinc-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                >
                    <option value="{{ \App\Models\User::ROLE_STUDENT }}">
                        Estudiante
                    </option>

                    <option value="{{ \App\Models\User::ROLE_ADMIN }}">
                        Administrador
                    </option>
                </select>

                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                    Los administradores podrán gestionar cuentas y posteriormente cursos.
                </p>

                @error('role')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Estado --}}
            <div class="rounded-[10px] border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/60">
                <label class="flex cursor-pointer items-start gap-3">
                    <input
                        wire:model="isActive"
                        type="checkbox"
                        class="mt-1 size-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500"
                    >

                    <span>
                        <span class="block text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                            Activar cuenta inmediatamente
                        </span>

                        <span class="mt-1 block text-sm leading-5 text-zinc-500 dark:text-zinc-400">
                            Una cuenta inactiva no podrá iniciar sesión aunque establezca su contraseña.
                        </span>
                    </span>
                </label>

                @error('isActive')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="rounded-[10px] border border-cyan-200 bg-cyan-50 p-4 text-sm leading-6 text-cyan-900 dark:border-cyan-900 dark:bg-cyan-950/40 dark:text-cyan-100">
                No se enviará ninguna contraseña. El correo contendrá un enlace temporal y firmado para que el usuario cree la suya.
            </div>
        </div>

        {{-- Acciones --}}
        <div class="flex flex-col-reverse gap-3 border-t border-zinc-200 bg-zinc-50 px-6 py-4 sm:flex-row sm:justify-end dark:border-zinc-700 dark:bg-zinc-800/50">
            <a
                href="{{ route('admin.users.index') }}"
                wire:navigate
                class="inline-flex h-11 items-center justify-center rounded-[10px] border border-zinc-300 px-5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-600 dark:text-zinc-200 dark:hover:bg-zinc-700"
            >
                Cancelar
            </a>

            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="save"
                class="inline-flex h-11 items-center justify-center gap-2 rounded-[10px] bg-[#102A56] px-5 text-sm font-semibold text-white transition hover:bg-[#173B72] focus:outline-none focus:ring-4 focus:ring-blue-500/20 disabled:cursor-not-allowed disabled:opacity-70"
            >
                <span wire:loading.remove wire:target="save">
                    Crear y enviar invitación
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

                    Enviando...
                </span>
            </button>
        </div>
    </form>
</div>