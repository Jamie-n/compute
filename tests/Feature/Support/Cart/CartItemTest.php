<?php

namespace Tests\Feature\Support\Cart;

use App\Models\Product;
use App\Support\Cart\CartItem;
use Exception;
use Tests\TestCase;


class CartItemTest extends TestCase
{

    public function test_cant_decrease_item_quantity_below_zero()
    {
        $product = Product::factory()->stock(10)->create();

        $cartItem = new CartItem($product);

        self::assertEquals(1, $cartItem->getQuantity());

        $this->assertThrows(function () use ($cartItem) {
            $cartItem->decreaseQuantity();
        });
    }

    /**
     * @throws Exception - If the quantity we request to remove decreases the value below zero
     */
    public function test_decrease_item_quantity()
    {
        $product = Product::factory()->stock(100)->create();

        $cartItem = new CartItem($product);

        $cartItem->setQuantity(10);

        self::assertEquals(10, $cartItem->getQuantity());

        $cartItem->decreaseQuantity();

        self::assertEquals(9, $cartItem->getQuantity());
    }

    public function test_increase_item_quantity()
    {
        $product = Product::factory()->stock(10)->create();

        $cartItem = new CartItem($product);

        self::assertEquals(1, $cartItem->getQuantity());

        $cartItem->increaseQuantity();

        self::assertEquals(2, $cartItem->getQuantity());
    }
}
