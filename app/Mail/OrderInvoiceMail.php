<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;

class OrderInvoiceMail extends Mailable
{
    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;

        $this->subject = "Order Placed - $order->reference_number";
    }

    public function build(): OrderInvoiceMail
    {
        return $this->markdown('emails.order-invoice');
    }
}
