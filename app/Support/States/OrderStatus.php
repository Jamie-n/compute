<?php

namespace App\Support\States;

use App\Models\Order;
use App\Support\States\Transitions\PackingToShipped;
use App\Support\States\Transitions\ProcessingToPacking;
use App\Support\States\Transitions\ShippedToDelivered;
use Spatie\ModelStates\Exceptions\InvalidConfig;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class OrderStatus extends State
{
    public abstract static function getName(): string;

    public abstract static function getOrder(): int;

    /**
     * @throws InvalidConfig
     */
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Processing::class)
            ->allowTransition(Processing::class, Packing::class, ProcessingToPacking::class)
            ->allowTransition(Packing::class, Shipped::class, PackingToShipped::class)
            ->allowTransition(Shipped::class, Delivered::class, ShippedToDelivered::class);
    }
}
