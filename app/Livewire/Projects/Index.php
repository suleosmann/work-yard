<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Support\Permission;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.pm')]
#[Title('Projects')]
class Index extends Component
{
    public bool $showForm = false;

    public ?Project $editing = null;

    public string $name = '';

    public string $description = '';

    /**
     * @return Collection<int, Project>
     */
    #[Computed]
    public function projects(): Collection
    {
        return Project::withCount('tasks')->orderBy('name')->get();
    }

    public function doneCount(Project $project): int
    {
        return $project->tasks()->where('status', 'done')->count();
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
