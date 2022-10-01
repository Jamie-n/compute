<?php

namespace Tests\Unit\Handlers;

use App\Handlers\CartHandler;
use App\Models\Product;
use App\Support\Cart\CartManager;
use Tests\TestCase;


class CartHandlerTest extends TestCase
{
    public function testAddsProductToCartIfNotAlreadyInCart()
    {
        $product = Product::factory()->stock(10)->create();

        app()->make(CartHandler::class)->addToCart($product);

        self::assertTrue(CartManager::hasItemInBasket($product));
    }

    public function testIncreasesQuantityOfProductInCartIfItemAlreadyPresent()
    {
        $product = Product::factory()->stock(10)->create();

        app()->make(CartHandler::class)->addToCart($product);
        app()->make(CartHandler::class)->addToCart($product);

        self::assertEquals(CartManager::getBasket()->get($product->id)->getQuantity(), 2);
    }

    public function testHandlesReducingQuantityOfItemWhichIsNotInCart()
    {
        $product = Product::factory()->stock(10)->create();
        $success = app()->make(CartHandler::class)->reduceCartQuantity($product);

        self::assertFalse($success);
    }

    public function testHandlesRemovingItemWhichIsNotInCart()
    {
        $product = Product::factory()->stock(10)->create();
        $success = app()->make(CartHandler::class)->removeFromCart($product);

        self::assertFalse($success);
    }

}
