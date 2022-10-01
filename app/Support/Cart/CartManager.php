<?php

namespace App\Support\Cart;

use App\Models\Product;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartManager
{

    /**
     * Default key value for the basket in the session
     */
    public const BASKET_KEY = 'basket';

    /**
     * Add a product to the basket in the session
     * If the product is already in the basket the quantity key will be incremented
     * @return void
     */
    public static function boot(): void
    {
        if (!Session::has(self::BASKET_KEY))
            self::initializeBasket();
    }

    /**
     * Initialize the basket key in the session
     * @return void
     */
    public static function initializeBasket(): void
    {
        Session::put(self::BASKET_KEY, collect());
    }

    /**
     * Get the basket from the session or initialize the basket if it hasn't been created.
     * @return Collection|null
     */
    public static function getBasket(): ?Collection
    {
        return Session::get(self::BASKET_KEY);
    }

    public static function addToCart(Product $product): void
    {
        self::getBasket()->put($product->id, new CartItem($product));
    }

    public static function hasItemInBasket(Product $product): bool
    {
        return self::getBasket()->has($product->id);
    }

    /**
     * @throws Exception
     */
    public static function increaseQuantity(Product $product): void
    {
        /**
         * @var $basketItem CartItem
         */
        $basketItem = self::getBasket()->get($product->id);

        $basketItem->increaseQuantity();
    }

    /**
     * The decrease quantity method of the CartItem will throw an exception if the item reaches 0 which is caught and handled by removing the item from the cart, preventing items showing in the cart with 0 quantity.
     * @param Product $product
     * @return void
     */
    public static function decreaseQuantity(Product $product): void
    {
        /**
         *
         * @var $basketItem CartItem
         */
        $basketItem = self::getBasket()->get($product->id);

        try {
            $basketItem->decreaseQuantity();
        } catch (Exception $e) {
            self::removeFromCart($product);
        }
    }

    public static function removeFromCart(Product $product): void
    {
        self::getBasket()->pull($product->id);
    }

    /**
     * Get the number of items currently in the basket
     * @return int
     */
    public static function itemCountInBasket(): int
    {
        return self::getBasket()->sum(function (CartItem $item) {
            return $item->getQuantity();
        });
    }

    public static function basketTotal(): string
    {
        $value = self::getBasket()->sum(function (CartItem $item) {
            return $item->getTotalPrice();
        });

        return number_format($value, 2, '.', '');
    }

    /**
     * Check to see if there are any items in the basket
     * @return bool
     */
    public static function hasItemsInBasket(): bool
    {
        return self::getBasket()->isNotEmpty();
    }
}
