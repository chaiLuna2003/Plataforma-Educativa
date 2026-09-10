@props(['title' => null])

<x-layouts.app.sidebar :title="$title">
    <flux:main>
        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>