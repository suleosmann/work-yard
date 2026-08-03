<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    @vite('resources/css/pm-tool.css')
</head>
<body class="pm-tool auth">
    <div class="auth-split">
        <aside class="auth-aside">
            <a href="{{ route('home') }}" class="brand" style="display:flex; align-items:center;" wire:navigate>
                <span class="brand-mark"></span>
                <span class="brand-name">WORKYARD</span>
            </a>
            <div class="aside-body">
                <span class="aside-eyebrow">Work-order system</span>
                <h2>Pick up where the last shift left off.</h2>
                <p>Sign in to see your tickets, move work through the pipeline, and keep the log honest.</p>
            </div>
        </aside>

        <main class="auth-main">
            <div class="auth-card">
                {{ $slot }}
            </div>
        </main>
    </div>

    @fluxScripts
</body>
</html>
