<div>
    <div class="topbar">
        <div class="topbar-left">
            <h1 class="display">Projects</h1>
            <div class="sub">Pick a project to open its board</div>
        </div>
        <div class="topbar-right">
            @can('create projects')
                <button class="btn btn-primary" wire:click="startCreate">+ New project</button>
            @endcan
        </div>
    </div>

    <div class="content">
        @if ($this->projects->isEmpty())
            <div class="empty">No projects yet.</div>
        @else
            <div class="project-grid">
                @foreach ($this->projects as $project)
                    @php
                        $done = $this->doneCount($project);
                        $pct = $project->tasks_count ? (int) round(($done / $project->tasks_count) * 100) : 0;
                    @endphp
                    <div class="project-card" style="position:relative;">
                        <a href="{{ route('projects.board', $project) }}" wire:navigate style="text-decoration:none; color:inherit; display:block;">
                            <div class="name">{{ $project->name }}</div>
                            <div class="desc">{{ $project->description ?: 'No description yet.' }}</div>
                            <div class="progress-wrap" style="margin-top:0;">
                                <div class="progress-track"><div class="progress-fill" style="width:{{ $pct }}%;"></div></div>
                                <div class="progress-text mono">{{ $done }}/{{ $project->tasks_count }} done &middot; {{ $pct }}%</div>
                            </div>
                        </a>
                        @can('edit projects')
                            <button
                                class="icon-btn"
                                style="position:absolute; top:12px; right:12px;"
                                title="Edit project"
                                wire:click="startEdit({{ $project->id }})"
                            >&#9998;</button>
                        @endcan
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if ($showForm)
        <div class="overlay" wire:click.self="cancel">
            <div class="modal">
                <div class="modal-head">
                    <h2 style="margin:0;">{{ $editing ? 'Edit project' : 'New project' }}</h2>
                    <button class="modal-close" wire:click="cancel">&times;</button>
                </div>
                <form wire:submit="save">
                    <div class="field-row">
                        <div class="field" style="width:100%;">
                            <label>Name</label>
                            <input type="text" wire:model="name" placeholder="e.g. Claims Disbursement System">
                            @error('name') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field" style="width:100%;">
                            <label>Description</label>
                            <textarea wire:model="description" placeholder="What is this project about?"></textarea>
                            @error('description') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="divider"></div>
                    <div class="action-row">
                        <button type="submit" class="btn btn-primary btn-sm">{{ $editing ? 'Save changes' : 'Create project' }}</button>
                        <button type="button" class="btn btn-ghost btn-sm" wire:click="cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
