<?php

namespace App\Support\Cart;

use App\Models\Product;
use Exception;

final class CartItem
{
    protected Product $product;
    protected int $quantity;

    public function __construct(Product $product)
    {
        $this->product = $product;
        $this->quantity = 1;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function canAddMoreToBasket(): bool
    {
        return $this->product->refresh()->stock_quantity - $this->quantity > 0;
    }

    /**
     * @throws Exception
     */
    public function increaseQuantity(): void
    {
        $itemQuantity = $this->quantity += 1;

        $this->setQuantity($itemQuantity);
    }

    /**
     * @throws Exception
     */
    public function decreaseQuantity(): void
    {
        $itemQuantity = $this->quantity - 1;

        $this->setQuantity($itemQuantity);
    }

    /**
     * @throws Exception
     */
    public function setQuantity(int $quantity): void
    {
        //Prevent negative quantities
        if ($quantity <= 0)
            throw new Exception('Item quantity cannot be less than zero');

        $this->quantity = $quantity;
    }


    public function getTotalPrice(): string
    {
        return number_format($this->product->display_price * $this->quantity, 2, '.', '');
    }

}
