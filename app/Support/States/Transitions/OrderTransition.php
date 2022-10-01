<?php

namespace App\Support\States\Transitions;

use App\Models\Order;
use App\Support\States\OrderStatus;
use Spatie\ModelStates\Transition;

abstract class OrderTransition extends Transition
{
    protected Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function updateState(OrderStatus $status)
    {
        $this->order->update(['status' => $status]);
    }

    public abstract function handle();
}
