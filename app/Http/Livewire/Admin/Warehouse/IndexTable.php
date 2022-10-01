<?php

namespace App\Http\Livewire\Admin\Warehouse;

use App\Models\DeliveryType;
use App\Models\Order;
use App\Support\States\OrderStatesRepository;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class IndexTable extends Component
{
    use WithPagination;

    public $order_status;
    public $reference_number;
    public $delivery_type;

    public array $orderStatuses;
    public array $deliveryTypes;

    public const REFRESH = 'refresh';

    protected $listeners = [self::REFRESH => 'refresh'];

    protected $queryString = [
        'delivery_type' => ['ignore' => '', 'as' => 'delivery-type'],
        'order_status' => ['ignore' => '', 'as' => 'order-status'],
        'reference_number' => ['ignore' => '', 'as' => 'reference-number']
    ];

    public function mount(OrderStatesRepository $repository)
    {
        $this->orderStatuses = $repository->getAllOrderStates();
        $this->deliveryTypes = DeliveryType::getAdminDropdownOptions()->toArray();

        //Only set the order status to the first key in the status array if there isn't one already set
        // by the url parameter
        if (!isset($this->order_status))
            $this->order_status = array_values($this->orderStatuses)[0];
    }

    public function updatedOrderStatus()
    {
        $this->refresh();
    }

    public function updatedReferenceNumber()
    {
        $this->refresh();
    }

    public function updatedDeliveryType()
    {
        $this->refresh();
    }

    public function refresh()
    {
        $this->resetPage();
    }

    public function render(OrderStatesRepository $repository)
    {
        $orders = Order::latest()
            ->whereState('status', $repository->convertStateClassToFullNamespace($this->order_status))
            ->when($this->reference_number, function (Builder $builder) {
                return $builder->where('reference_number', 'LIKE', "%{$this->reference_number}%");
            })->when($this->delivery_type, function (Builder $builder) {
                return $builder->where('delivery_type_id', '=', $this->delivery_type);
            })->paginate(config('pagination.admin_warehouse_index_page_length'));

        return view('livewire.admin.warehouse.index-table')
            ->extends('layouts.app')
            ->slot('content')
            ->with('orders', $orders);
    }
}
