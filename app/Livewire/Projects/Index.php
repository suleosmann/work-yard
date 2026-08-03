<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Models\Task;
use App\Support\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.pm')]
#[Title('Projects')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $sort = 'name';

    public bool $showForm = false;

    public ?Project $editing = null;

    public string $name = '';

    public string $description = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'sort']);
        $this->resetPage();
    }

    public function activeFilterCount(): int
    {
        return (filled($this->search) ? 1 : 0) + ($this->sort !== 'name' ? 1 : 0);
    }

    /**
     * @return LengthAwarePaginator<int, Project>
     */
    #[Computed]
    public function projects(): LengthAwarePaginator
    {
        $query = Project::query()
            ->withCount('tasks')
            ->withCount(['tasks as done_tasks_count' => fn (Builder $q) => $q->where('status', 'done')]);

        if (filled($this->search)) {
            $needle = mb_strtolower($this->search);
            $query->where(function (Builder $q) use ($needle) {
                $q->whereRaw('lower(name) like ?', ["%{$needle}%"])
                    ->orWhereRaw('lower(description) like ?', ["%{$needle}%"]);
            });
        }

        match ($this->sort) {
            'tasks' => $query->orderByDesc('tasks_count')->orderBy('name'),
            'newest' => $query->latest('id'),
            default => $query->orderBy('name'),
        };

        return $query->paginate(8);
    }

    /**
     * @return array{projects: int, tasks: int, done: int, done_pct: int}
     */
    #[Computed]
    public function stats(): array
    {
        $totalTasks = Task::count();
        $doneTasks = Task::where('status', 'done')->count();

        return [
            'projects' => Project::count(),
            'tasks' => $totalTasks,
            'done' => $doneTasks,
            'done_pct' => $totalTasks ? (int) round(($doneTasks / $totalTasks) * 100) : 0,
        ];
    }

    public function startCreate(): void
    {
        $this->authorize(Permission::CREATE_PROJECTS);

        $this->reset(['editing', 'name', 'description']);
        $this->showForm = true;
    }

    public function startEdit(Project $project): void
    {
        $this->authorize(Permission::EDIT_PROJECTS);

        $this->editing = $project;
        $this->name = $project->name;
        $this->description = (string) $project->description;
        $this->showForm = true;
    }

    public function cancel(): void
    {
        $this->reset(['showForm', 'editing', 'name', 'description']);
    }

    public function save(): void
    {
        $this->authorize($this->editing ? Permission::EDIT_PROJECTS : Permission::CREATE_PROJECTS);

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
        ]);

        if ($this->editing) {
            $this->editing->update($validated);
        } else {
            Project::create($validated);
        }

        $this->cancel();
    }

    public function render(): View
    {
        return view('livewire.projects.index');
    }
}
