<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ filled($title ?? null) ? $title.' — Workyard' : 'Workyard' }}</title>
    @vite(['resources/css/pm-tool.css'])
</head>
<body class="pm-tool">
    <div class="shell">
        <aside class="sidebar">
            <a href="{{ route('projects.index') }}" wire:navigate class="brand">
                <div class="brand-mark"></div>
                <div class="brand-name">WORKYARD</div>
            </a>

            <div>
                <div class="nav-label">Projects</div>
                <ul class="nav-list">
                    @foreach ($navProjects ?? \App\Models\Project::withCount('tasks')->orderBy('name')->get() as $navProject)
                        @php
                            $navDone = $navProject->tasks()->where('status', 'done')->count();
                            $navPct = $navProject->tasks_count ? (int) round(($navDone / $navProject->tasks_count) * 100) : 0;
                        @endphp
                        <li>
                            <a
                                href="{{ route('projects.board', $navProject) }}"
                                wire:navigate
                                class="nav-item {{ (($project ?? null)?->id === $navProject->id) ? 'active' : '' }}"
                            >
                                <div class="nav-item-row">
                                    <span>{{ $navProject->name }}</span>
                                    <span class="nav-count mono">{{ $navProject->tasks_count }}</span>
                                </div>
                                <div class="nav-progress"><i style="width:{{ $navPct }}%;"></i></div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="sidebar-footer mono">
                {{ auth()->user()->name }} &middot; {{ ucfirst(auth()->user()->getRoleNames()->first() ?? 'no role') }}<br>
                <a href="{{ route('profile.edit') }}" wire:navigate style="color:#D8CDC2;">Account settings</a>
                &middot;
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" style="background:none; border:none; color:#D8CDC2; font-family:inherit; font-size:inherit; cursor:pointer; padding:0; text-decoration:underline;">Log out</button>
                </form>
            </div>
        </aside>

        <div class="main">
            {{ $slot }}
        </div>
    </div>

    <div class="toast" id="pmToast" style="position:fixed; bottom:22px; left:50%; transform:translateX(-50%) translateY(12px); background:var(--ink); color:#fff; padding:10px 18px; border-radius:7px; font-size:12.5px; opacity:0; transition:all .18s ease; pointer-events:none; z-index:99;"></div>
    <script>
        document.addEventListener('livewire:init', () => {
            let toastTimer;
            Livewire.on('toast', (event) => {
                const message = Array.isArray(event) ? event[0]?.message : event.message;
                const el = document.getElementById('pmToast');
                el.textContent = message;
                el.style.opacity = '1';
                el.style.transform = 'translateX(-50%) translateY(0)';
                clearTimeout(toastTimer);
                toastTimer = setTimeout(() => {
                    el.style.opacity = '0';
                    el.style.transform = 'translateX(-50%) translateY(12px)';
                }, 2200);
            });
        });
    </script>
</body>
</html>
