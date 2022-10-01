<?php

namespace App\Support\States;

use App\Models\Order;

class Delivered extends OrderStatus
{
    public static function getOrder(): int
    {
        return 3;
    }

    public static function getName(): string
    {
        return 'Delivered';

    }
}
