<div>
    @if ($show)
        <div class="overlay" wire:click.self="close" wire:keydown.escape.window="close">
            <div class="modal">
                <div class="modal-head">
                    <h2 style="margin:0;">New task</h2>
                    <button class="modal-close" wire:click="close">&times;</button>
                </div>
                <form wire:submit="save">
                    <div class="field-row">
                        <div class="field" style="width:100%;">
                            <label>Title</label>
                            <input type="text" wire:model="title" placeholder="e.g. Add retry logic to webhook handler" autofocus>
                            @error('title') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label>Priority</label>
                            <select wire:model="priority">
                                @foreach ($priorities as $p)
                                    <option value="{{ $p->value }}">{{ $p->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Assignee</label>
                            <select wire:model="assigneeId">
                                <option value="">Unassigned</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label>Due date</label>
                            <input type="date" wire:model="dueDate">
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field" style="width:100%;">
                            <label>Description</label>
                            <textarea wire:model="description" placeholder="What needs to happen?"></textarea>
                        </div>
                    </div>
                    <div class="divider"></div>
                    <div class="action-row">
                        <button type="submit" class="btn btn-primary btn-sm">Create task</button>
                        <button type="button" class="btn btn-ghost btn-sm" wire:click="close">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
