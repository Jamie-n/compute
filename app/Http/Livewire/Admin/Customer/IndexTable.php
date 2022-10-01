<?php

namespace App\Http\Livewire\Admin\Customer;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

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
        $users = User::whereDoesntHaveRoles()
            ->filter($this->search_term)
            ->latest()
            ->paginate(config('pagination.admin_user_index_page_length'));

        return view('livewire.admin.customer.index-table')
            ->extends('layouts.app')
            ->slot('content')
            ->with('users', $users);
    }
}
