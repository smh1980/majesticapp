<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Majestic App.' }}</title>
        @vite(['resources/css/app.css', 'resources/css/custom.css', 'resources/js/app.js', ])
        @stack('styles')
        @livewireStyles
    </head>
    {{-- <body class="bg-slate-200 dark:bg-slate-700"> --}}
    <body class="bg-slate-200 dark:bg-slate-700 h-full overflow-x-hidden">
        @livewire('partials.navbar')
        <main>
            {{ $slot }}
        </main>
        @livewire('partials.footer')
        @livewireScripts
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <x-livewire-alert::scripts />
        {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}
    </body>
</html>
