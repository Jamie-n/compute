<?php

namespace App\Http\Controllers;

use App\Handlers\CartHandler;
use App\Models\Product;
use App\Support\Cart\CartManager;
use Exception;

class BasketController extends Controller
{

    public function index()
    {
        $basket = CartManager::getBasket();

        $totalCost = CartManager::basketTotal();

        return view('basket.index')
            ->with('basket', $basket)
            ->with('total', $totalCost);
    }

    /**
     * @throws Exception
     */
    public function addToBasket(Product $product, CartHandler $cartHandler)
    {
        $cartHandler->addToCart($product);

        return redirect()->back();
    }

    public function reduceQuantityInBasket(Product $product, CartHandler $cartHandler)
    {
        $cartHandler->reduceCartQuantity($product);

        return redirect()->back();
    }

    public function removeFromBasket(Product $product, CartHandler $cartHandler)
    {
        $cartHandler->removeFromCart($product);

        return redirect()->back();
    }
}
