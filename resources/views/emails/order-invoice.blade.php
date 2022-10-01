@component('mail::message')

Order Reference Number: {{$order->reference_number}}

Dear {{$order->deliveryAddress->name}},

Your order was placed successfully, our warehouse operatives are now packing your order. You will receive a notification once your order has been shipped.

To view a summary of your order, please click the button below.
@component('mail::button', ['url' => route('order.show', $order)])
    View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
