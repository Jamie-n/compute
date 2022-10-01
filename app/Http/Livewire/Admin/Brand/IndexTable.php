<?php

namespace App\Http\Livewire\Admin\Brand;

use App\Models\Brand;
use Livewire\Component;
use Livewire\WithPagination;

class IndexTable extends Component
{
    use WithPagination;

    public const REFRESH = 'refresh';

    protected $listeners = [self::REFRESH => 'refresh'];

    public function refresh()
    {
        $this->resetPage();
    }

    public function render()
    {
        $brands = Brand::paginate(config('pagination.admin_brands_index_page_length'));

        return view('livewire.admin.brand.index-table')
            ->extends('layouts.app')
            ->slot('content')
            ->with('brands', $brands);
    }
}
