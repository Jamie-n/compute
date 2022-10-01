<?php

namespace App\Support\States\Transitions;

use App\Actions\Order\SendInvoiceMailable;
use App\Support\States\Packing;

class ProcessingToPacking extends OrderTransition
{
    public function handle()
    {
        session()->put('reference_number', $this->order->reference_number);

        app()->make(SendInvoiceMailable::class)->handle($this->order);

        self::updateState(new Packing($this->order));

        return $this->order;
    }
}
