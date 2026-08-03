<?php

namespace App\Livewire\Tasks;

use App\Actions\MoveTaskStatus;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use RuntimeException;

#[Layout('layouts.pm')]
class Board extends Component
{
    public Project $project;

    #[Url]
    public string $search = '';

    #[Url]
    public string $priority = 'all';

    #[Url]
    public bool $mine = false;

    public function mount(Project $project): void
    {
        $this->project = $project;
    }

    #[Computed]
    public function title(): string
    {
        return $this->project->name;
    }

    /**
     * @return Collection<int, Task>
     */
    #[Computed]
    public function tasks(): Collection
    {
        $query = $this->project->tasks()->with('assignee')->latest('id');

        if ($this->priority !== 'all') {
            $query->where('priority', $this->priority);
        }

        if ($this->mine) {
            $query->where('assignee_id', auth()->id());
        }

        if (filled($this->search)) {
            $needle = mb_strtolower($this->search);
            $query->where(function ($q) use ($needle) {
                $q->whereRaw('lower(title) like ?', ["%{$needle}%"])
                    ->orWhereRaw('lower(id) like ?', ["%{$needle}%"]);
            });
        }

        return $query->get();
    }

    /**
     * @return \Illuminate\Support\Collection<string, Collection<int, Task>>
     */
    #[Computed]
    public function columns(): \Illuminate\Support\Collection
    {
        return $this->tasks()->groupBy(fn (Task $task) => $task->status->value);
    }

    /**
     * @return array{done: int, total: int, pct: int}
     */
    #[Computed]
    public function progress(): array
    {
        $total = $this->project->tasks()->count();
        $done = $this->project->tasks()->where('status', 'done')->count();

        return [
            'done' => $done,
            'total' => $total,
            'pct' => $total ? (int) round(($done / $total) * 100) : 0,
        ];
    }

    public function move(int $taskId, string $to): void
    {
        $task = $this->project->tasks()->findOrFail($taskId);

        try {
            (new MoveTaskStatus)($task, TaskStatus::from($to), auth()->user());
            $this->dispatch('toast', message: "{$task->id} moved to ".TaskStatus::from($to)->label());
        } catch (RuntimeException|AuthorizationException $e) {
            $this->dispatch('toast', message: $e->getMessage());
        }
    }

    #[On('task-updated')]
    #[On('task-deleted')]
    public function refreshBoard(): void
    {
        //
    }

    public function render(): View
    {
        return view('livewire.tasks.board', [
            'statuses' => TaskStatus::cases(),
        ])->title($this->project->name.' — Board');
    }
}
