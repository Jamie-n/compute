<?php

namespace App\Actions\Order;

use App\Mail\OrderInvoiceMail;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendInvoiceMailable
{
    public function handle(Order $order): bool
    {

        try {
            Mail::to($order->deliveryAddress->email_address)->send(new OrderInvoiceMail($order));
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
