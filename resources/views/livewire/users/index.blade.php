<div x-data="{ filters: false }">
    <div class="topbar">
        <div class="topbar-left">
            <h1 class="display">Users</h1>
            <div class="sub">Everyone with access to Workyard</div>
        </div>
        <div class="topbar-right">
            @can('manage users')
                <button class="btn btn-primary" wire:click="startCreate">+ New user</button>
            @endcan
        </div>
    </div>

    <div class="content">
        {{-- Summary cards --}}
        @php
            $byRole = $this->stats['byRole'];
            $contributors = ($byRole['developer'] ?? 0) + ($byRole['qa'] ?? 0);
        @endphp
        <div class="stat-row">
            <div class="stat-card accent">
                <div class="stat-label">Total users</div>
                <div class="stat-value">{{ $this->stats['total'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Admins</div>
                <div class="stat-value">{{ $byRole['admin'] ?? 0 }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Project managers</div>
                <div class="stat-value">{{ $byRole['pm'] ?? 0 }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Contributors</div>
                <div class="stat-value">{{ $contributors }}</div>
                <div class="stat-sub">developers &amp; QA</div>
            </div>
        </div>

        {{-- Toolbar --}}
        <div class="table-toolbar">
            <span class="table-count">{{ $this->users->total() }} {{ Str::plural('user', $this->users->total()) }}</span>
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
                        <th>User</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th class="right">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->users as $user)
                        @php $userRole = $user->roles->pluck('name')->first(); @endphp
                        <tr wire:key="user-{{ $user->id }}">
                            <td>
                                <div class="cell-title">{{ $user->name }}</div>
                                <div class="cell-sub mono">{{ $user->email }}</div>
                            </td>
                            <td class="cell-mono">{{ $user->phone ?: '—' }}</td>
                            <td>
                                <span class="role-badge {{ $userRole ? '' : 'none' }}">{{ $userRole ?? 'no role' }}</span>
                            </td>
                            <td class="right cell-mono">{{ $user->created_at?->format('M j, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="table-empty">
                                    @if ($this->activeFilterCount())
                                        No users match your filters.
                                    @else
                                        No users yet.
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($this->users->hasPages())
            <div class="pagination">
                <span class="range">Showing {{ $this->users->firstItem() }}&ndash;{{ $this->users->lastItem() }} of {{ $this->users->total() }}</span>
                <div class="pages">
                    <button class="page-btn" wire:click="previousPage" @disabled($this->users->onFirstPage())>Prev</button>
                    <span class="now">Page {{ $this->users->currentPage() }} / {{ $this->users->lastPage() }}</span>
                    <button class="page-btn" wire:click="nextPage" @disabled(! $this->users->hasMorePages())>Next</button>
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
                <h3>Filter users</h3>
            </div>
            <button class="modal-close" @click="filters = false" type="button">&times;</button>
        </div>
        <div class="drawer-body">
            <div class="filter-group">
                <span class="group-label">Search</span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Name or email&hellip;">
            </div>
            <div class="filter-group">
                <span class="group-label">Role</span>
                @foreach ($this->roles as $roleOption)
                    <label class="check-row">
                        <input type="checkbox" wire:model.live="roleFilter" value="{{ $roleOption }}">
                        {{ ucfirst($roleOption) }}
                    </label>
                @endforeach
            </div>
            <div class="filter-group">
                <span class="group-label">Sort by</span>
                <select wire:model.live="sort">
                    <option value="name">Name (A&ndash;Z)</option>
                    <option value="newest">Newest first</option>
                </select>
            </div>
        </div>
        <div class="drawer-foot">
            <button class="btn btn-ghost btn-sm" wire:click="clearFilters" type="button">Clear all</button>
            <button class="btn btn-primary btn-sm" @click="filters = false" type="button">Done</button>
        </div>
    </aside>

    {{-- Create user modal --}}
    @if ($showForm)
        <div class="overlay" wire:click.self="cancel">
            <div class="modal">
                <div class="modal-head">
                    <h2 style="margin:0;">New user</h2>
                    <button class="modal-close" wire:click="cancel">&times;</button>
                </div>
                <form wire:submit="save">
                    <div class="field-row">
                        <div class="field" style="width:100%;">
                            <label>Name</label>
                            <input type="text" wire:model="name" placeholder="e.g. Jane Doe">
                            @error('name') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label>Email</label>
                            <input type="email" wire:model="email" placeholder="jane@example.com">
                            @error('email') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div class="field">
                            <label>Phone</label>
                            <input type="text" wire:model="phone" placeholder="254700000000">
                            @error('phone') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field" style="width:100%;">
                            <label>Role</label>
                            <select wire:model="role">
                                <option value="">Select a role&hellip;</option>
                                @foreach ($this->roles as $roleOption)
                                    <option value="{{ $roleOption }}">{{ ucfirst($roleOption) }}</option>
                                @endforeach
                            </select>
                            @error('role') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="hint">Default password: <b>{{ \App\Livewire\Users\Index::DEFAULT_PASSWORD }}</b> — share this with the new user so they can log in.</div>
                    <div class="divider"></div>
                    <div class="action-row">
                        <button type="submit" class="btn btn-primary btn-sm">Create user</button>
                        <button type="button" class="btn btn-ghost btn-sm" wire:click="cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
