<?php

namespace App\Http\Livewire\Admin\Product;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class IndexTable extends Component
{
    use WithPagination;

    public const REFRESH = 'refresh';

    protected $listeners = [self::REFRESH => 'refresh'];

    protected $queryString = [
        'search_term' => ['except' => '', 'as' => 'search']
    ];

    public $search_term = '';

    public function refresh()
    {
        $this->resetPage();
    }

    public function render()
    {
        $products = Product::orderBy('name')
            ->filter($this->search_term)
            ->paginate(config('pagination.admin_products_index_page_length'));

        return view('livewire.admin.product.index-table')
            ->extends('layouts.app')
            ->slot('content')
            ->with('products', $products);
    }
}
