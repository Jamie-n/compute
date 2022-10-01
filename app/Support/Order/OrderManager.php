<?php

namespace App\Support\Order;

use App\Exceptions\StockQuantityException;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderManager
{
    protected Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public static function boot(Order $order): OrderManager
    {
        return new self($order);
    }

    /**
     * @throws StockQuantityException
     */
    public function linkProductToOrder(Product $product, int $quantity): void
    {
        $this->order->products()->attach($product->id, ['quantity' => $quantity, 'unit_price' => $product->display_price]);

        $product->reduceStock($quantity);
    }

    public function removeProductFromOrder(Product $product): void
    {
        $orderQuantity = $this->order->products()->find($product)->pivot->quantity;

        DB::transaction(function () use ($product, $orderQuantity) {
            $this->order->products()->detach($product->id);
            $product->increaseStock($orderQuantity);
        });
    }

}
