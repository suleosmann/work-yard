<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Support\Permission;
use App\Support\Role as RoleName;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.pm')]
#[Title('Users')]
class Index extends Component
{
    use WithPagination;

    const DEFAULT_PASSWORD = 'password';

    #[Url]
    public string $search = '';

    /**
     * @var array<int, string>
     */
    #[Url]
    public array $roleFilter = [];

    #[Url]
    public string $sort = 'name';

    public bool $showForm = false;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $role = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'roleFilter', 'sort']);
        $this->resetPage();
    }

    public function activeFilterCount(): int
    {
        return (filled($this->search) ? 1 : 0) + count($this->roleFilter) + ($this->sort !== 'name' ? 1 : 0);
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function roles(): array
    {
        return RoleName::all();
    }

    /**
     * @return LengthAwarePaginator<int, User>
     */
    #[Computed]
    public function users(): LengthAwarePaginator
    {
        $query = User::query()->with('roles');

        if (filled($this->search)) {
            $needle = mb_strtolower($this->search);
            $query->where(function (Builder $q) use ($needle) {
                $q->whereRaw('lower(name) like ?', ["%{$needle}%"])
                    ->orWhereRaw('lower(email) like ?', ["%{$needle}%"]);
            });
        }

        if ($this->roleFilter !== []) {
            $query->whereHas('roles', fn (Builder $q) => $q->whereIn('name', $this->roleFilter));
        }

        match ($this->sort) {
            'newest' => $query->latest('id'),
            default => $query->orderBy('name'),
        };

        return $query->paginate(10);
    }

    /**
     * @return array{total: int, byRole: array<string, int>}
     */
    #[Computed]
    public function stats(): array
    {
        $byRole = Role::withCount('users')->get()
            ->mapWithKeys(fn (Role $role): array => [$role->name => $role->users_count])
            ->all();

        return [
            'total' => User::count(),
            'byRole' => $byRole,
        ];
    }

    public function startCreate(): void
    {
        $this->authorize(Permission::MANAGE_USERS);

        $this->reset(['name', 'email', 'phone', 'role']);
        $this->showForm = true;
    }

    public function cancel(): void
    {
        $this->reset(['showForm', 'name', 'email', 'phone', 'role']);
    }

    public function save(): void
    {
        $this->authorize(Permission::MANAGE_USERS);

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:'.implode(',', RoleName::all()),
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?: null,
            'password' => bcrypt(self::DEFAULT_PASSWORD),
            'email_verified_at' => now(),
        ]);

        $user->syncRoles([$validated['role']]);

        $this->dispatch('toast', message: "{$user->name} created — default password: ".self::DEFAULT_PASSWORD);
        $this->cancel();
    }

    public function render(): View
    {
        return view('livewire.users.index');
    }
}
