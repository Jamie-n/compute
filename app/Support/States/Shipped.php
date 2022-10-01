<?php

namespace App\Support\States;

use App\Models\Order;
use Illuminate\Contracts\Container\BindingResolutionException;

class Shipped extends OrderStatus
{
    public static function getOrder(): int
    {
        return 2;
    }

    public static function getName(): string
    {
        return 'Shipped';
    }
}
