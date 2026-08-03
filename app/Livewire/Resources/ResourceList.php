<?php

namespace App\Livewire\Resources;

use App\Models\Project;
use App\Models\Resource;
use App\Support\Permission;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.pm')]
class ResourceList extends Component
{
    public Project $project;

    public bool $showForm = false;

    public ?Resource $editing = null;

    public string $title = '';

    public string $subtitle = '';

    public string $type = 'DOC';

    public function mount(Project $project): void
    {
        $this->project = $project;
    }

    /**
     * @return Collection<int, \App\Models\Resource>
     */
    #[Computed]
    public function resources(): Collection
    {
        return $this->project->resources()->orderBy('id')->get();
    }

    public function startCreate(): void
    {
        $this->authorize(Permission::MANAGE_RESOURCES);

        $this->reset(['editing', 'title', 'subtitle']);
        $this->type = 'DOC';
        $this->showForm = true;
    }

    public function startEdit(Resource $resource): void
    {
        $this->authorize(Permission::MANAGE_RESOURCES);

        $this->editing = $resource;
        $this->title = $resource->title;
        $this->subtitle = (string) $resource->subtitle;
        $this->type = $resource->type;
        $this->showForm = true;
    }

    public function cancel(): void
    {
        $this->reset(['showForm', 'editing', 'title', 'subtitle', 'type']);
    }

    public function save(): void
    {
        $this->authorize(Permission::MANAGE_RESOURCES);

        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'type' => 'required|string|max:12',
        ]);

        if ($this->editing) {
            $this->editing->update($validated);
        } else {
            $this->project->resources()->create($validated);
        }

        $this->cancel();
    }

    public function delete(Resource $resource): void
    {
        $this->authorize(Permission::MANAGE_RESOURCES);

        $resource->delete();
    }

    public function render(): View
    {
        return view('livewire.resources.resource-list')
            ->title($this->project->name.' — Resources');
    }
}
