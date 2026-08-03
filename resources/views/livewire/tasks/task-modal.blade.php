<div>
    @if ($show && $task)
        @php $canEdit = auth()->user()->can('edit task fields'); @endphp
        <div class="overlay" wire:click.self="close" wire:keydown.escape.window="close">
            <div class="modal">
                <div class="modal-head">
                    <div style="flex:1;">
                        <div class="ticket-id mono">{{ $project->slug }}-{{ $task->id }}</div>
                        @if ($canEdit)
                            <input class="title-input" wire:model.blur="title">
                        @else
                            <h2>{{ $task->title }}</h2>
                        @endif
                    </div>
                    <div class="modal-head-actions">
                        @can('delete tasks')
                            <button class="icon-btn" title="Delete task" wire:click="delete" wire:confirm="Delete this task?">&#128465;</button>
                        @endcan
                        <button class="modal-close" wire:click="close">&times;</button>
                    </div>
                </div>

                <div class="field-row">
                    <div class="field">
                        <label>Status</label>
                        <div class="val">{{ $task->status->label() }}</div>
                    </div>
                    <div class="field">
                        <label>Priority</label>
                        @if ($canEdit)
                            <select wire:model.live="priority">
                                @foreach ($priorities as $p)
                                    <option value="{{ $p->value }}">{{ $p->label() }}</option>
                                @endforeach
                            </select>
                        @else
                            <div class="val" style="text-transform:capitalize;">{{ $task->priority->label() }}</div>
                        @endif
                    </div>
                </div>

                <div class="field-row">
                    <div class="field">
                        <label>Assignee</label>
                        <select wire:model.live="assigneeId" @disabled(! auth()->user()->can('assign tasks'))>
                            <option value="">Unassigned</option>
                            @foreach (\App\Models\User::orderBy('name')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Due date</label>
                        <input type="date" wire:model.blur="dueDate" @disabled(! $canEdit)>
                    </div>
                </div>

                <p style="font-size:13px; line-height:1.5; color:#4A4038;">{{ $task->description }}</p>

                <div class="divider"></div>

                <div class="action-row">
                    @if ($task->status->value === 'pending')
                        @can('advance task status')
                            <button class="btn btn-primary btn-sm" wire:click="move('in_progress')">Start task</button>
                        @else
                            <span class="hint" style="margin:0;">Your role can't change this task's status.</span>
                        @endcan
                    @elseif ($task->status->value === 'in_progress')
                        @can('advance task status')
                            <button class="btn btn-primary btn-sm" wire:click="move('qa')">Send to QA</button>
                        @else
                            <span class="hint" style="margin:0;">Your role can't change this task's status.</span>
                        @endcan
                    @elseif ($task->status->value === 'qa')
                        @can('qa decide')
                            <button class="btn btn-primary btn-sm" wire:click="move('done')">Pass</button>
                            <button class="btn btn-ghost btn-sm" wire:click="move('in_progress')">Fail &mdash; send back</button>
                        @else
                            <span class="hint" style="margin:0;">Only QA or Admin can pass/fail this task.</span>
                        @endcan
                    @elseif ($task->status->value === 'done')
                        <span class="hint" style="margin:0;">Task complete.</span>
                    @endif
                </div>

                <div class="comment-box">
                    <div class="nav-label" style="color:#8A7E72;">Activity</div>
                    <div class="comment-list">
                        @forelse ($task->activities as $activity)
                            <div class="comment"><b>{{ $activity->user?->name ?? 'System' }}:</b> {{ $activity->body }}</div>
                        @empty
                            <div class="hint" style="margin:0;">No activity yet.</div>
                        @endforelse
                    </div>
                    @can('add comments')
                        <textarea wire:model="newComment" placeholder="Add a comment..."></textarea>
                        @error('newComment') <div class="error">{{ $message }}</div> @enderror
                        <div style="margin-top:8px; text-align:right;">
                            <button class="btn btn-primary btn-sm" wire:click="addComment">Post</button>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    @endif
</div>
