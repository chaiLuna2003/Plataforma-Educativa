<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div
    class="w-full"
    x-data="{ showPassword: false }"
>
    <div class="mb-8">
        <span class="mb-3 block text-sm font-semibold uppercase tracking-[0.18em] text-[#2B7A9B]">
            Plataforma Educativa
        </span>

        <h2 class="text-3xl font-bold tracking-tight text-[#102A56]">
            Bienvenido de nuevo
        </h2>

        <p class="mt-3 leading-7 text-slate-500">
            Ingresa tus datos para acceder a tus cursos y continuar aprendiendo.
        </p>
    </div>

    {{-- Estado de la sesión --}}
    @if (session('status'))
        <div
            role="status"
            class="mb-6 rounded-[10px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"
        >
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="login" class="space-y-5">
        {{-- Correo electrónico --}}
        <div>
            <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">
                Correo electrónico
            </label>

            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <svg
                        class="size-5"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M21.75 6.75v10.5A2.25 2.25 0 0 1 19.5 19.5h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0-8.659 5.68a2.25 2.25 0 0 1-2.182 0L2.25 6.75"
                        />
                    </svg>
                </span>

                <input
                    wire:model="email"
                    id="email"
                    name="email"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="correo@ejemplo.com"
                    class="h-12 w-full rounded-[10px] border border-slate-300 bg-white py-3 pl-12 pr-4 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#3975D5] focus:ring-4 focus:ring-[#3975D5]/10"
                >
            </div>

            @error('email')
                <p class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Contraseña --}}
        <div>
            <div class="mb-2 flex items-center justify-between gap-4">
                <label for="password" class="block text-sm font-semibold text-slate-700">
                    Contraseña
                </label>

                @if (Route::has('password.request'))
                    <a
                        href="{{ route('password.request') }}"
                        wire:navigate
                        class="text-sm font-semibold text-[#2B7A9B] transition hover:text-[#102A56] hover:underline"
                    >
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <svg
                        class="size-5"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M16.5 10.5V6.75a4.5 4.5 0 0 0-9 0v3.75m-.75 0h10.5A2.25 2.25 0 0 1 19.5 12.75v6A2.25 2.25 0 0 1 17.25 21H6.75a2.25 2.25 0 0 1-2.25-2.25v-6a2.25 2.25 0 0 1 2.25-2.25Z"
                        />
                    </svg>
                </span>

                <input
                    wire:model="password"
                    id="password"
                    name="password"
                    x-bind:type="showPassword ? 'text' : 'password'"
                    required
                    autocomplete="current-password"
                    placeholder="Ingresa tu contraseña"
                    class="h-12 w-full rounded-[10px] border border-slate-300 bg-white py-3 pl-12 pr-12 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#3975D5] focus:ring-4 focus:ring-[#3975D5]/10"
                >

                <button
                    type="button"
                    x-on:click="showPassword = ! showPassword"
                    class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition hover:text-[#102A56]"
                    x-bind:aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                >
                    <svg
                        x-show="! showPassword"
                        class="size-5"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .638C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                        />
                    </svg>

                    <svg
                        x-show="showPassword"
                        x-cloak
                        class="size-5"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243"
                        />
                    </svg>
                </button>
            </div>

            @error('password')
                <p class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Mantener sesión --}}
        <label class="inline-flex cursor-pointer items-center gap-3">
            <input
                wire:model="remember"
                type="checkbox"
                class="size-4 rounded border-slate-300 text-[#3975D5] focus:ring-[#3975D5]"
            >

            <span class="text-sm text-slate-600">
                Mantener mi sesión iniciada
            </span>
        </label>

        {{-- Botón --}}
        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="login"
            class="flex h-12 w-full items-center justify-center rounded-[10px] bg-[#102A56] px-5 font-semibold text-white shadow-sm transition hover:bg-[#173B72] focus:outline-none focus:ring-4 focus:ring-[#3975D5]/20 disabled:cursor-not-allowed disabled:opacity-70"
        >
            <span wire:loading.remove wire:target="login">
                Iniciar sesión
            </span>

            <span wire:loading.flex wire:target="login" class="items-center gap-2">
                <svg
                    class="size-5 animate-spin"
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

                Ingresando...
            </span>
        </button>
    </form>

    <div class="mt-8 rounded-[10px] border border-slate-200 bg-slate-100/70 px-4 py-3 text-center">
        <p class="text-sm text-slate-600">
            ¿Necesitas acceso?
            <span class="font-semibold text-[#102A56]">
                Contacta al administrador de la plataforma.
            </span>
        </p>
    </div>
</div>
