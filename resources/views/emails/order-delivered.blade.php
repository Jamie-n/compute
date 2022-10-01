@component('mail::message')

Order Reference Number: {{$order->reference_number}}

Dear {{$order->deliveryAddress->name}},

We have been notified by our delivery partner that your order has been delivered successfully, thank you for shopping with {{config('app.name')}}.

If you experience any issues with your purchase please speak to our support team who will be happy to help.

To view a summary of your order, please click the button below.
@component('mail::button', ['url' => route('order.show', $order)])
    View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
