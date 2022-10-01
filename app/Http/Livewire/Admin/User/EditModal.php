<?php

namespace App\Http\Livewire\Admin\User;

use App\Models\User;
use App\Support\Enums\Permissions;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class EditModal extends Component
{
    public const SHOW = 'show-edit';

    protected $listeners = [self::SHOW => 'show'];

    public bool $hidden = true;

    public User $user;

    public array $selectedRoles = [];

    public function mount()
    {
        $this->user = User::make();
    }

    public function show(User $user)
    {
        $this->reset('selectedRoles');
        $this->user = $user;
        $this->selectedRoles = $user->roles()->pluck('name', 'id')->toArray();

        $this->hidden = false;
    }

    public function save()
    {
        $filteredRoles = array_filter($this->selectedRoles);

        $this->user->syncRoles(array_keys($filteredRoles));

        $this->hidden = true;

        $this->emitTo(IndexTable::class, IndexTable::REFRESH);
    }

    public function hide()
    {
        $this->hidden = true;
    }

    public function render()
    {
        $roles = Role::whereHas('permissions', function (Builder $builder) {
            return $builder->where('name', Permissions::ADMIN->value);
        })->get();

        return view('livewire.admin.user.edit-modal')->with('roles', $roles);
    }
}
