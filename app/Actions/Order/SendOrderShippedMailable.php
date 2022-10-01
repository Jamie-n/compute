<?php

namespace App\Actions\Order;

use App\Mail\OrderShippedMail;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendOrderShippedMailable
{
    public function handle(Order $order)
    {
        try {
            Mail::to($order->deliveryAddress->email_address)->send(new OrderShippedMail($order));
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}
