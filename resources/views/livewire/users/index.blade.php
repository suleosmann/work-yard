<div>
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
        <div class="res-grid">
            @foreach ($this->users as $user)
                <div class="res-card">
                    <div class="res-icon mono">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                    <div>
                        <div class="res-title">{{ $user->name }}</div>
                        <div class="res-sub mono">{{ $user->email }}</div>
                        @if ($user->phone)
                            <div class="res-sub mono">{{ $user->phone }}</div>
                        @endif
                        <div style="margin-top:6px;">
                            <span class="chip active" style="cursor:default;">{{ $user->roles->pluck('name')->first() ?? 'no role' }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

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
