<?php

namespace App\Http\Livewire\Admin\Delivery;

use App\Models\DeliveryType;
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
        $deliveryTypes = DeliveryType::paginate(config('pagination.admin_delivery_types_page_length'));

        return view('livewire.admin.delivery.index-table')
            ->extends('layouts.app')
            ->slot('content')
            ->with('deliveryTypes', $deliveryTypes);
    }
}
