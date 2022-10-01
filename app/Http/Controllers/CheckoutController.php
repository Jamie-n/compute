<?php

namespace App\Http\Controllers;

use App\Actions\Order\CreateOrder;
use App\Support\Cart\CartManager;

class CheckoutController extends Controller
{
    public function index()
    {
        if (!CartManager::hasItemsInBasket())
            return redirect()->route('basket.index');

        CreateOrder::validateOrder(CartManager::getBasket());

        return view('checkout.index')
            ->with('basket', CartManager::getBasket())
            ->with('total', CartManager::basketTotal());
    }
}
