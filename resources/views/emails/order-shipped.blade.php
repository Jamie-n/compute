@component('mail::message')

Order Reference Number: {{$order->reference_number}}

Dear {{$order->deliveryAddress->name}},

Your order has been shipped from our warehouse and is now being handled by our delivery partner, you should receive an email shortly containing a tracking number.

To view a summary of your order, please click the button below.
@component('mail::button', ['url' => route('order.show', $order)])
    View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
