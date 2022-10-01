<?php

namespace App\Support\States\Transitions;


use App\Actions\Order\SendOrderShippedMailable;
use App\Models\Order;
use App\Support\States\Shipped;
use Illuminate\Contracts\Container\BindingResolutionException;


class PackingToShipped extends OrderTransition
{

    /**
     * @throws BindingResolutionException
     */
    public function handle(): Order
    {
        app()->make(SendOrderShippedMailable::class)->handle($this->order);

        self::updateState(new Shipped($this->order));

        return $this->order;
    }
}
