<?php

namespace App\Http\Livewire\Admin\User;

use App\Models\User;
use App\Support\Enums\Permissions;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class DeleteModal extends Component
{
    public const SHOW = 'show-delete';

    protected $listeners = [self::SHOW => 'show'];

    public bool $hidden = true;

    public User $user;

    public function mount()
    {
        $this->user = User::make();
    }

    public function show(User $user)
    {
        $this->user = $user;

        $this->hidden = false;
    }

    public function hide()
    {
        $this->hidden = true;
    }

    public function delete()
    {
        $this->hidden = true;

        $this->user->delete();

        $this->emitTo(IndexTable::class, IndexTable::REFRESH);
    }

    public function render()
    {
        return view('livewire.admin.user.delete-modal');
    }
}
