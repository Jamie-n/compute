<?php

namespace App\Http\Livewire\Admin\User;

use App\Models\User;
use App\Support\Enums\Permissions;
use App\Support\Enums\UserRoles;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Models\Role;

class IndexTable extends Component
{
    use WithPagination;

    public const REFRESH = 'refresh';

    protected $listeners = [self::REFRESH => 'refresh'];

    protected $queryString = [
        'search_term' => ['except' => '', 'as' => 'search'],
    ];

    public string $search_term = '';

    public function refresh()
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = User::whereHasRoles()
            ->filter($this->search_term)
            ->with('roles')
            ->orderBy('name')
            ->paginate(config('pagination.admin_user_index_page_length'));

        return view('livewire.admin.user.index-table')
            ->extends('layouts.app')
            ->slot('content')
            ->with('users', $users);
    }
}
