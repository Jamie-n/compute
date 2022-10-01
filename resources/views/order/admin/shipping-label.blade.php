<style>
    html {
        margin: 30px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'
    }
</style>

<div>
    {{$order->deliveryAddress->name}}, <br>
    {{$order->deliveryAddress->address_line_1}}, <br>
    @isset($order->deliveryAddress->address_line_2)
        {{$order->deliveryAddress->address_line_2}}, <br>
    @endisset
    {{$order->deliveryAddress->city}},<br>
    {{$order->deliveryAddress->county}},<br>
    {{$order->deliveryAddress->postcode}}<br>
</div>

