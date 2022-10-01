<?php

namespace App\Rules;

use App\Models\Order;
use Illuminate\Contracts\Validation\Rule;

class PackedCorrectQuantityRule implements Rule
{
    protected Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function passes($attribute, $value): bool
    {
        $attribute = explode('.', $attribute)[1];

        $product = $this->order->products->where('slug', '=', $attribute)->first();

        if ($product->pivot->quantity != $value)
            return false;

        return true;
    }

    public function message(): string
    {
        return 'Please pack the correct number of items.';
    }
}
