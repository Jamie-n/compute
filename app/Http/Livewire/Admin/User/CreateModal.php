<?php

namespace App\Http\Livewire\Admin\User;

use App\Models\User;
use App\Support\Enums\Permissions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class CreateModal extends Component
{
    public const SHOW = 'show-create';

    protected $listeners = [self::SHOW => 'show'];

    public User $user;

    public string $password = '';
    public string $password_confirmation = '';
    public array $selected_roles = [];

    public bool $hidden = true;

    protected $rules = [
        'user.name' => ['required', 'max:255'],
        'user.email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'selected_roles' => ['required'],
        'password' => ['required', 'max:255', 'confirmed'],
        'password_confirmation' => ['required', 'same:password'],
    ];

    protected $messages = [
        'selected_roles.required' => 'Please assign this user a role.'
    ];

    public function mount()
    {
        $this->user = User::make();
    }

    public function show()
    {
        $this->hidden = false;

        $this->reset('password', 'password_confirmation', 'selected_roles');
        $this->user = User::make();
    }

    public function hide()
    {
        $this->hidden = true;
    }

    /**
     * When selecting a role filter the array to remove all indexes set to false, so that we only contains the id's of roles which we want to assign to the user.
     * @return void
     */
    public function updatedSelectedRoles()
    {
        $this->selected_roles = array_filter($this->selected_roles);
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $this->user->password = Hash::make($this->password);

            $this->user->save();

            $this->user->syncRoles(array_keys($this->selected_roles));
        });

        $this->emitTo(IndexTable::class, IndexTable::REFRESH);

        $this->hidden = true;
    }

    public function render()
    {
        $roles = Role::whereHas('permissions', function (Builder $builder) {
            return $builder->where('name', Permissions::ADMIN->value);
        })->get();

        return view('livewire.admin.user.create-modal')->with('roles', $roles);
    }
}
