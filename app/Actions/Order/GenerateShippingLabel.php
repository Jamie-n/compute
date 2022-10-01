<?php

namespace App\Actions\Order;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateShippingLabel
{

    public function handle(Order $order)
    {
        $order->load('deliveryAddress');

        return Pdf::loadView('order.admin.shipping-label', ['order' => $order])->setPaper([0, 0, 150, 150], 'landscape')->output();
    }
}
