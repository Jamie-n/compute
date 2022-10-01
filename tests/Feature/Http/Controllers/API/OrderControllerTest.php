<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Mail\OrderDeliveredMail;
use App\Models\Order;
use Illuminate\Mail\Mailable;
use Mail;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    public function test_can_send_api_request_to_mark_order_as_delivered()
    {
        $order = Order::factory()->shipped()->create();

        $this->get(route('api.order.delivered', $order))
            ->assertSuccessful();
    }

    public function sends_error_response_if_attempting_to_deliver_product_already_delivered()
    {
        $order = Order::factory()->delivered()->create();

        $this->get(route('api.order.delivered', $order))
            ->assertForbidden();
    }

    public function sends_error_response_if_attempting_to_deliver_which_has_not_been_shipped()
    {
        $order = Order::factory()->processing()->create();

        $this->get(route('api.order.delivered', $order))
            ->assertForbidden();
    }

    public function test_sends_delivered_email_when_order_is_marked_as_delivered()
    {
        $order = Order::factory()->shipped()->create();

        $this->get(route('api.order.delivered', $order))
            ->assertSuccessful();

        Mail::assertSent(OrderDeliveredMail::class, 1);

        Mail::assertSent(OrderDeliveredMail::class, function (Mailable $mailable) use ($order) {
            return $mailable->hasTo($order->deliveryAddress->email_address);
        });
    }
}
