<?php

namespace App\Http\Livewire\Checkout;

use App\Actions\Order\CreateOrder;
use App\Exceptions\InvalidOrderException;
use App\Models\DeliveryType;
use App\Models\DiscountCode;
use App\Rules\DiscountCodeValidRule;
use App\Support\Cart\CartManager;
use App\Support\Enums\Alert;
use App\Support\PayPal\PaypalManager;
use App\Support\PayPal\PaypalPaymentHandler;
use App\Support\States\Packing;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\Component;
use Spatie\ModelStates\Exceptions\CouldNotPerformTransition;
use Throwable;

class CheckoutCard extends Component
{
    public Collection $shipping_info;

    public $delivery_type_id;

    public $additional_delivery_info;

    public bool $editShipping = true;

    public string $discount_code = '';

    public ?DiscountCode $discountCode = null;

    public float $discountAmount = 0;

    protected $rules = [
        'shipping_info.name' => ['required', 'string', 'max:255'],
        'shipping_info.email_address' => ['required', 'email', 'max:255'],
        'shipping_info.phone_number' => ['required', 'string', 'max:255'],
        'shipping_info.address_line_1' => ['required', 'string', 'max:255'],
        'shipping_info.address_line_2' => ['nullable', 'string', 'max:255'],
        'shipping_info.city' => ['required', 'string', 'max:255'],
        'shipping_info.county' => ['required', 'string', 'max:255'],
        'shipping_info.postcode' => ['required', 'string', 'max:7'],
    ];

    protected $validationAttributes = [
        'shipping_info.name' => 'name',
        'shipping_info.email_address' => 'email address',
        'shipping_info.phone_number' => 'phone number',
        'shipping_info.address_line_1' => 'address line 1',
        'shipping_info.address_line_2' => 'address line 2',
        'shipping_info.city' => 'city',
        'shipping_info.county' => 'county',
        'shipping_info.postcode' => 'postcode',
    ];

    public function mount()
    {
        $this->shipping_info = collect();
        $this->delivery_type_id = DeliveryType::orderBy('price')->first()->id;
    }

    public function getDeliveryType()
    {
        return DeliveryType::find($this->delivery_type_id);
    }

    public function validateShipping()
    {
        $this->validate();

        $this->editShipping = false;
    }

    public function editShipping()
    {
        $this->editShipping = true;
    }

    public function validateDeliveryInfo()
    {
        $this->validate([
            'discount_code' => new DiscountCodeValidRule($this->discount_code),
            'additional_delivery_info' => ['nullable', 'max:255'],
            'delivery_type_id' => ['required', 'exists:delivery_types,id']
        ], [], [
            'additional_delivery_info' => 'additional delivery information',
            'delivery_type_id' => 'delivery type'
        ]);
    }

    public function getDeliveryCost()
    {
        return optional($this->getDeliveryType())->price ?? 0;
    }

    public function getBasketTotal(): float
    {
        if ($this->discountCode)
            $this->discountAmount = CartManager::basketTotal() * ($this->discountCode->discount_percentage / 100);

        return round((CartManager::basketTotal() - $this->discountAmount) + $this->getDeliveryCost(), 2);
    }

    public function getOriginalTotal(): float
    {
        return round(CartManager::basketTotal() + $this->getDeliveryCost(), 2);
    }

    public function applyDiscountCode()
    {
        $this->reset('discountAmount');

        $this->validate([
            'discount_code' => new DiscountCodeValidRule($this->discount_code)
        ]);

        $this->discountCode = DiscountCode::whereCode($this->discount_code)->first();
    }

    /**
     * @throws Throwable
     */
    public function generateOrder(PaypalManager $paypalManager, PaypalPaymentHandler $paymentHandler)
    {
        $this->validateDeliveryInfo();

        $orderJson = $paypalManager
            ->addProducts(CartManager::getBasket())
            ->setDeliveryType($this->getDeliveryType())
            ->setBasketTotal(CartManager::basketTotal())
            ->addDiscount($this->discountAmount)
            ->setCurrency(PaypalManager::$GBP)
            ->noShipping()
            ->authorizePayment()
            ->generateOrderJson();

        $response = $paymentHandler->createOrder($orderJson);

        return $response['id'];
    }

    /**
     * Attempt to build the order which has been placed, if an invalid order exception is thrown we will void the authorised paypal payment and redirect the user to the basket screen with a payment error message
     * If the order is placed successfully we then will capture the authorized payment and redirect to the confirmation screen.
     * @throws CouldNotPerformTransition
     * @throws Throwable
     */
    public function createOrder($transaction, CreateOrder $orderCreator)
    {
        $transactionReference = Arr::get($transaction, 'purchase_units.0.payments.authorizations.0.id');

        try {
            $order = $orderCreator
                ->setDeliveryType($this->getDeliveryType(), $this->additional_delivery_info)
                ->setPaypalTransactionId($transactionReference)
                ->setShippingInformation($this->shipping_info)
                ->setDiscountCode($this->discountCode)
                ->setBasket(CartManager::getBasket())
                ->setUser(auth()->user())
                ->buildOrder();
        } catch (InvalidOrderException $exception) {
            $this->orderFailed($exception, $transactionReference);
            return;
        }

        $this->capturePayment($transactionReference, $order);

        $order->status->transitionTo(Packing::class);

        $this->redirect(route('order.confirmation'));
    }

    /**
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function orderFailed($exception, $ref)
    {
        app()->make(PaypalPaymentHandler::class)->voidAuthorisedPayment($ref);

        session()->flash(Alert::DANGER->value, $exception->getMessage());
        $this->redirect(route('basket.index'));
    }

    /**
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function capturePayment($ref, $order)
    {
        app()->make(PaypalPaymentHandler::class)->captureAuthorizedPayment($order, $ref);
    }

    public function render()
    {
        return view('livewire.checkout.checkout-card');
    }
}
