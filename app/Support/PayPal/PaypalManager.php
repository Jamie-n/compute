<?php

namespace App\Support\PayPal;

use App\Models\DeliveryType;
use App\Support\Cart\CartItem;
use App\Support\PayPal\Traits\HasCurrencies;
use Illuminate\Support\Collection;

class PaypalManager
{
    use HasCurrencies;

    protected string $shippingPreference;

    protected ?DeliveryType $deliveryType = null;

    protected string $currency;

    protected string $intent;

    protected Collection $products;

    protected float $orderTotal = 0.00;

    protected float $discountAmount = 0.00;

    public function authorizePayment(): static
    {
        $this->intent = 'AUTHORIZE';

        return $this;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function noShipping(): static
    {
        $this->shippingPreference = 'NO_SHIPPING';

        return $this;
    }

    public function setDeliveryType(DeliveryType $deliveryType): static
    {
        $this->deliveryType = $deliveryType;

        $this->orderTotal += $deliveryType->price;

        return $this;
    }

    /**
     * @param Collection<CartItem> $products
     * @return $this
     */
    public function addProducts(Collection $products): static
    {
        $this->products = $products;

        return $this;
    }

    public function addDiscount(float $discountAmount): static
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    public function setBasketTotal(float $basketTotal): static
    {
        $this->orderTotal += $basketTotal;

        return $this;
    }

    public function getOrderTotal()
    {
        return round($this->orderTotal, 2);
    }

    public function getDiscountAmount(): float
    {
        return round($this->discountAmount, 2);
    }

    public function getOrderValue()
    {
        return round($this->getOrderTotal() - $this->getDiscountAmount(), 2);
    }

    protected function generatePurchaseUnits(): Collection
    {
        $amountCollect = collect([
            'amount' => [
                'currency_code' => $this->currency,
                'value' => $this->getOrderValue(),
                'breakdown' => [
                    'item_total' => [
                        'value' => $this->getOrderTotal(),
                        'currency_code' => $this->currency,
                    ],
                    'discount' => [
                        'currency_code' => $this->currency,
                        'value' => $this->getDiscountAmount(),
                    ]
                ],
            ],
        ]);

        $amountCollect->put('items', $this->generateItemsArray());

        return $amountCollect;
    }

    protected function generateItemsArray(): array
    {
        $items = [];

        $this->products->each(function (CartItem $cartItem) use (&$items) {
            $items[] = $this->generateItemArray($cartItem->getProduct()->name, $cartItem->getQuantity(), $cartItem->getProduct()->display_price);
        });

        if (!$this->deliveryType)
            return $items;

        $items[] = $this->generateItemArray($this->deliveryType->name, 1, $this->deliveryType->price);

        return $items;
    }

    public function generateItemArray($name, $quantity, $totalValue)
    {
        return [
            'name' => $name,
            'quantity' => $quantity,
            'unit_amount' => [
                'currency_code' => $this->currency,
                'value' => $totalValue,
            ],
        ];
    }

    protected function generateApplicationContext(): array
    {
        return ['shipping_preference' => $this->shippingPreference];
    }

    public function generateOrderJson(): array
    {
        $orderApiJson = collect([]);

        $orderApiJson->put('intent', $this->intent);
        $orderApiJson->put('purchase_units', [$this->generatePurchaseUnits()]);
        $orderApiJson->put('application_context', $this->generateApplicationContext());

        return $orderApiJson->toArray();
    }
}
