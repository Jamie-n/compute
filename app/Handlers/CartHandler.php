<?php

namespace App\Handlers;

use App\Models\Product;
use App\Support\Cart\CartManager;
use Exception;

class CartHandler
{

    /**
     * @throws Exception
     */
    public function addToCart(Product $product): bool
    {
        if (CartManager::hasItemInBasket($product)){
            CartManager::increaseQuantity($product);
            return true;
        }

        CartManager::addToCart($product);

        return true;
    }

    public function reduceCartQuantity(Product $product): bool
    {
        if (!CartManager::hasItemInBasket($product))
            return false;

        CartManager::decreaseQuantity($product);

        return true;
    }

    public function removeFromCart(Product $product): bool
    {
        if (!CartManager::hasItemInBasket($product))
            return false;

        CartManager::removeFromCart($product);

        return true;
    }
}
