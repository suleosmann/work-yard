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

    {{-- ===== Orange hero band ===== --}}
    <header class="hero-band">
        <div class="wrap">
            <nav class="mkt-nav">
                <a href="{{ url('/') }}" class="brand" style="display:flex; align-items:center;">
                    <span class="brand-mark"></span>
                    <span class="brand-name">WORKYARD</span>
                </a>
                @auth
                    <a href="{{ route('projects.index') }}" wire:navigate class="btn btn-ondark">Open the board</a>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="btn btn-ondark">Log in</a>
                @endauth
            </nav>

            <div class="hero">
                <div class="hero-copy">
                    <span class="eyebrow">Work-order system</span>
                    <h1>Every task is a job ticket. Every move is on the record.</h1>
                    <p>
                        Workyard is a lean Kanban board for small teams — tasks travel a fixed
                        pipeline, each step logs itself, and who can move what is enforced by real
                        role permissions, not the honor system.
                    </p>
                    <div class="actions">
                        @auth
                            <a href="{{ route('projects.index') }}" wire:navigate class="btn btn-ondark btn-lg">Open the board</a>
                        @else
                            <a href="{{ route('login') }}" wire:navigate class="btn btn-ondark btn-lg">Log in</a>
                            <span class="note">Invite-only — ask your admin</span>
                        @endauth
                    </div>
                </div>

                {{-- Signature object: a job ticket --}}
                <div class="hero-ticket">
                    <span class="punch l"></span>
                    <span class="punch r"></span>
                    <div class="stub">
                        <span class="serial">WY-104</span>
                        <span class="status"><span class="dot dot-qa"></span> QA</span>
                    </div>
                    <div class="body">
                        <h4>Add adjustmentAmount validation</h4>
                        <div class="row">
                            <div class="ticket-meta-left">
                                <span class="priority priority-medium">medium</span>
                                <span class="due">Aug 5</span>
                            </div>
                            <span class="avatar" style="background:var(--orange-deep);">MU</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- ===== Pipeline strip (the real sequence) ===== --}}
    <section class="pipeline-band">
        <div class="wrap">
            <div class="pipeline">
                <div class="pipe-step"><span class="num">01</span><span class="label">Pending</span><span class="arrow">&rarr;</span></div>
                <div class="pipe-step"><span class="num">02</span><span class="label">In&nbsp;Progress</span><span class="arrow">&rarr;</span></div>
                <div class="pipe-step"><span class="num">03</span><span class="label">QA</span><span class="arrow">&rarr;</span></div>
                <div class="pipe-step"><span class="num">04</span><span class="label">Done</span><span class="arrow">&rarr;</span></div>
            </div>
        </div>
    </section>

    {{-- ===== Features ===== --}}
    <section class="section">
        <div class="wrap">
            <div class="section-head">
                <div class="section-label">What you get</div>
                <h2 class="section-title">Everything a small team needs, nothing it doesn't.</h2>
            </div>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="icon">BRD</div>
                    <h3>Kanban board</h3>
                    <p>Move tasks through Pending, In Progress, QA and Done with one-click buttons, priority filters, search, and a "my tasks" view.</p>
                </div>
                <div class="feature-card">
                    <div class="icon">PRM</div>
                    <h3>Real permissions</h3>
                    <p>Who can assign, edit, or pass QA is enforced by actual role permissions, checked on every action — not just hidden buttons.</p>
                </div>
                <div class="feature-card">
                    <div class="icon">LOG</div>
                    <h3>Activity log</h3>
                    <p>Every status change writes itself into the ticket's history alongside your comments, so nothing needs re-explaining.</p>
                </div>
                <div class="feature-card">
                    <div class="icon">RES</div>
                    <h3>Project resources</h3>
                    <p>Keep the staging URL, the docs, and the repo pinned to the project they belong to instead of scattered in chat.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== Roles as ticket stubs ===== --}}
    <section class="section roles-band">
        <div class="wrap">
            <div class="section-head">
                <div class="section-label">Roles</div>
                <h2 class="section-title">Four roles, each scoped to what they actually do.</h2>
            </div>
            <div class="role-grid">
                <div class="role-card">
                    <div class="role-name">Admin</div>
                    <ul>
                        <li>Full access to everything</li>
                        <li>Manages the team &amp; accounts</li>
                    </ul>
                </div>
                <div class="role-card">
                    <div class="role-name">Project manager</div>
                    <ul>
                        <li>Create &amp; edit projects</li>
                        <li>Create &amp; assign tasks</li>
                        <li>Edit title, priority, due date</li>
                        <li>Everything but QA decisions</li>
                    </ul>
                </div>
                <div class="role-card">
                    <div class="role-name">Developer</div>
                    <ul>
                        <li>Start work, send to QA</li>
                        <li>Comment on tasks</li>
                        <li>No assigning or field edits</li>
                    </ul>
                </div>
                <div class="role-card">
                    <div class="role-name">QA</div>
                    <ul>
                        <li>Pass a task to Done</li>
                        <li>Fail it back to In Progress</li>
                        <li>Comment on tasks</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== Orange CTA band ===== --}}
    <section class="cta-band">
        <div class="wrap">
            <h2>Clock in.</h2>
            <p>Access is by invitation. If your team runs on Workyard, sign in to pick up your tickets.</p>
            @auth
                <a href="{{ route('projects.index') }}" wire:navigate class="btn btn-ondark btn-lg">Open the board</a>
            @else
                <a href="{{ route('login') }}" wire:navigate class="btn btn-ondark btn-lg">Log in</a>
            @endauth
        </div>
    </section>

    <footer class="site-footer">
        <div class="wrap">
            <span>WORKYARD</span>
            <span>Project management, job-ticket style</span>
        </div>
    </footer>

</body>
</html>
