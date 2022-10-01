<?php

namespace App\Support\States;

use App\Actions\Order\SendInvoiceMailable;
use App\Models\Order;
use Illuminate\Contracts\Container\BindingResolutionException;

class Packing extends OrderStatus
{

    public static function getOrder(): int
    {
        return 1;
    }

    public static function getName(): string
    {
        return 'Packing';
    }
}
