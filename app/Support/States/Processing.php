<?php

namespace App\Support\States;

use App\Models\Order;

class Processing extends OrderStatus
{

    public static function getOrder(): int
    {
        return 0;
    }

    public static function getName(): string
    {
        return 'Processing';
    }
}
