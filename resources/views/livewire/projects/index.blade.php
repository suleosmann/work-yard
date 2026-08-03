<div x-data="{ filters: false }">
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
        {{-- Summary cards --}}
        <div class="stat-row">
            <div class="stat-card accent">
                <div class="stat-label">Projects</div>
                <div class="stat-value">{{ $this->stats['projects'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total tasks</div>
                <div class="stat-value">{{ $this->stats['tasks'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Completed</div>
                <div class="stat-value">{{ $this->stats['done'] }}</div>
                <div class="stat-sub">{{ $this->stats['done_pct'] }}% of all tasks</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Open</div>
                <div class="stat-value">{{ $this->stats['tasks'] - $this->stats['done'] }}</div>
                <div class="stat-sub">still in the pipeline</div>
            </div>
        </div>

        {{-- Toolbar --}}
        <div class="table-toolbar">
            <span class="table-count">{{ $this->projects->total() }} {{ Str::plural('project', $this->projects->total()) }}</span>
            <button class="btn btn-ghost filter-btn" @click="filters = true" type="button">
                Filter
                @if ($this->activeFilterCount())
                    <span class="fdot">{{ $this->activeFilterCount() }}</span>
                @endif
            </button>
        </div>

        {{-- Table --}}
        <div class="wy-table-wrap">
            <table class="wy-table">
                <thead>
                    <tr>
                        <th>Ref</th>
                        <th>Project</th>
                        <th class="right">Tasks</th>
                        <th>Progress</th>
                        @can('edit projects')<th class="right">&nbsp;</th>@endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->projects as $project)
                        @php $pct = $project->tasks_count ? (int) round(($project->done_tasks_count / $project->tasks_count) * 100) : 0; @endphp
                        <tr wire:key="project-{{ $project->id }}">
                            <td class="cell-mono">{{ $project->slug }}</td>
                            <td>
                                <a href="{{ route('projects.board', $project) }}" wire:navigate class="cell-title">{{ $project->name }}</a>
                                @if ($project->description)
                                    <div class="cell-sub">{{ Str::limit($project->description, 64) }}</div>
                                @endif
                            </td>
                            <td class="right cell-mono">{{ $project->tasks_count }}</td>
                            <td>
                                <div class="prog-cell">
                                    <span class="mini-track"><i style="width:{{ $pct }}%;"></i></span>
                                    <span class="cell-mono">{{ $project->done_tasks_count }}/{{ $project->tasks_count }}</span>
                                </div>
                            </td>
                            @can('edit projects')
                                <td class="right">
                                    <button class="icon-btn" title="Edit project" wire:click="startEdit({{ $project->id }})">&#9998;</button>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="table-empty">
                                    @if ($this->activeFilterCount())
                                        No projects match your filters.
                                    @else
                                        No projects yet.
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($this->projects->hasPages())
            <div class="pagination">
                <span class="range">Showing {{ $this->projects->firstItem() }}&ndash;{{ $this->projects->lastItem() }} of {{ $this->projects->total() }}</span>
                <div class="pages">
                    <button class="page-btn" wire:click="previousPage" @disabled($this->projects->onFirstPage())>Prev</button>
                    <span class="now">Page {{ $this->projects->currentPage() }} / {{ $this->projects->lastPage() }}</span>
                    <button class="page-btn" wire:click="nextPage" @disabled(! $this->projects->hasMorePages())>Next</button>
                </div>
            </div>
        @endif
    </div>

    {{-- Slide-in filter drawer --}}
    <div x-show="filters" x-transition.opacity class="drawer-backdrop" @click="filters = false" x-cloak></div>
    <aside class="filter-drawer" :class="{ open: filters }">
        <div class="drawer-head">
            <div>
                <div class="kicker">Refine</div>
                <h3>Filter projects</h3>
            </div>
            <button class="modal-close" @click="filters = false" type="button">&times;</button>
        </div>
        <div class="drawer-body">
            <div class="filter-group">
                <span class="group-label">Search</span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Name or description&hellip;">
            </div>
            <div class="filter-group">
                <span class="group-label">Sort by</span>
                <select wire:model.live="sort">
                    <option value="name">Name (A&ndash;Z)</option>
                    <option value="tasks">Most tasks</option>
                    <option value="newest">Newest first</option>
                </select>
            </div>
        </div>
        <div class="drawer-foot">
            <button class="btn btn-ghost btn-sm" wire:click="clearFilters" type="button">Clear all</button>
            <button class="btn btn-primary btn-sm" @click="filters = false" type="button">Done</button>
        </div>
    </aside>

    {{-- Create / edit modal --}}
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
                            <input type="text" wire:model="name" placeholder="Project name">
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
