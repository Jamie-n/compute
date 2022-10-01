<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;

class OrderShippedMail extends Mailable
{
    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;

        $this->subject = "Order Shipped - $order->reference_number";
    }

    public function build()
    {
        return $this->markdown('emails.order-shipped');
    }
}
