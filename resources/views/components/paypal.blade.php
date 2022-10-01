<div class="text-center">
    <div id="paypal-button-container"></div>
</div>

<script src="https://www.paypal.com/sdk/js?client-id={{config('paypal.sandbox.client_id')}}&intent=authorize&currency=GBP&disable-funding=card"></script>

<script>
    paypal.Buttons({
        style: {
            shape: 'pill',
            color: 'white',
            label: 'checkout',
            tagline: 'false'
        },
        // Set up the transaction
        createOrder: function (data, actions) {
            return @this.call('generateOrder')
        },
        onApprove: function (data, actions) {
            return actions.order.authorize().then(function (details) {
                @this.call('createOrder', details)
            });
        }
    }).render('#paypal-button-container');
</script>
