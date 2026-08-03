<?php

namespace App\Livewire\Tasks;

use App\Enums\TaskPriority;
use App\Models\Project;
use App\Models\User;
use App\Support\Permission;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class NewTaskForm extends Component
{
    public Project $project;

    public bool $show = false;

    public string $title = '';

    public string $priority = 'medium';

    public ?int $assigneeId = null;

    public ?string $dueDate = null;

    public string $description = '';

    #[On('open-new-task')]
    public function open(): void
    {
        $this->authorize(Permission::CREATE_TASKS);

        $this->reset(['title', 'priority', 'assigneeId', 'dueDate', 'description']);
        $this->priority = 'medium';
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
    }

    public function save(): void
    {
        $this->authorize(Permission::CREATE_TASKS);

        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'assigneeId' => 'nullable|exists:users,id',
            'dueDate' => 'nullable|date',
            'description' => 'nullable|string|max:4000',
        ]);

        $task = $this->project->tasks()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?: 'No description yet.',
            'status' => 'pending',
            'priority' => $validated['priority'],
            'assignee_id' => $validated['assigneeId'],
            'due_date' => $validated['dueDate'],
        ]);

        $this->dispatch('toast', message: "Task #{$task->id} created");
        $this->dispatch('task-updated');
        $this->close();
    }

    public function render(): View
    {
        return view('livewire.tasks.new-task-form', [
            'priorities' => TaskPriority::cases(),
            'users' => User::orderBy('name')->get(),
        ]);
    }
}
