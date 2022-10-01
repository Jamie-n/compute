<?php

namespace Tests\Unit\Support\Order;

use App\Exceptions\StockQuantityException;
use App\Models\Order;
use App\Models\Product;
use App\Support\Order\OrderManager;
use Tests\TestCase;


class OrderManagerTest extends TestCase
{

    public function test_remove_product_from_order()
    {
        $order = Order::factory()->hasAttached(Product::factory()->stock(10)->count(3), ['quantity' => 3, 'unit_price' => 1.99])->create();

        $productToRemove = $order->products()->first();

        OrderManager::boot($order)->removeProductFromOrder($productToRemove);

        self::assertTrue($order->refresh()->products()->whereId($productToRemove->id)->get()->isEmpty());
    }

    public function test_stock_is_increased_when_removing_items_from_order()
    {
        $order = Order::factory()->hasAttached(Product::factory()->stock(10)->count(3)->create(['stock_quantity' => '3']), ['quantity' => 3, 'unit_price' => 1.99])->create();

        $productToRemove = $order->products()->first();
        OrderManager::boot($order)->removeProductFromOrder($productToRemove);

        self::assertEquals(6, $productToRemove->stock_quantity);
    }

    /**
     * @throws StockQuantityException
     */
    public function test_can_link_product_to_order()
    {
        $order = Order::factory()->create();
        $product = Product::factory()->stock(10)->create();

        OrderManager::boot($order)->linkProductToOrder($product, 5);

        self::assertTrue($order->products()->first()->is($product));
    }

    /**
     * @throws StockQuantityException
     */
    public function test_reduces_stock_quantity_when_adding_product_to_order()
    {
        $order = Order::factory()->create();
        $product = Product::factory()->stock(10)->create();

        OrderManager::boot($order)->linkProductToOrder($product, 5);

        self::assertEquals(5, $product->stock_quantity);
    }

    public function test_cannot_add_more_items_than_are_in_stock()
    {
        $order = Order::factory()->create();
        $product = Product::factory()->stock(2)->create();

        $this->assertThrows(function () use ($order, $product) {
            OrderManager::boot($order)->linkProductToOrder($product, 5);
        }, StockQuantityException::class);
    }

    public function test_unit_price_set_correctly_when_adding_item_to_order()
    {
        $order = Order::factory()->create();
        $product = Product::factory()->stock(10)->price(10.99)->create();

        OrderManager::boot($order)->linkProductToOrder($product, 5);

        self::assertEquals('10.99', $order->products()->first()->pivot->unit_price);
    }
}
