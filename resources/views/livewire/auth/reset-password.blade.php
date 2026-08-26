<?php

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user): void {
                $attributes = [
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ];

                if (
                    $user->invited_at !== null
                    && $user->email_verified_at === null
                ) {
                    $attributes['email_verified_at'] = now();
                }

                $user->forceFill($attributes)->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status !== Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash(
    'status',
    'Tu contraseña se restableció correctamente. Ya puedes iniciar sesión.'
);

        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div
    class="w-full"
    x-data="{
        showPassword: false,
        showConfirmation: false
    }"
>
    <div class="mb-8">
        <span class="mb-3 block text-sm font-semibold uppercase tracking-[0.18em] text-[#2B7A9B]">
            Seguridad de la cuenta
        </span>

        <h2 class="text-3xl font-bold tracking-tight text-[#102A56]">
            Crea tu contraseña
        </h2>

        <p class="mt-3 leading-7 text-slate-500">
            Elige una contraseña segura para completar la activación de tu cuenta.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-[10px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="resetPassword" class="space-y-5">
        {{-- Correo --}}
        <div>
            <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">
                Correo electrónico
            </label>

            <input
                wire:model="email"
                id="email"
                name="email"
                type="email"
                required
                readonly
                autocomplete="email"
                class="h-12 w-full cursor-not-allowed rounded-[10px] border border-slate-200 bg-slate-100 px-4 text-slate-600 outline-none"
            >

            @error('email')
                <p class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Contraseña --}}
        <div>
            <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">
                Nueva contraseña
            </label>

            <div class="relative">
                <input
                    wire:model="password"
                    id="password"
                    name="password"
                    x-bind:type="showPassword ? 'text' : 'password'"
                    required
                    autocomplete="new-password"
                    placeholder="Ingresa una contraseña segura"
                    class="h-12 w-full rounded-[10px] border border-slate-300 bg-white px-4 pr-24 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#3975D5] focus:ring-4 focus:ring-[#3975D5]/10"
                >

                <button
                    type="button"
                    x-on:click="showPassword = ! showPassword"
                    class="absolute inset-y-0 right-0 px-4 text-sm font-semibold text-[#2B7A9B] transition hover:text-[#102A56]"
                    x-text="showPassword ? 'Ocultar' : 'Mostrar'"
                ></button>
            </div>

            @error('password')
                <p class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Confirmación --}}
        <div>
            <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700">
                Confirmar contraseña
            </label>

            <div class="relative">
                <input
                    wire:model="password_confirmation"
                    id="password_confirmation"
                    name="password_confirmation"
                    x-bind:type="showConfirmation ? 'text' : 'password'"
                    required
                    autocomplete="new-password"
                    placeholder="Escribe nuevamente la contraseña"
                    class="h-12 w-full rounded-[10px] border border-slate-300 bg-white px-4 pr-24 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#3975D5] focus:ring-4 focus:ring-[#3975D5]/10"
                >

                <button
                    type="button"
                    x-on:click="showConfirmation = ! showConfirmation"
                    class="absolute inset-y-0 right-0 px-4 text-sm font-semibold text-[#2B7A9B] transition hover:text-[#102A56]"
                    x-text="showConfirmation ? 'Ocultar' : 'Mostrar'"
                ></button>
            </div>
        </div>

        <div class="rounded-[10px] border border-cyan-200 bg-cyan-50 px-4 py-3">
            <p class="text-sm leading-6 text-cyan-900">
                Usa al menos ocho caracteres. Recomendamos combinar mayúsculas,
                minúsculas, números y símbolos.
            </p>
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="resetPassword"
            class="flex h-12 w-full items-center justify-center rounded-[10px] bg-[#102A56] px-5 font-semibold text-white transition hover:bg-[#173B72] focus:outline-none focus:ring-4 focus:ring-[#3975D5]/20 disabled:cursor-not-allowed disabled:opacity-70"
        >
            <span wire:loading.remove wire:target="resetPassword">
                Establecer contraseña
            </span>

            <span wire:loading wire:target="resetPassword">
                Guardando...
            </span>
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Este enlace es personal y dejará de funcionar después de utilizarlo.
    </p>
</div>