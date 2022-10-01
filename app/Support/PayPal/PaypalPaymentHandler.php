<?php

namespace App\Support\PayPal;

use App\Models\Order;
use Psr\Http\Message\StreamInterface;
use Srmklive\PayPal\Services\PayPal;
use Throwable;

class PaypalPaymentHandler
{
    protected PayPal $payPal;

    protected string $message;

    protected Order $order;

    protected string $transactionId;

    public function __construct()
    {
        $this->payPal = new PayPal();
        $this->payPal->setApiCredentials(config('paypal'));
        $token = $this->payPal->getAccessToken();
        $this->payPal->setAccessToken($token);

        $this->message = config('paypal.authorised_payment_captured_invoice_message');
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @throws Throwable
     */
    public function createOrder($orderData): StreamInterface|array|string
    {
        return $this->payPal->createOrder($orderData);
    }

    /**
     * @throws Throwable
     */
    public function captureAuthorizedPayment(Order $order, string $ref): StreamInterface|array|string
    {
        return $this->payPal->captureAuthorizedPayment(
            $ref,
            $order->reference_number,
            round($order->order_total, 2),
            $this->message
        );
    }

    /**
     * @throws Throwable
     */
    public function voidAuthorisedPayment(string $ref): void
    {
        $this->payPal->voidAuthorizedPayment($ref);
    }
}
