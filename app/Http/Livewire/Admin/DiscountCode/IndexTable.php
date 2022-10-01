<?php

namespace App\Http\Livewire\Admin\DiscountCode;

use App\Models\DiscountCode;
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
        $discountCodes = DiscountCode::paginate(config('pagination.admin_discount_codes_page_length'));

        return view('livewire.admin.discount-code.index-table')
            ->extends('layouts.app')
            ->slot('content')
            ->with('codes', $discountCodes);
    }
}
