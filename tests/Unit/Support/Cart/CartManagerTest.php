<?php

namespace Tests\Unit\Support\Cart;

use App\Models\Product;
use App\Support\Cart\CartManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class CartManagerTest extends TestCase
{

    public function testInitializesBasket()
    {
        Session::forget(CartManager::BASKET_KEY);

        self::assertNull(Session::get(CartManager::BASKET_KEY));

        CartManager::initializeBasket();

        self::assertInstanceOf(Collection::class, CartManager::getBasket());
    }

    public function testCanAddItemToBasket()
    {
        $product = Product::factory()->stock(10)->create();

        CartManager::addToCart($product);

        $productInCart = CartManager::getBasket()->first()->getProduct();

        self::assertEquals($product->id, $productInCart->id);
    }

    public function testCanAddMultipleProductsToCart()
    {
        $product = Product::factory()->stock(10)->create();
        $product1 = Product::factory()->stock(10)->create();

        CartManager::addToCart($product);
        CartManager::addToCart($product1);

        $productsInCartCount = CartManager::getBasket()->count();

        self::assertEquals(2, $productsInCartCount);
    }

    public function testCanAddSameItemToCartMultipleTimes()
    {
        $product = Product::factory()->stock(10)->create();

        CartManager::addToCart($product);
        CartManager::increaseQuantity($product);
        CartManager::increaseQuantity($product);

        $quantityOfProductsInCart = CartManager::getBasket()->first()->getQuantity();

        self::assertEquals(3, $quantityOfProductsInCart);
    }

    public function testAddingSameProductOnlyCreatesOneCartItem()
    {
        $product = Product::factory()->stock(10)->create();

        CartManager::addToCart($product);
        CartManager::increaseQuantity($product);
        CartManager::increaseQuantity($product);

        $productsInCartCount = CartManager::getBasket()->count();

        self::assertEquals(1, $productsInCartCount);
    }

    public function testCanCalculateCorrectNumberOfCartItemsWhenMultipleProductsAdded()
    {
        $product = Product::factory()->stock(10)->create();
        $product1 = Product::factory()->stock(10)->create();

        CartManager::addToCart($product);
        CartManager::increaseQuantity($product);
        CartManager::addToCart($product1);

        $quantityOfProductsInCart = CartManager::itemCountInBasket();

        self::assertEquals(3, $quantityOfProductsInCart);
    }

    public function testCanReduceItemQuantityInCart()
    {
        $product = Product::factory()->stock(10)->create();

        CartManager::addToCart($product);
        CartManager::increaseQuantity($product);

        self::assertEquals(2, CartManager::getBasket()->get($product->id)->getQuantity());

        CartManager::decreaseQuantity($product);

        self::assertEquals(1, CartManager::getBasket()->get($product->id)->getQuantity());
    }

    public function testCanRemoveItemFromCart()
    {
        $product = Product::factory()->stock(10)->create();

        CartManager::addToCart($product);

        self::assertEquals(1, CartManager::itemCountInBasket());

        CartManager::removeFromCart($product);

        self::assertEquals(0, CartManager::itemCountInBasket());
    }

    public function testRemoveFromCartOnlyRemovesRequestedProduct()
    {
        $product = Product::factory()->stock(10)->create();
        $product1 = Product::factory()->stock(10)->create();

        CartManager::addToCart($product);
        CartManager::addToCart($product1);

        self::assertEquals(2, CartManager::itemCountInBasket());

        CartManager::removeFromCart($product);

        self::assertEquals(1, CartManager::itemCountInBasket());
        self::assertTrue(CartManager::getBasket()->has($product1->id));
    }

    public function testCartManagerRemovesItemFromBasketIfQuantityIsReducedToZero()
    {
        $product = Product::factory()->stock(10)->create();

        CartManager::addToCart($product);

        CartManager::decreaseQuantity($product);

        self::assertEquals(0, CartManager::itemCountInBasket());
    }

    public function testCorrectlyCalculatesBasketTotalAsInt()
    {
        $product = Product::factory()->stock(10)->price(10.99)->create();
        $product1 = Product::factory()->stock(10)->price(1.01)->create();

        CartManager::addToCart($product);
        CartManager::addToCart($product1);

        self::assertEquals(12, CartManager::basketTotal());
    }

    public function testCorrectlyCalculatesBasketTotalAsFloat()
    {
        $product = Product::factory()->stock(10)->price(10.99)->create();
        $product1 = Product::factory()->stock(10)->price(1.56)->create();

        CartManager::addToCart($product);
        CartManager::addToCart($product1);

        self::assertEquals(12.55, CartManager::basketTotal());
    }
}
