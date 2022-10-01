<?php

namespace App\Actions\Order;

use App\Exceptions\InvalidOrderException;
use App\Exceptions\StockQuantityException;
use App\Http\Requests\CheckoutRequest;
use App\Models\Address;
use App\Models\DeliveryType;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Support\Cart\CartItem;
use App\Support\Cart\CartManager;
use App\Support\Order\OrderManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateOrder
{
    protected ?Order $order;

    protected Authenticatable $user;
    protected Collection $shippingInformation;
    protected Collection $basket;

    protected DeliveryType $deliveryType;
    protected ?string $additionalDeliveryInfo;

    protected ?DiscountCode $discountCode = null;

    protected ?string $paypalTransactionId;

    public function setBasket(Collection $basket): static
    {
        $this->basket = $basket;
        return $this;
    }

    public function setShippingInformation(Collection $shippingInformation): static
    {
        $this->shippingInformation = $shippingInformation;
        return $this;
    }

    public function setUser(Authenticatable $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function setPaypalTransactionId(string $payPalTransactionId): static
    {
        $this->paypalTransactionId = $payPalTransactionId;

        return $this;
    }

    public function setDeliveryType(DeliveryType $deliveryType, ?string $additionalDeliveryInfo): static
    {
        $this->deliveryType = $deliveryType;
        $this->additionalDeliveryInfo = $additionalDeliveryInfo;

        return $this;
    }

    public function setDiscountCode(?DiscountCode $discountCode): static
    {
        $this->discountCode = $discountCode;

        return $this;
    }

    /**
     * Build the top level order model, associate all the products stored in the cart with this order and save.
     * Will only clear the basket if the order is saved successfully.
     *
     * @throws InvalidOrderException - If the basket is empty
     */
    public function buildOrder(): Order
    {
        if ($this->basket->isEmpty())
            throw InvalidOrderException::genericMessage();

        DB::transaction(function () {

            $this->order = Order::make();
            $this->order->reference_number = Str::uuid();
            $this->order->paypal_transaction_id = $this->paypalTransactionId;

            $this->associateUser();
            $this->setDeliveryDetails();
            $this->setShippingAddress();
            $this->setOrderTotal();
            $this->applyDiscountCode();

            $this->order->save();

            $this->addItemsToOrder();
        });

        CartManager::initializeBasket();

        return $this->order;
    }

    protected function setOrderTotal(): void
    {
        $this->order->order_total = $this->deliveryType->price + CartManager::basketTotal();
    }

    protected function setShippingAddress(): void
    {
        $address = Address::create($this->shippingInformation->toArray());
        $this->order->deliveryAddress()->associate($address);
    }

    protected function associateUser(): void
    {
        $this->order->user()->associate($this->user);
    }

    protected function setDeliveryDetails(): void
    {
        $this->order->deliveryType()->associate($this->deliveryType);
        $this->order->additional_delivery_info = $this->additionalDeliveryInfo;
    }

    protected function applyDiscountCode()
    {
        $this->order->discount_code_id = optional($this->discountCode)->id;
    }

    /**
     * Associate all the products in the basket with the order model
     * Will throw an invalid order exception if one of the products is out of stock
     * @return void
     * @throws InvalidOrderException
     */
    protected function addItemsToOrder(): void
    {
        $orderManager = OrderManager::boot($this->order);

        $this->basket->each(function (CartItem $item) use ($orderManager) {
            try {
                $orderManager->linkProductToOrder($item->getProduct(), $item->getQuantity());
            } catch (StockQuantityException $e) {
                throw InvalidOrderException::genericMessage();
            }
        });
    }

    public static function validateOrder(Collection $basket): void
    {
        $messageBag = Validator::make([], []);

        $basket->each(function (CartItem $item) use ($messageBag) {
            if (!$item->getProduct()->isInStock())
                $messageBag->errors()->add($item->getProduct()->id, $item->getProduct()->name);
        });

        if ($messageBag->errors()->isNotEmpty()) {
            (throw new ValidationException($messageBag, null, 'cart'));
        }
    }
}

