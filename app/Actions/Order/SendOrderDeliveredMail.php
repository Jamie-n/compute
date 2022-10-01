<?php

namespace App\Actions\Order;

use App\Mail\OrderDeliveredMail;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendOrderDeliveredMail
{
    public function handle(Order $order)
    {
        try {
            Mail::to($order->deliveryAddress->email_address)->send(new OrderDeliveredMail($order));
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}
