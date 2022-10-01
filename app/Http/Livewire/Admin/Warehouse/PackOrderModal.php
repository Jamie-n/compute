<?php

namespace App\Http\Livewire\Admin\Warehouse;

use App\Actions\Order\GenerateShippingLabel;
use App\Models\Order;
use App\Models\Product;
use App\Rules\PackedCorrectQuantityRule;
use App\Support\States\Shipped;
use Illuminate\Contracts\Container\BindingResolutionException;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PackOrderModal extends Component
{
    public const SHOW = 'show-order-modal';
    public const HIDE = 'hide-order-modal';

    protected $listeners = [self::SHOW => 'show', self::HIDE => 'hide'];

    public $hidden = true;

    public $shippingLabelGenerated = false;

    public Order $order;

    public array $packing_quantities = [];

    protected function getRules()
    {
        return $this->order->products->flatMap(function (Product $product) {
            return ['packing_quantities.' . $product->getRouteKey() => ['required', new PackedCorrectQuantityRule($this->order)]];
        })->toArray();
    }

    protected function getMessages()
    {
        return $this->order->products->flatMap(function (Product $product) {
            return ['packing_quantities.' . $product->getRouteKey() . '.required' => 'Please set the packing quantity.'];
        })->toArray();
    }

    public function mount()
    {
        $this->order = Order::make();
    }

    public function show(Order $order)
    {
        $this->order = $order;

        $this->hidden = false;
        $this->shippingLabelGenerated = false;
    }

    public function hide()
    {
        $this->hidden = true;
    }

    public function orderPacked()
    {
        $this->validate();

        $this->hide();

        $this->reset('packing_quantities');

        $this->order->status->transitionTo(Shipped::class);

        $this->emit(IndexTable::REFRESH);
    }


    /**
     * @throws BindingResolutionException
     */
    public function generateShippingLabel(): StreamedResponse
    {
        $pdf = app()->make(GenerateShippingLabel::class)->handle($this->order);

        $this->shippingLabelGenerated = true;

        /**
         * Workaround to enable file streaming from laravel livewire, code used based on an answer submitted by
         * Code Snippet code is based on the solution as proposed by olivsinz, Jan 27, 2021.
         *
         * Github Issue Here:
         * https://github.com/barryvdh/laravel-dompdf/issues/740
         *
         */
        return response()->streamDownload(function () use ($pdf) {
            print $pdf;
        }, "{$this->order->reference_number}_shipping_label.pdf");
    }

    public function canConfirmOrderPacked()
    {
        return $this->shippingLabelGenerated;
    }

    public function render()
    {
        return view('livewire.admin.warehouse.pack-order-modal');
    }
}
