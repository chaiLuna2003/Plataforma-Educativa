<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="scroll-smooth">

<head>
    @include('partials.head', [
    'title' => 'Plataforma Educativa',
    ])

    <meta
        name="description"
        content="Formación en línea para estudiantes y profesionales de la salud mediante la Plataforma Educativa CUCS.">

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body
    x-data="{ menuOpen: false }"
    class="min-h-screen bg-white font-sans text-slate-700 antialiased transition-colors duration-300 dark:bg-slate-950 dark:text-slate-200">
    <header class="fixed inset-x-0 top-0 z-50 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-950/90">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-5 sm:px-8 lg:px-10">
            <a
                href="{{ route('home') }}"
                class="flex items-center gap-3"
                aria-label="Ir al inicio">
                <span class="flex size-11 items-center justify-center rounded-[10px] bg-white p-1.5 shadow-sm ring-1 ring-slate-200 dark:ring-slate-700">
                    <img
                        src="{{ asset('images/cucs-logo.png') }}"
                        alt="Logotipo de CUCS"
                        class="size-full object-contain">
                </span>

                <span>
                    <span class="block text-sm font-bold tracking-[0.14em] text-cucs-navy dark:text-white">
                        CUCS
                    </span>
                    <span class="block text-xs text-slate-500 dark:text-slate-400">
                        Plataforma Educativa
                    </span>
                </span>
            </a>

            <nav class="hidden items-center gap-7 lg:flex" aria-label="Navegación principal">
                <a href="#beneficios" class="text-sm font-medium text-slate-600 transition hover:text-cucs-blue dark:text-slate-300 dark:hover:text-cucs-aqua-light">
                    Beneficios
                </a>
                <a href="#cursos" class="text-sm font-medium text-slate-600 transition hover:text-cucs-blue dark:text-slate-300 dark:hover:text-cucs-aqua-light">
                    Cursos
                </a>
                <a href="#testimonios" class="text-sm font-medium text-slate-600 transition hover:text-cucs-blue dark:text-slate-300 dark:hover:text-cucs-aqua-light">
                    Testimonios
                </a>
                <a href="#preguntas" class="text-sm font-medium text-slate-600 transition hover:text-cucs-blue dark:text-slate-300 dark:hover:text-cucs-aqua-light">
                    Preguntas
                </a>
            </nav>

            <div class="flex items-center gap-2 sm:gap-3">
                <button
                    type="button"
                    x-on:click="$flux.appearance = document.documentElement.classList.contains('dark') ? 'light' : 'dark'"
                    class="inline-flex size-10 items-center justify-center rounded-[10px] border border-slate-200 bg-white text-slate-600 transition hover:border-cucs-blue hover:text-cucs-blue focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cucs-blue dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-cucs-aqua-light dark:hover:text-cucs-aqua-light"
                    aria-label="Cambiar apariencia"
                    title="Cambiar apariencia">
                    <svg class="size-5 dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1.5M12 19.5V21M4.22 4.22l1.06 1.06M18.72 18.72l1.06 1.06M3 12h1.5M19.5 12H21M4.22 19.78l1.06-1.06M18.72 5.28l1.06-1.06" />
                        <circle cx="12" cy="12" r="4" />
                    </svg>
                    <svg class="hidden size-5 dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 15.75A9 9 0 0 1 8.25 3.75a9 9 0 1 0 12 12Z" />
                    </svg>
                </button>

                @auth
                <a
                    href="{{ route('dashboard') }}"
                    class="hidden rounded-[10px] bg-cucs-navy px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-cucs-blue focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cucs-blue sm:inline-flex">
                    Ir al panel
                </a>
                @else
                <a
                    href="{{ route('login') }}"
                    class="hidden rounded-[10px] bg-cucs-navy px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-cucs-blue focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cucs-blue sm:inline-flex">
                    Iniciar sesión
                </a>
                @endauth

                <button
                    type="button"
                    x-on:click="menuOpen = ! menuOpen"
                    x-bind:aria-expanded="menuOpen"
                    aria-controls="mobile-navigation"
                    aria-label="Abrir menú"
                    class="inline-flex size-10 items-center justify-center rounded-[10px] border border-slate-200 text-slate-700 lg:hidden dark:border-slate-700 dark:text-white">
                    <svg x-show="! menuOpen" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                    <svg x-cloak x-show="menuOpen" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" d="m6 6 12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>
        </div>

        <nav
            id="mobile-navigation"
            x-cloak
            x-show="menuOpen"
            x-transition
            x-on:click.outside="menuOpen = false"
            class="border-t border-slate-200 bg-white px-5 py-5 lg:hidden dark:border-slate-800 dark:bg-slate-950"
            aria-label="Navegación móvil">
            <div class="mx-auto grid max-w-7xl gap-2">
                <a x-on:click="menuOpen = false" href="#beneficios" class="rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-900">Beneficios</a>
                <a x-on:click="menuOpen = false" href="#cursos" class="rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-900">Cursos</a>
                <a x-on:click="menuOpen = false" href="#testimonios" class="rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-900">Testimonios</a>
                <a x-on:click="menuOpen = false" href="#preguntas" class="rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-900">Preguntas</a>

                @auth
                <a href="{{ route('dashboard') }}" class="mt-2 rounded-[10px] bg-cucs-navy px-4 py-3 text-center text-sm font-semibold text-white">Ir al panel</a>
                @else
                <a href="{{ route('login') }}" class="mt-2 rounded-[10px] bg-cucs-navy px-4 py-3 text-center text-sm font-semibold text-white">Iniciar sesión</a>
                @endauth
            </div>
    </header>

    <a
        href="{{ $whatsappContactUrl }}"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Contactar por WhatsApp"
        title="Contactar por WhatsApp"
        class="fixed bottom-4 right-4 z-40 inline-flex items-center gap-2 rounded-full bg-emerald-500 p-3.5 text-white shadow-lg shadow-emerald-950/20 transition hover:-translate-y-0.5 hover:bg-emerald-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500 sm:right-6 sm:px-4">
        <svg
            class="size-5"
            viewBox="0 0 24 24"
            fill="currentColor"
            aria-hidden="true">
            <path d="M12.04 2a9.84 9.84 0 0 0-8.48 14.81L2.05 22l5.31-1.47A9.94 9.94 0 1 0 12.04 2Zm0 17.86a7.79 7.79 0 0 1-3.97-1.09l-.28-.17-3.15.87.84-3.07-.19-.31a7.74 7.74 0 1 1 6.75 3.77Zm4.27-5.81c-.23-.12-1.38-.68-1.59-.76-.22-.08-.37-.12-.53.12-.16.23-.61.76-.75.92-.14.16-.28.18-.51.06a6.34 6.34 0 0 1-1.87-1.15 7.01 7.01 0 0 1-1.29-1.61c-.14-.23-.02-.36.1-.48.11-.1.24-.27.35-.41.12-.14.16-.23.24-.39.08-.16.04-.29-.02-.41-.06-.12-.53-1.27-.72-1.74-.19-.46-.38-.4-.53-.41h-.45a.86.86 0 0 0-.63.29c-.22.23-.83.81-.83 1.98s.85 2.3.97 2.46c.12.16 1.67 2.55 4.05 3.58.57.24 1.01.39 1.35.5.57.18 1.08.15 1.49.09.45-.07 1.38-.57 1.58-1.11.2-.55.2-1.02.14-1.11-.06-.1-.22-.16-.45-.27Z" />
        </svg>

        <span class="hidden text-sm font-semibold sm:inline">
            WhatsApp
        </span>
    </a>

    <main>

        <main>
            <section class="relative overflow-hidden pb-20 pt-32 sm:pb-24 sm:pt-40 lg:pb-32 lg:pt-44">
                <div aria-hidden="true" class="absolute -right-40 top-20 size-[32rem] rounded-full bg-cucs-mint/70 blur-3xl dark:bg-cucs-aqua/10"></div>
                <div aria-hidden="true" class="absolute -left-52 top-80 size-[28rem] rounded-full bg-cucs-sky blur-3xl dark:bg-cucs-blue/10"></div>

                <div class="relative mx-auto grid max-w-7xl items-center gap-14 px-5 sm:px-8 lg:grid-cols-[1.08fr_0.92fr] lg:px-10">
                    <div class="max-w-3xl">
                        <span class="inline-flex items-center gap-2 rounded-full border border-cucs-aqua/25 bg-cucs-mint/70 px-4 py-2 text-sm font-semibold text-cucs-navy dark:border-cucs-aqua/30 dark:bg-cucs-aqua/10 dark:text-cucs-aqua-light">
                            <span class="size-2 rounded-full bg-cucs-aqua"></span>
                            Formación profesional en línea
                        </span>

                        <h1 class="mt-7 text-4xl font-semibold tracking-tight text-cucs-navy sm:text-5xl lg:text-6xl lg:leading-[1.08] dark:text-white">
                            Aprende hoy. Fortalece tu práctica para el futuro.
                        </h1>

                        <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600 dark:text-slate-300">
                            Plataforma Educativa CUCS reúne cursos especializados, contenido en video y seguimiento de progreso para acompañar tu desarrollo académico y profesional.
                        </p>

                        <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                            <a
                                href="#cursos"
                                class="inline-flex items-center justify-center rounded-[10px] bg-cucs-navy px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-cucs-navy/15 transition hover:-translate-y-0.5 hover:bg-cucs-blue focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cucs-blue">
                                Conoce nuestros cursos
                                <span class="ml-2" aria-hidden="true">→</span>
                            </a>
                            <a
                                href="{{ $whatsappTrialUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center justify-center rounded-[10px] border border-slate-300 bg-white px-6 py-3.5 text-sm font-semibold text-cucs-navy transition hover:-translate-y-0.5 hover:border-cucs-aqua hover:text-cucs-aqua dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:hover:border-cucs-aqua-light dark:hover:text-cucs-aqua-light">
                                Solicitar prueba gratuita
                            </a>
                        </div>

                        <div class="mt-10 flex flex-wrap gap-x-7 gap-y-3 text-sm text-slate-500 dark:text-slate-400">
                            <span class="inline-flex items-center gap-2"><span class="text-cucs-aqua" aria-hidden="true">✓</span> Acceso 24/7</span>
                            <span class="inline-flex items-center gap-2"><span class="text-cucs-aqua" aria-hidden="true">✓</span> Videos especializados</span>
                            <span class="inline-flex items-center gap-2"><span class="text-cucs-aqua" aria-hidden="true">✓</span> Progreso visible</span>
                        </div>
                    </div>

                    <div class="relative mx-auto w-full max-w-xl">
                        <div class="rounded-[24px] border border-slate-200 bg-white/90 p-5 shadow-2xl shadow-cucs-navy/10 backdrop-blur dark:border-slate-800 dark:bg-slate-900/90">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-5 dark:border-slate-800">
                                <div class="flex items-center gap-3">
                                    <span class="flex size-11 items-center justify-center rounded-[10px] bg-cucs-mint text-cucs-navy dark:bg-cucs-aqua/15 dark:text-cucs-aqua-light">
                                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v15H6.5A2.5 2.5 0 0 0 4 20.5v-15Z" />
                                            <path stroke-linecap="round" d="M4 20.5A2.5 2.5 0 0 1 6.5 18H20" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="font-semibold text-cucs-navy dark:text-white">Tu aprendizaje</p>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Claro, organizado y accesible</p>
                                    </div>
                                </div>
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">En línea</span>
                            </div>

                            <div class="mt-5 space-y-4">
                                <div class="rounded-[16px] bg-cucs-navy p-5 text-white">
                                    <p class="text-sm text-blue-100">Contenido disponible</p>
                                    <p class="mt-2 text-xl font-semibold">Aprende a tu propio ritmo</p>
                                    <div class="mt-5 h-2 overflow-hidden rounded-full bg-white/15">
                                        <div class="h-full w-2/3 rounded-full bg-cucs-aqua-light"></div>
                                    </div>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="rounded-[16px] border border-slate-200 p-4 dark:border-slate-700">
                                        <span class="text-2xl font-semibold text-cucs-navy dark:text-white">24/7</span>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Acceso desde cualquier dispositivo</p>
                                    </div>
                                    <div class="rounded-[16px] border border-slate-200 p-4 dark:border-slate-700">
                                        <span class="text-2xl font-semibold text-cucs-navy dark:text-white">100%</span>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Experiencia completamente en línea</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="beneficios" class="scroll-mt-24 bg-cucs-surface py-20 sm:py-24 dark:bg-slate-900/50">
                <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                    <div class="max-w-2xl">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-cucs-aqua">Una experiencia diseñada para avanzar</p>
                        <h2 class="mt-3 text-3xl font-semibold tracking-tight text-cucs-navy sm:text-4xl dark:text-white">Formación accesible, ordenada y enfocada en ti</h2>
                        <p class="mt-5 text-lg leading-8 text-slate-600 dark:text-slate-300">Estudia con claridad y consulta el contenido asignado a tu plan desde una experiencia sencilla y segura.</p>
                    </div>

                    <div class="mt-12 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
                        @foreach ([
                        ['01', 'Aprende a tu ritmo', 'Consulta tus lecciones cuando lo necesites y continúa desde cualquier dispositivo.'],
                        ['02', 'Contenido especializado', 'Accede a cursos y recursos seleccionados para fortalecer tu formación profesional.'],
                        ['03', 'Progreso organizado', 'Identifica las lecciones completadas y visualiza tu avance dentro de cada curso.'],
                        ['04', 'Acceso seguro', 'Tu cuenta y tu plan determinan el contenido disponible de forma privada.'],
                        ] as [$number, $title, $description])
                        <article class="rounded-[18px] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <span class="text-sm font-semibold text-cucs-aqua">{{ $number }}</span>
                            <h3 class="mt-5 text-lg font-semibold text-cucs-navy dark:text-white">{{ $title }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $description }}</p>
                        </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="cursos" class="scroll-mt-24 py-20 sm:py-24">
                <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                    <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                        <div class="max-w-2xl">
                            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-cucs-aqua">Catálogo académico</p>
                            <h2 class="mt-3 text-3xl font-semibold tracking-tight text-cucs-navy sm:text-4xl dark:text-white">Conoce nuestros cursos</h2>
                            <p class="mt-5 text-lg leading-8 text-slate-600 dark:text-slate-300">Explora una selección del contenido publicado y solicita información para encontrar el plan adecuado.</p>
                        </div>
                        <a href="{{ $whatsappTrialUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center text-sm font-semibold text-cucs-blue hover:text-cucs-aqua dark:text-cucs-aqua-light">
                            Solicitar orientación <span class="ml-2" aria-hidden="true">→</span>
                        </a>
                    </div>

                    @if ($cursosDestacados->isNotEmpty())
                    <div
                        @if ($cursosDestacados->count() > 3)
                        x-data
                        role="region"
                        aria-label="Carrusel de cursos publicados"
                        @endif
                        class="mt-12"
                        >
                        @if ($cursosDestacados->count() > 3)
                        <div class="mb-5 flex justify-end gap-2">
                            <button
                                type="button"
                                x-on:click="$refs.courseSlider.scrollBy({
                        left: -$refs.courseSlider.clientWidth * 0.85,
                        behavior: 'smooth'
                    })"
                                class="inline-flex size-10 items-center justify-center rounded-full border border-slate-300 text-cucs-navy transition hover:border-cucs-blue hover:text-cucs-blue focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cucs-blue dark:border-slate-700 dark:text-white dark:hover:border-cucs-aqua-light dark:hover:text-cucs-aqua-light"
                                aria-label="Ver cursos anteriores">
                                <span aria-hidden="true">←</span>
                            </button>

                            <button
                                type="button"
                                x-on:click="$refs.courseSlider.scrollBy({
                        left: $refs.courseSlider.clientWidth * 0.85,
                        behavior: 'smooth'
                    })"
                                class="inline-flex size-10 items-center justify-center rounded-full border border-slate-300 text-cucs-navy transition hover:border-cucs-blue hover:text-cucs-blue focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cucs-blue dark:border-slate-700 dark:text-white dark:hover:border-cucs-aqua-light dark:hover:text-cucs-aqua-light"
                                aria-label="Ver cursos siguientes">
                                <span aria-hidden="true">→</span>
                            </button>
                        </div>
                        @endif

                        <div
                            @if ($cursosDestacados->count() > 3)
                            x-ref="courseSlider"
                            @endif
                            @class([
                            'grid gap-6 md:grid-cols-2 lg:grid-cols-3' =>
                            $cursosDestacados->count() <= 3, 'flex snap-x snap-mandatory gap-6 overflow-x-auto pb-5'=>
                                $cursosDestacados->count() > 3,
                                ])
                                >
                                @foreach ($cursosDestacados as $curso)
                                @php
                                $courseWhatsappUrl =
                                'https://wa.me/'
                                .$whatsappNumber
                                .'?text='
                                .rawurlencode(
                                'Hola, quiero solicitar información sobre el curso: '
                                .$curso->titulo
                                .'.'
                                );
                                @endphp

                                <article
                                    @class([ 'group flex h-full flex-col overflow-hidden rounded-[18px] border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl dark:border-slate-800 dark:bg-slate-900' , 'w-[86%] shrink-0 snap-start sm:w-[48%] lg:w-[31.8%]'=>
                                    $cursosDestacados->count() > 3,
                                    ])
                                    >
                                    <div class="relative aspect-[16/10] overflow-hidden bg-cucs-sky dark:bg-slate-800">
                                        @if ($curso->imagen_path)
                                        <img
                                            src="{{ asset('storage/'.$curso->imagen_path) }}"
                                            alt="Portada de {{ $curso->titulo }}"
                                            class="size-full object-cover transition duration-500 group-hover:scale-105">
                                        @else
                                        <div class="flex size-full items-center justify-center bg-gradient-to-br from-cucs-sky via-white to-cucs-mint dark:from-slate-800 dark:via-slate-900 dark:to-cucs-aqua/20">
                                            <img
                                                src="{{ asset('images/cucs-logo.png') }}"
                                                alt=""
                                                class="size-24 object-contain opacity-80">
                                        </div>
                                        @endif

                                        <span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold capitalize text-cucs-navy shadow-sm backdrop-blur dark:bg-slate-950/85 dark:text-white">
                                            {{ $curso->nivel }}
                                        </span>
                                    </div>

                                    <div class="flex flex-1 flex-col p-6">
                                        <p class="text-sm font-medium text-cucs-aqua">
                                            {{ $curso->lecciones_publicadas_count }}

                                            {{
                                (int) $curso->lecciones_publicadas_count === 1
                                    ? 'lección'
                                    : 'lecciones'
                            }}
                                        </p>

                                        <h3 class="mt-2 text-xl font-semibold text-cucs-navy dark:text-white">
                                            {{ $curso->titulo }}
                                        </h3>

                                        <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-500 dark:text-slate-400">
                                            {{
                                \Illuminate\Support\Str::limit(
                                    $curso->descripcion,
                                    150
                                )
                            }}
                                        </p>

                                        <a
                                            href="{{ $courseWhatsappUrl }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="mt-6 inline-flex items-center justify-center rounded-[10px] border border-cucs-navy px-4 py-3 text-sm font-semibold text-cucs-navy transition hover:bg-cucs-navy hover:text-white dark:border-cucs-aqua-light dark:text-cucs-aqua-light dark:hover:bg-cucs-aqua-light dark:hover:text-cucs-navy">
                                            Solicitar información
                                        </a>
                                    </div>
                                </article>
                                @endforeach
                        </div>
                    </div>
                    @else
                    <div class="mt-12 rounded-[18px] border border-dashed border-slate-300 bg-cucs-surface px-6 py-14 text-center dark:border-slate-700 dark:bg-slate-900">
                        <h3 class="text-xl font-semibold text-cucs-navy dark:text-white">
                            Próximamente encontrarás nuevos cursos
                        </h3>

                        <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-500 dark:text-slate-400">
                            Estamos preparando la siguiente oferta académica.
                            Solicita información para conocer las próximas fechas.
                        </p>
                    </div>
                    @endif
                </div>
            </section>

            <section class="py-8 sm:py-12">
                <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                    <div class="relative overflow-hidden rounded-[24px] bg-cucs-navy px-6 py-12 text-white sm:px-10 lg:flex lg:items-center lg:justify-between lg:px-14 lg:py-14">
                        <div aria-hidden="true" class="absolute -right-20 -top-28 size-80 rounded-full bg-cucs-aqua/30 blur-3xl"></div>
                        <div class="relative max-w-2xl">
                            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-cucs-aqua-light">Conoce la experiencia</p>
                            <h2 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">Solicita una prueba gratuita</h2>
                            <p class="mt-4 text-base leading-7 text-blue-100">Escríbenos por WhatsApp y recibe orientación para conocer la plataforma y los cursos disponibles.</p>
                        </div>
                        <a
                            href="{{ $whatsappTrialUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="relative mt-8 inline-flex items-center justify-center rounded-[10px] bg-white px-6 py-3.5 text-sm font-semibold text-cucs-navy shadow-lg transition hover:-translate-y-0.5 hover:bg-cucs-mint lg:mt-0">
                            Solicitar prueba gratuita
                            <span class="ml-2" aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>
            </section>

            <section id="testimonios" class="scroll-mt-24 py-20 sm:py-24">
                <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                    <div class="mx-auto max-w-2xl text-center">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-cucs-aqua">Experiencias de aprendizaje</p>
                        <h2 class="mt-3 text-3xl font-semibold tracking-tight text-cucs-navy sm:text-4xl dark:text-white">Testimonios</h2>
                        <p class="mt-5 text-lg leading-8 text-slate-600 dark:text-slate-300">Este espacio está preparado para compartir experiencias verificadas y autorizadas de nuestra comunidad educativa.</p>
                    </div>

                    <div class="mt-12 grid gap-5 md:grid-cols-3">
                        @foreach ([
                        ['Estudiante', 'Experiencia académica'],
                        ['Profesional', 'Formación continua'],
                        ['Comunidad CUCS', 'Aprendizaje en línea'],
                        ] as [$profile, $context])
                        <article class="rounded-[18px] border border-slate-200 bg-cucs-surface p-6 dark:border-slate-800 dark:bg-slate-900">
                            <div class="flex gap-1 text-cucs-aqua" aria-label="Espacio para valoración verificada">
                                @for ($star = 0; $star < 5; $star++)
                                    <span aria-hidden="true">☆</span>
                                    @endfor
                            </div>
                            <p class="mt-5 text-base leading-7 text-slate-600 dark:text-slate-300">Testimonio verificado disponible próximamente.</p>
                            <div class="mt-6 border-t border-slate-200 pt-5 dark:border-slate-800">
                                <p class="font-semibold text-cucs-navy dark:text-white">{{ $profile }}</p>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $context }}</p>
                            </div>
                        </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="preguntas" class="scroll-mt-24 bg-cucs-surface py-20 sm:py-24 dark:bg-slate-900/50">
                <div class="mx-auto grid max-w-7xl gap-12 px-5 sm:px-8 lg:grid-cols-[0.72fr_1.28fr] lg:px-10">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-cucs-aqua">Resolvemos tus dudas</p>
                        <h2 class="mt-3 text-3xl font-semibold tracking-tight text-cucs-navy sm:text-4xl dark:text-white">Preguntas frecuentes</h2>
                        <p class="mt-5 text-base leading-7 text-slate-600 dark:text-slate-300">Si necesitas orientación personalizada, nuestro canal de WhatsApp está disponible para ayudarte.</p>
                        <a href="{{ $whatsappTrialUrl }}" target="_blank" rel="noopener noreferrer" class="mt-7 inline-flex items-center text-sm font-semibold text-cucs-blue hover:text-cucs-aqua dark:text-cucs-aqua-light">
                            Hablar con nosotros <span class="ml-2" aria-hidden="true">→</span>
                        </a>
                    </div>

                    <div class="space-y-3">
                        @foreach ([
                        ['¿Cómo obtengo acceso a la plataforma?', 'El acceso se proporciona mediante una cuenta autorizada. Nuestro equipo te orientará sobre los planes y cursos disponibles.'],
                        ['¿Puedo registrarme directamente?', 'Por ahora no existe registro público. Las cuentas de estudiantes son creadas y habilitadas de manera administrativa.'],
                        ['¿Dónde puedo ver mis cursos?', 'Después de iniciar sesión encontrarás en tu panel únicamente los cursos incluidos en tu plan activo.'],
                        ['¿Los cursos tienen horarios establecidos?', 'El contenido está disponible en línea para que avances a tu ritmo, de acuerdo con la vigencia y condiciones de tu acceso.'],
                        ['¿Puedo acceder desde mi teléfono?', 'Sí. La plataforma está diseñada para funcionar en teléfonos, tabletas y computadoras con un navegador actualizado.'],
                        ] as [$question, $answer])
                        <details class="group rounded-[14px] border border-slate-200 bg-white p-5 open:shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-semibold text-cucs-navy focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-cucs-blue dark:text-white">
                                {{ $question }}
                                <span class="text-xl font-normal text-cucs-aqua transition group-open:rotate-45" aria-hidden="true">+</span>
                            </summary>
                            <p class="mt-4 pr-8 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $answer }}</p>
                        </details>
                        @endforeach
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 bg-white py-10 dark:border-slate-800 dark:bg-slate-950">
            <div class="mx-auto flex max-w-7xl flex-col gap-7 px-5 sm:px-8 md:flex-row md:items-center md:justify-between lg:px-10">
                <div class="flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-[10px] bg-white p-1.5 ring-1 ring-slate-200 dark:ring-slate-700">
                        <img src="{{ asset('images/cucs-logo.png') }}" alt="Logotipo de CUCS" class="size-full object-contain">
                    </span>
                    <div>
                        <p class="font-semibold text-cucs-navy dark:text-white">Plataforma Educativa CUCS</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Centro Universitario al Cuidado de la Salud</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-x-6 gap-y-3 text-sm">
                    <a href="#cursos" class="text-slate-500 hover:text-cucs-blue dark:text-slate-400 dark:hover:text-cucs-aqua-light">Cursos</a>
                    <a href="#preguntas" class="text-slate-500 hover:text-cucs-blue dark:text-slate-400 dark:hover:text-cucs-aqua-light">Preguntas</a>
                    <a href="{{ $whatsappTrialUrl }}" target="_blank" rel="noopener noreferrer" class="text-slate-500 hover:text-cucs-blue dark:text-slate-400 dark:hover:text-cucs-aqua-light">WhatsApp</a>
                    <a href="{{ route('login') }}" class="font-semibold text-cucs-navy hover:text-cucs-blue dark:text-white dark:hover:text-cucs-aqua-light">Iniciar sesión</a>
                </div>
            </div>

            <div class="mx-auto mt-8 max-w-7xl border-t border-slate-200 px-5 pt-6 text-sm text-slate-400 sm:px-8 lg:px-10 dark:border-slate-800">
                © {{ now()->year }} CUCS. Todos los derechos reservados.
            </div>
        </footer>

        @fluxScripts
</body>

</html>