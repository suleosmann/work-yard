<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Support\Permission;
use App\Support\Role as RoleName;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.pm')]
#[Title('Users')]
class Index extends Component
{
    const DEFAULT_PASSWORD = 'password';

    public bool $showForm = false;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $role = '';

    /**
     * @return Collection<int, User>
     */
    #[Computed]
    public function users(): Collection
    {
        return User::with('roles')->orderBy('name')->get();
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function roles(): array
    {
        return RoleName::all();
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
