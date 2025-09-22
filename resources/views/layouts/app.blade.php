<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ trim($__env->yieldContent('title')) ? $__env->yieldContent('title') . ' • ' : '' }}{{ config('app.name', 'Coil Project') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if (class_exists(\Livewire\Livewire::class))
        @livewireStyles
    @endif
</head>
<body class="min-h-screen" style="background-color: var(--bg-body); color: var(--text-primary)">
    <script>
        // Early theme bootstrapping to avoid flash
        (function(){
            try {
                var t = localStorage.getItem('theme');
                var sys = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                var theme = (t === 'light' || t === 'dark') ? t : sys;
                document.documentElement.setAttribute('data-theme', theme);
            } catch(e) {}
        })();
    </script>
    <header class="border-b" style="border-color: var(--panel-ring)">
        <nav class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ url('/') }}" class="font-semibold">{{ config('app.name', 'Coil Project') }}</a>
            <div class="flex items-center gap-4 text-sm">
                <a href="{{ url('/food') }}" class="underline">Food</a>
                <a href="{{ url('/places') }}" class="underline">Places</a>
            </div>
        </nav>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-6 lg:py-10">
        @yield('content')
    </main>

    @if (class_exists(\Livewire\Livewire::class))
        @livewireScripts
    @endif
</body>
</html>
