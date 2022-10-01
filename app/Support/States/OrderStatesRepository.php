<?php

namespace App\Support\States;

use App\Models\Order;
use Illuminate\Support\Str;

class OrderStatesRepository
{

    public function getAllOrderStates(): array
    {
        return Order::getStates()->get('status')
            ->sortBy(fn($classname) => ($classname::getOrder()))
            ->mapWithKeys(fn($classname) => ([Str::lower($classname::getName()) => $classname::getName()]))
            ->toArray();
    }

    public function convertStateClassToFullNamespace($className): string
    {
        return __NAMESPACE__ . '\\' . Str::title($className);
    }
}
