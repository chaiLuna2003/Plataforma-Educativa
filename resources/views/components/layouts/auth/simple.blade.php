<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>

    <body class="min-h-screen bg-slate-50 antialiased">
        <main class="grid min-h-screen lg:grid-cols-[1.05fr_0.95fr]">
            {{-- Panel institucional --}}
            <section
                class="relative hidden overflow-hidden bg-[#102A56] px-12 py-10 text-white lg:flex lg:flex-col lg:justify-between"
            >
                {{-- Decoración --}}
                <div
                    aria-hidden="true"
                    class="absolute -left-32 -top-32 h-96 w-96 rounded-full bg-[#3CC8C8]/20 blur-3xl"
                ></div>

                <div
                    aria-hidden="true"
                    class="absolute -bottom-36 -right-24 h-[30rem] w-[30rem] rounded-full bg-[#3975D5]/30 blur-3xl"
                ></div>

                <div
                    aria-hidden="true"
                    class="absolute inset-0 opacity-[0.07]"
                    style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 28px 28px;"
                ></div>

                {{-- Marca --}}
                <a href="{{ url('/') }}" class="relative z-10 inline-flex items-center gap-4">
                    <span class="flex size-16 items-center justify-center rounded-[10px] bg-white p-2 shadow-lg">
                        <img
                            src="{{ asset('images/cucs-logo.png') }}"
                            alt="Logotipo de CUCS"
                            class="h-full w-full object-contain"
                        >
                    </span>

                    <span>
                        <span class="block text-xl font-bold tracking-wide">
                            CUCS
                        </span>

                        <span class="block text-sm text-cyan-100">
                            Centro Universitario al Cuidado de la Salud
                        </span>
                    </span>
                </a>

                {{-- Mensaje principal --}}
                <div class="relative z-10 max-w-xl">
                    <span
                        class="mb-6 inline-flex rounded-full border border-cyan-200/30 bg-white/10 px-4 py-2 text-sm font-medium text-cyan-50 backdrop-blur"
                    >
                        Formación médica continua
                    </span>

                    <h1 class="text-4xl font-semibold leading-tight xl:text-5xl">
                        Aprende, actualízate y transforma tu práctica profesional.
                    </h1>

                    <p class="mt-6 max-w-lg text-lg leading-8 text-blue-100">
                        Accede a contenido educativo, cursos especializados y recursos
                        diseñados para fortalecer tu desarrollo profesional.
                    </p>

                    <div class="mt-10 grid max-w-lg grid-cols-3 gap-4">
                        <div class="rounded-[10px] border border-white/15 bg-white/10 p-4 backdrop-blur">
                            <span class="block text-2xl font-semibold">24/7</span>
                            <span class="mt-1 block text-sm text-blue-100">Acceso disponible</span>
                        </div>

                        <div class="rounded-[10px] border border-white/15 bg-white/10 p-4 backdrop-blur">
                            <span class="block text-2xl font-semibold">100%</span>
                            <span class="mt-1 block text-sm text-blue-100">En línea</span>
                        </div>

                        <div class="rounded-[10px] border border-white/15 bg-white/10 p-4 backdrop-blur">
                            <span class="block text-2xl font-semibold">CUCS</span>
                            <span class="mt-1 block text-sm text-blue-100">Comunidad educativa</span>
                        </div>
                    </div>
                </div>

                <p class="relative z-10 text-sm text-blue-200">
                    © {{ now()->year }} CUCS. Todos los derechos reservados.
                </p>
            </section>

            {{-- Panel del formulario --}}
            <section class="flex min-h-screen items-center justify-center px-6 py-10 sm:px-10 lg:px-16">
                <div class="w-full max-w-md">
                    {{-- Marca móvil --}}
                    <a href="{{ url('/') }}" class="mb-10 flex items-center gap-3 lg:hidden">
                        <span class="flex size-14 items-center justify-center rounded-[10px] bg-white p-2 shadow-sm ring-1 ring-slate-200">
                            <img
                                src="{{ asset('images/cucs-logo.png') }}"
                                alt="Logotipo de CUCS"
                                class="h-full w-full object-contain"
                            >
                        </span>

                        <span>
                            <span class="block font-bold text-[#102A56]">CUCS</span>
                            <span class="block text-xs text-slate-500">
                                Plataforma Educativa
                            </span>
                        </span>
                    </a>

                    {{ $slot }}

                    <p class="mt-10 text-center text-xs leading-5 text-slate-400 lg:hidden">
                        © {{ now()->year }} Centro Universitario al Cuidado de la Salud
                    </p>
                </div>
            </section>
        </main>

        @fluxScripts
    </body>
</html>