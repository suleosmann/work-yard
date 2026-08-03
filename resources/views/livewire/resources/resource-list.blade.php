<div>
    <div class="topbar">
        <div class="topbar-left">
            <h1 class="display">{{ $project->name }}</h1>
            <div class="sub">{{ $project->description }}</div>
        </div>
        <div class="topbar-right">
            @can('manage resources')
                <button class="btn btn-primary" wire:click="startCreate">+ New resource</button>
            @endcan
        </div>
    </div>

    <div class="tabs">
        <a href="{{ route('projects.board', $project) }}" wire:navigate class="tab">Board</a>
        <a href="{{ route('projects.resources', $project) }}" wire:navigate class="tab active">Resources</a>
    </div>

    <div class="content">
        @if ($this->resources->isEmpty())
            <div class="empty">No resources added for this project yet</div>
        @else
            <div class="res-grid">
                @foreach ($this->resources as $resource)
                    <div class="res-card">
                        <div class="res-icon mono">{{ $resource->type }}</div>
                        <div>
                            <div class="res-title">{{ $resource->title }}</div>
                            <div class="res-sub">{{ $resource->subtitle }}</div>
                        </div>
                        @can('manage resources')
                            <div class="res-actions">
                                <button class="icon-btn" title="Edit" wire:click="startEdit({{ $resource->id }})">&#9998;</button>
                                <button class="icon-btn" title="Delete" wire:click="delete({{ $resource->id }})" wire:confirm="Remove this resource?">&#128465;</button>
                            </div>
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
                    <h2 style="margin:0;">{{ $editing ? 'Edit resource' : 'New resource' }}</h2>
                    <button class="modal-close" wire:click="cancel">&times;</button>
                </div>
                <form wire:submit="save">
                    <div class="field-row">
                        <div class="field" style="width:100%;">
                            <label>Title</label>
                            <input type="text" wire:model="title" placeholder="e.g. Staging API base URL">
                            @error('title') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field" style="width:100%;">
                            <label>Subtitle / description</label>
                            <input type="text" wire:model="subtitle" placeholder="e.g. api-staging.claims.internal">
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label>Type label</label>
                            <input type="text" wire:model="type" placeholder="e.g. ENV, DOC, REP" maxlength="12">
                            @error('type') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="divider"></div>
                    <div class="action-row">
                        <button type="submit" class="btn btn-primary btn-sm">{{ $editing ? 'Save changes' : 'Add resource' }}</button>
                        <button type="button" class="btn btn-ghost btn-sm" wire:click="cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
