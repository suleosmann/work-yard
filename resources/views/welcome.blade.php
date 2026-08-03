<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Workyard — Project management, job-ticket style</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    @vite(['resources/css/pm-tool.css'])
</head>
<body class="pm-tool site">
    <nav class="site-nav">
        <a href="{{ url('/') }}" class="brand">
            <div class="brand-mark" style="background:var(--ink);"></div>
        </a>
        <a href="{{ url('/') }}" class="brand" style="margin-inline-end:auto; margin-inline-start:9px;">
            <span class="brand-name" style="color:var(--ink);">WORKYARD</span>
        </a>
        @auth
            <a href="{{ route('projects.index') }}" wire:navigate class="btn btn-primary">Go to your projects</a>
        @else
            <a href="{{ route('login') }}" wire:navigate class="btn btn-primary">Log in</a>
        @endauth
    </nav>

    <main class="site-main">
        <section class="hero">
            <span class="eyebrow mono">Project management, job-ticket style</span>
            <h1>Run projects like a stack of job tickets, not a spreadsheet.</h1>
            <p>
                Workyard is a lean Kanban board for small teams — every task moves through
                Pending, In Progress, QA, and Done with a full activity log, and every move
                is enforced by real role permissions instead of an honor system.
            </p>
            <div class="actions">
                @auth
                    <a href="{{ route('projects.index') }}" wire:navigate class="btn btn-primary btn-lg">Go to your projects</a>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="btn btn-primary btn-lg">Log in</a>
                @endauth
            </div>
        </section>

        <div class="section-label mono">What you get</div>
        <h2 class="section-title">Everything a small team needs, nothing it doesn't</h2>
        <div class="feature-grid">
            <div class="feature-card">
                <div class="icon mono">BRD</div>
                <h3>Kanban board</h3>
                <p>Pending → In Progress → QA → Done, with simple move buttons, priority filters, search, and a "my tasks" view.</p>
            </div>
            <div class="feature-card">
                <div class="icon mono">PRM</div>
                <h3>Real permissions</h3>
                <p>Who can assign, edit, or QA-decide a task is enforced by actual role permissions, checked on every action — not just hidden buttons.</p>
            </div>
            <div class="feature-card">
                <div class="icon mono">LOG</div>
                <h3>Activity &amp; comments</h3>
                <p>Every status change is logged automatically alongside manual comments, so a task's history speaks for itself.</p>
            </div>
            <div class="feature-card">
                <div class="icon mono">RES</div>
                <h3>Project resources</h3>
                <p>Keep the staging URL, the docs link, and the repo attached to the project they belong to.</p>
            </div>
        </div>

        <div class="section-label mono">Roles</div>
        <h2 class="section-title">Four roles, clearly scoped</h2>
        <div class="role-grid">
            <div class="role-card">
                <div class="role-name mono">Admin</div>
                <ul>
                    <li>Full access to everything</li>
                </ul>
            </div>
            <div class="role-card">
                <div class="role-name mono">Project manager</div>
                <ul>
                    <li>Create &amp; edit projects</li>
                    <li>Create &amp; assign tasks</li>
                    <li>Edit title, priority, due date</li>
                    <li>Everything except QA decisions</li>
                </ul>
            </div>
            <div class="role-card">
                <div class="role-name mono">Developer</div>
                <ul>
                    <li>Move tasks Pending → QA</li>
                    <li>Add comments</li>
                    <li>Can't assign or edit priority/due date</li>
                </ul>
            </div>
            <div class="role-card">
                <div class="role-name mono">QA</div>
                <ul>
                    <li>Pass a task (QA → Done)</li>
                    <li>Fail a task (QA → In progress)</li>
                    <li>Add comments</li>
                </ul>
            </div>
        </div>
    </main>

    <footer class="site-footer mono">
        WORKYARD — access is by invitation only. Ask your admin for an account.
    </footer>
</body>
</html>
