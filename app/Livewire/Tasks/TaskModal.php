<?php

namespace App\Livewire\Tasks;

use App\Actions\MoveTaskStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Support\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use RuntimeException;

class TaskModal extends Component
{
    public Project $project;

    public bool $show = false;

    public ?Task $task = null;

    public string $title = '';

    public string $priority = '';

    public ?int $assigneeId = null;

    public ?string $dueDate = null;

    public string $newComment = '';

    #[On('open-task')]
    public function open(int $taskId): void
    {
        $this->task = $this->project->tasks()->with(['assignee', 'activities.user'])->findOrFail($taskId);
        $this->syncFields();
        $this->show = true;
    }

    protected function syncFields(): void
    {
        $this->title = $this->task->title;
        $this->priority = $this->task->priority->value;
        $this->assigneeId = $this->task->assignee_id;
        $this->dueDate = $this->task->due_date?->format('Y-m-d');
        $this->newComment = '';
    }

    public function close(): void
    {
        $this->show = false;
        $this->task = null;
    }

    public function updatedTitle(string $value): void
    {
        if (! auth()->user()->can(Permission::EDIT_TASK_FIELDS)) {
            $this->syncFields();

            return;
        }

        $value = trim($value);

        if ($value === '') {
            $this->syncFields();

            return;
        }

        $this->task->update(['title' => $value]);
        $this->dispatch('task-updated');
    }

    public function updatedPriority(string $value): void
    {
        if (! auth()->user()->can(Permission::EDIT_TASK_FIELDS)) {
            $this->syncFields();

            return;
        }

        $this->task->update(['priority' => TaskPriority::from($value)]);
        $this->dispatch('task-updated');
    }

    public function updatedDueDate(?string $value): void
    {
        if (! auth()->user()->can(Permission::EDIT_TASK_FIELDS)) {
            $this->syncFields();

            return;
        }

        $this->task->update(['due_date' => $value ?: null]);
        $this->dispatch('task-updated');
    }

    public function updatedAssigneeId(int|string|null $value): void
    {
        if (! auth()->user()->can(Permission::ASSIGN_TASKS)) {
            $this->syncFields();

            return;
        }

        $this->task->update(['assignee_id' => $value ?: null]);
        $this->task->refresh();
        $this->dispatch('toast', message: $this->task->assignee ? "Assigned to {$this->task->assignee->name}" : 'Unassigned');
        $this->dispatch('task-updated');
    }

    public function move(string $to): void
    {
        try {
            (new MoveTaskStatus)($this->task, TaskStatus::from($to), auth()->user());
            $this->task->refresh();
            $this->task->load('activities.user');
            $this->dispatch('toast', message: "{$this->task->id} moved to ".TaskStatus::from($to)->label());
            $this->dispatch('task-updated');
        } catch (RuntimeException|AuthorizationException $e) {
            $this->dispatch('toast', message: $e->getMessage());
        }
    }

    public function addComment(): void
    {
        if (! auth()->user()->can(Permission::ADD_COMMENTS)) {
            return;
        }

        $validated = $this->validate([
            'newComment' => 'required|string|max:2000',
        ]);

        $this->task->activities()->create([
            'user_id' => auth()->id(),
            'body' => $validated['newComment'],
            'created_at' => now(),
        ]);

        $this->newComment = '';
        $this->task->load('activities.user');
    }

    public function delete(): void
    {
        $this->authorize(Permission::DELETE_TASKS);

        $id = $this->task->id;
        $this->task->delete();
        $this->close();
        $this->dispatch('toast', message: "Task #{$id} deleted");
        $this->dispatch('task-deleted');
    }

    public function render(): View
    {
        return view('livewire.tasks.task-modal', [
            'statuses' => TaskStatus::cases(),
            'priorities' => TaskPriority::cases(),
        ]);
    }
}
