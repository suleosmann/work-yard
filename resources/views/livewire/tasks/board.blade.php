@php
    $avatarColors = ['#15110D', '#FF5A1F', '#C23E0A', '#8A7E72', '#4A4038', '#B4592E'];
@endphp
<div>
    <div class="topbar">
        <div class="topbar-left">
            <h1 class="display">{{ $project->name }}</h1>
            <div class="sub">{{ $project->description }}</div>
            <div class="progress-wrap">
                <div class="progress-track"><div class="progress-fill" style="width:{{ $this->progress['pct'] }}%;"></div></div>
                <div class="progress-text mono">{{ $this->progress['done'] }}/{{ $this->progress['total'] }} done &middot; {{ $this->progress['pct'] }}%</div>
            </div>
        </div>
        <div class="topbar-right">
            @can('create tasks')
                <button class="btn btn-primary" wire:click="$dispatch('open-new-task')">+ New task</button>
            @endcan
        </div>
    </div>

    <div class="tabs">
        <a href="{{ route('projects.board', $project) }}" wire:navigate class="tab active">Board</a>
        <a href="{{ route('projects.resources', $project) }}" wire:navigate class="tab">Resources</a>
    </div>

    <div class="toolbar">
        <input type="text" class="search-input" wire:model.live.debounce.300ms="search" placeholder="Search tasks...">
        <div class="chip-row">
            <div class="chip {{ $priority === 'all' ? 'active' : '' }}" wire:click="$set('priority', 'all')">All</div>
            <div class="chip {{ $priority === 'high' ? 'active' : '' }}" wire:click="$set('priority', 'high')">High</div>
            <div class="chip {{ $priority === 'medium' ? 'active' : '' }}" wire:click="$set('priority', 'medium')">Medium</div>
            <div class="chip {{ $priority === 'low' ? 'active' : '' }}" wire:click="$set('priority', 'low')">Low</div>
        </div>
        <div class="chip chip-toggle {{ $mine ? 'on' : '' }}" wire:click="$toggle('mine')">My tasks</div>
    </div>

    <div class="content">
        <div class="board">
            @foreach ($statuses as $status)
                @php $items = $this->columns->get($status->value, collect()); @endphp
                <div class="col">
                    <div class="col-head">
                        <span class="dot {{ $status->dotClass() }}"></span>
                        {{ $status->label() }}
                        <span class="col-count mono">{{ $items->count() }}</span>
                    </div>
                    <div class="col-body">
                        @forelse ($items as $task)
                            <button type="button" class="ticket" wire:click="$dispatch('open-task', { taskId: {{ $task->id }} })">
                                <div class="ticket-id mono">{{ $project->slug }}-{{ $task->id }}</div>
                                <div class="ticket-title">{{ $task->title }}</div>
                                <div class="ticket-meta">
                                    <div class="ticket-meta-left">
                                        <span class="priority priority-{{ $task->priority->value }}">{{ $task->priority->value }}</span>
                                        @if ($task->due_date)
                                            <span class="due {{ $task->isOverdue() ? 'overdue' : '' }}">{{ $task->isOverdue() ? 'Overdue ' : '' }}{{ $task->due_date->format('M j') }}</span>
                                        @endif
                                    </div>
                                    @if ($task->assignee)
                                        <span class="avatar" style="background:{{ $avatarColors[$task->assignee->id % count($avatarColors)] }};" title="{{ $task->assignee->name }}">{{ $task->assignee->initials() }}</span>
                                    @else
                                        <span class="avatar" style="background:#EAE1D6; color:#B4A798;">&mdash;</span>
                                    @endif
                                </div>

                                @if ($task->status->value === 'pending' && auth()->user()->can('advance task status'))
                                    <div class="ticket-moves">
                                        <span class="btn btn-primary btn-sm" wire:click.stop="move({{ $task->id }}, 'in_progress')">Start</span>
                                    </div>
                                @elseif ($task->status->value === 'in_progress' && auth()->user()->can('advance task status'))
                                    <div class="ticket-moves">
                                        <span class="btn btn-primary btn-sm" wire:click.stop="move({{ $task->id }}, 'qa')">Send to QA</span>
                                    </div>
                                @elseif ($task->status->value === 'qa' && auth()->user()->can('qa decide'))
                                    <div class="ticket-moves">
                                        <span class="btn btn-primary btn-sm" wire:click.stop="move({{ $task->id }}, 'done')">Pass</span>
                                        <span class="btn btn-ghost btn-sm" wire:click.stop="move({{ $task->id }}, 'in_progress')">Fail</span>
                                    </div>
                                @endif

                                @if ($task->status->value === 'done')
                                    <div class="stamp">DONE</div>
                                @endif
                            </button>
                        @empty
                            <div class="empty">No tasks here</div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <livewire:tasks.task-modal :project="$project" :key="'task-modal-'.$project->id" />
    <livewire:tasks.new-task-form :project="$project" :key="'new-task-'.$project->id" />
</div>
