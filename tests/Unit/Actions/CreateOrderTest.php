<?php

namespace Tests\Unit\Actions;

use App\Actions\Order\CreateOrder;
use App\Exceptions\InvalidOrderException;
use App\Models\Address;
use App\Models\DeliveryType;
use App\Models\DiscountCode;
use App\Models\Product;
use App\Models\User;
use App\Support\Cart\CartManager;
use App\Support\States\Processing;
use Illuminate\Contracts\Container\BindingResolutionException;
use Str;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{

    /**
     * @throws InvalidOrderException
     * @throws BindingResolutionException
     */
    public function test_can_build_orders_for_in_stock_items()
    {
        $user = User::factory()->create();
        $product = Product::factory()->stock(10)->create();
        $shippingAddress = collect(Address::factory()->make()->toArray());
        $deliveryType = DeliveryType::factory()->create();

        CartManager::addToCart($product);

        $order = app()->make(CreateOrder::class)
            ->setUser($user)
            ->setBasket(CartManager::getBasket())
            ->setShippingInformation($shippingAddress)
            ->setDeliveryType($deliveryType, 'Test Delivery Info')
            ->setPaypalTransactionId(Str::random(15))
            ->buildOrder();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'delivery_type_id' => $deliveryType->id,
        ]);

        self::assertTrue($order->products()->first()->id == $product->id);
    }

    public function test_throws_validation_exception_when_ordering_out_of_stock_items()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $product = Product::factory()->outOfStock()->create();
        $shippingAddress = collect(Address::factory()->make()->toArray());
        $deliveryType = DeliveryType::factory()->create();

        CartManager::addToCart($product);

        $this->assertThrows(function () use ($user, $shippingAddress, $deliveryType) {
            app()->make(CreateOrder::class)
                ->setUser($user)
                ->setBasket(CartManager::getBasket())
                ->setShippingInformation($shippingAddress)
                ->setDeliveryType($deliveryType, 'Test Delivery Info')
                ->setPaypalTransactionId(Str::random(15))
                ->buildOrder();
        }, InvalidOrderException::class);
    }

    public function test_stock_of_other_items_in_basket_does_not_decrease_if_validation_error_is_thrown_for_out_of_stock_item()
    {
        $user = User::factory()->create();
        $product = Product::factory()->stock(10)->create();
        $product1 = Product::factory()->outOfStock()->create();
        $shippingAddress = collect(Address::factory()->make()->toArray());
        $deliveryType = DeliveryType::factory()->create();

        CartManager::addToCart($product);
        CartManager::addToCart($product1);

        try {
            app()->make(CreateOrder::class)
                ->setUser($user)
                ->setBasket(CartManager::getBasket())
                ->setShippingInformation($shippingAddress)
                ->setDeliveryType($deliveryType, 'Test Delivery Info')
                ->setPaypalTransactionId(Str::random(15))
                ->buildOrder();
        } catch (InvalidOrderException $e) {
        }

        self::assertEquals(10, $product->refresh()->stock_quantity, 'Expected stock quantity to remain as 10');
    }

    /**
     * @throws InvalidOrderException
     * @throws BindingResolutionException
     */
    public function test_order_state_set_to_processing_when_order_created()
    {
        $user = User::factory()->create();
        $product = Product::factory()->stock(10)->create();
        $shippingAddress = collect(Address::factory()->make()->toArray());
        $deliveryType = DeliveryType::factory()->create();

        CartManager::addToCart($product);

        $order = app()->make(CreateOrder::class)
            ->setUser($user)
            ->setBasket(CartManager::getBasket())
            ->setShippingInformation($shippingAddress)
            ->setDeliveryType($deliveryType, 'Test Delivery Info')
            ->setPaypalTransactionId(Str::random(15))
            ->buildOrder();

        self::assertEquals(Processing::getName(), $order->status->getName(), "Expected order to have the status of 'processing'");
    }

    /**
     * @throws InvalidOrderException
     * @throws BindingResolutionException
     */
    public function test_additional_delivery_info_is_saved_when_creating_orders()
    {
        $user = User::factory()->create();
        $product = Product::factory()->stock(10)->create();
        $shippingAddress = collect(Address::factory()->make()->toArray());
        $deliveryType = DeliveryType::factory()->create();

        CartManager::addToCart($product);

        app()->make(CreateOrder::class)
            ->setUser($user)
            ->setBasket(CartManager::getBasket())
            ->setShippingInformation($shippingAddress)
            ->setDeliveryType($deliveryType, 'Test Delivery Info')
            ->setPaypalTransactionId(Str::random(15))
            ->buildOrder();

        $this->assertDatabaseHas('orders', ['additional_delivery_info' => 'Test Delivery Info']);
    }

    /**
     * @throws InvalidOrderException
     * @throws BindingResolutionException
     */
    public function test_order_total_is_correctly_set()
    {
        $user = User::factory()->create();
        $delivery = DeliveryType::factory()->create(['price' => 10.99]);
        $product = Product::factory()->stock(10)->price(5.00)->create();
        $shippingAddress = collect(Address::factory()->make()->toArray());

        CartManager::addToCart($product);

        $order = app()->make(CreateOrder::class)
            ->setUser($user)
            ->setBasket(CartManager::getBasket())
            ->setShippingInformation($shippingAddress)
            ->setDeliveryType($delivery, 'Test Delivery Info')
            ->setPaypalTransactionId(Str::random(15))
            ->buildOrder();

        self::assertEquals(15.99, $order->order_total);
    }


    /**
     * @throws InvalidOrderException
     * @throws BindingResolutionException
     */
    public function test_paypal_reference_number_is_set()
    {
        $paypalReference = Str::random(15);
        $user = User::factory()->create();
        $delivery = DeliveryType::factory()->create(['price' => 10.99]);
        $product = Product::factory()->price(5.00)->stock(10)->create();
        $shippingAddress = collect(Address::factory()->make()->toArray());

        CartManager::addToCart($product);

        $order = app()->make(CreateOrder::class)
            ->setUser($user)
            ->setBasket(CartManager::getBasket())
            ->setShippingInformation($shippingAddress)
            ->setDeliveryType($delivery, 'Test Delivery Info')
            ->setPaypalTransactionId($paypalReference)
            ->buildOrder();

        self::assertEquals($paypalReference, $order->paypal_transaction_id);
    }

    public function test_discount_code_id_is_set_correctly()
    {
        $discountCode = DiscountCode::factory()->create();

        CartManager::addToCart(Product::factory()->price(5.00)->stock(10)->create());

        $order = app()->make(CreateOrder::class)
            ->setUser(User::factory()->create())
            ->setBasket(CartManager::getBasket())
            ->setShippingInformation(collect(Address::factory()->make()->toArray()))
            ->setDeliveryType(DeliveryType::factory()->create(['price' => 10.99]), 'Test Delivery Info')
            ->setPaypalTransactionId(Str::random(15))
            ->setDiscountCode($discountCode)
            ->buildOrder();

        self::assertEquals($discountCode->id, $order->discount_code_id);
    }


    public function test_discount_code_id_is_null_when_not_set()
    {
        CartManager::addToCart(Product::factory()->price(5.00)->stock(10)->create());

        $order = app()->make(CreateOrder::class)
            ->setUser(User::factory()->create())
            ->setBasket(CartManager::getBasket())
            ->setShippingInformation(collect(Address::factory()->make()->toArray()))
            ->setDeliveryType(DeliveryType::factory()->create(['price' => 10.99]), 'Test Delivery Info')
            ->setPaypalTransactionId(Str::random(15))
            ->buildOrder();

        self::assertNull($order->discount_code_id);
    }
}
