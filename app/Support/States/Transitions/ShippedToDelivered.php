<?php

namespace App\Support\States\Transitions;

use App\Actions\Order\SendOrderDeliveredMail;
use App\Support\States\Delivered;

class ShippedToDelivered extends OrderTransition
{
    public function handle()
    {
        app()->make(SendOrderDeliveredMail::class)->handle($this->order);

        self::updateState(new Delivered($this->order));

        return $this->order;
    }
}
