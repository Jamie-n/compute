<?php

namespace App\View\Components;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StockPill extends Component
{
    public bool $isProductInStock;


    public string $colour;
    public string $body;
    public string $icon;
    public string $iconSrText;

    public function __construct(Product $product)
    {
        $this->isProductInStock = $product->isInStock();

        $this->colour = self::selectOptionBasedOnStockStatus('bg-green-800', 'bg-red-800');
        $this->body = self::selectOptionBasedOnStockStatus('In Stock', 'Out of Stock');
        $this->icon = self::selectOptionBasedOnStockStatus('far fa-check-circle', 'far fa-times-circle');
        $this->iconSrText = self::selectOptionBasedOnStockStatus('In stock icon', 'Out of stock icon');
    }

    public function selectOptionBasedOnStockStatus($inStockOption, $outOfStockOption)
    {
        return $this->isProductInStock ? $inStockOption : $outOfStockOption;
    }

    public function render(): View
    {
        return view('components.stock-pill');
    }
}
