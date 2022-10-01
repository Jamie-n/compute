<?php

namespace Tests\Feature\Http\Livewire\Checkout;

use App\Exceptions\InvalidOrderException;
use App\Http\Livewire\Checkout\CheckoutCard;
use App\Mail\OrderInvoiceMail;
use App\Models\Address;
use App\Models\DeliveryType;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Support\Cart\CartManager;
use App\Support\Enums\Alert;
use App\Support\PayPal\PaypalPaymentHandler;
use App\Support\States\Packing;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire;
use Mockery\MockInterface;
use Tests\TestCase;

class CheckoutCardTest extends TestCase
{
    protected function generateOrderDataArray(Address $address)
    {
        return collect([
            'name' => $address->name,
            'email_address' => $address->email_address,
            'phone_number' => $address->phone_number,
            'address_line_1' => $address->address_line_1,
            'address_line_2' => $address->address_line_2,
            'city' => $address->city,
            'county' => $address->county,
            'postcode' => 'AAA AAA',
        ]);
    }

    public function generateExamplePaypalResponse()
    {
        return [
            "create_time" => "2022-12-11T18:27:38Z",
            "update_time" => "2022-12-11T18:27:49Z",
            "id" => "ABCD",
            "intent" => "CAPTURE",
            "status" => "COMPLETED",
            "payer" => [
                "email_address" => "example@example.com",
                "payer_id" => "ABC",
                "address" => [
                    "country_code" => "GB",
                ],
                "name" => [
                    "given_name" => "John",
                    "surname" => "Doe",
                ],
            ],
            "purchase_units" => [
                0 => [
                    "description" => "Example Product",
                    "reference_id" => "default",
                    "payments" => [
                        "authorizations" => [
                            0 => [
                                "status" => "COMPLETED",
                                "id" => 1234,
                            ],
                        ],
                    ],
                ],
            ]
        ];
    }

    public function test_can_store_order()
    {
        $this->mock(PaypalPaymentHandler::class, function (MockInterface $mock) {
            $mock->shouldReceive('captureAuthorizedPayment')->once();
        });

        $exampleResponse = $this->generateExamplePaypalResponse();

        $product = Product::factory()->stock(2)->create();
        $user = User::factory()->create();
        $this->be($user);

        CartManager::addToCart($product);

        $address = Address::factory()->make();
        $addressCollection = $this->generateOrderDataArray($address);
        $delivery = DeliveryType::factory()->create();

        Livewire::test(CheckoutCard::class)
            ->set('shipping_info', $addressCollection)
            ->set('delivery_type_id', $delivery->id)
            ->call('createOrder', $exampleResponse)
            ->assertRedirect(route('order.confirmation'))
            ->assertSessionHas('reference_number');

        $this->assertDatabaseHas('orders', ['reference_number' => session()->get('reference_number')]);
        $this->assertDatabaseHas('addresses', $addressCollection->toArray());

        $order = Order::whereReferenceNumber(session()->get('reference_number'))->first();

        self::assertEquals(Packing::class, $order->status);
    }

    public function test_stores_correct_item_quantity_as_pivot()
    {
        $this->mock(PaypalPaymentHandler::class, function (MockInterface $mock) {
            $mock->shouldReceive('captureAuthorizedPayment')->once();
        });

        $exampleResponse = $this->generateExamplePaypalResponse();

        $product = Product::factory()->stock(100)->create();
        $user = User::factory()->create();
        $this->be($user);

        CartManager::addToCart($product);
        CartManager::increaseQuantity($product);
        CartManager::increaseQuantity($product);

        $address = Address::factory()->make();
        $addressCollection = $this->generateOrderDataArray($address);
        $delivery = DeliveryType::factory()->create();

        Livewire::test(CheckoutCard::class)
            ->set('shipping_info', $addressCollection)
            ->set('delivery_type_id', $delivery->id)
            ->call('createOrder', $exampleResponse)
            ->assertRedirect(route('order.confirmation'))
            ->assertSessionHas('reference_number');

        self::assertEquals(3, Order::whereReferenceNumber(Session::get('reference_number'))->first()->products()->first()->pivot->quantity);

    }

    public function test_flashes_error_message_when_sending_checkout_request_with_an_empty_cart()
    {
        $this->mock(PaypalPaymentHandler::class, function (MockInterface $mock) {
            $mock->shouldReceive('voidAuthorisedPayment')->once();
        });

        $exampleResponse = $this->generateExamplePaypalResponse();

        $address = Address::factory()->make();
        $addressCollection = $this->generateOrderDataArray($address);
        $delivery = DeliveryType::factory()->create();

        $user = User::factory()->create();
        $this->be($user);

        Livewire::test(CheckoutCard::class)
            ->set('shipping_info', $addressCollection)
            ->set('delivery_type_id', $delivery->id)
            ->call('createOrder', $exampleResponse)
            ->assertRedirect(route('basket.index'))
            ->assertSessionHas(Alert::DANGER->value);
    }

    /**
     * @dataProvider validationDataShouldFail
     */
    public function test_shipping_address_validation_fails($key, $value, $rule)
    {
        $user = User::factory()->create();
        $this->be($user);

        Livewire::test(CheckoutCard::class)
            ->set($key, $value)
            ->call('validateShipping')
            ->assertHasErrors([$key => $rule]);
    }

    public function validationDataShouldFail()
    {
        return [
            'shipping name required' => ['shipping_info.name', '', 'required'],
            'shipping name too long' => ['shipping_info.name', Str::random(256), 'max:255'],

            'email address required' => ['shipping_info.email_address', '', 'required'],
            'email address not an email' => ['shipping_info.email_address', 'abc', 'email'],
            'email address too long' => ['shipping_info.email_address', Str::random(256) . '@ex.com', 'max:255'],

            'phone number required' => ['shipping_info.phone_number', '', 'required'],
            'phone number too long' => ['shipping_info.phone_number', Str::random(256), 'max:255'],

            'address line 1 required' => ['shipping_info.address_line_1', '', 'required'],
            'address line 1 too long' => ['shipping_info.address_line_1', Str::random(256), 'max:255'],

            'address line 2 too long' => ['shipping_info.address_line_2', Str::random(256), 'max:255'],

            'city required' => ['shipping_info.city', '', 'required'],
            'city too long' => ['shipping_info.city', Str::random(256), 'max:255'],

            'county required' => ['shipping_info.county', '', 'required'],
            'county too long' => ['shipping_info.county', Str::random(256), 'max:255'],

            'postcode required' => ['shipping_info.postcode', '', 'required'],
            'postcode too long' => ['shipping_info.postcode', Str::random(15), 'max:6'],
        ];
    }

    /**
     * @dataProvider validationDataShouldPass
     */
    public function test_shipping_info_validation_passes($key, $value, $rule)
    {
        $user = User::factory()->create();
        $this->be($user);

        Livewire::test(CheckoutCard::class)
            ->set($key, $value)
            ->call('validateShipping')
            ->assertHasNoErrors([$key => $rule]);
    }

    public function validationDataShouldPass()
    {
        return [
            'shipping name present' => ['shipping_info.name', 'Jamie Neighbours', 'required'],
            'shipping max length' => ['shipping_info.name', Str::random(255), 'max:255'],
            'shipping less than max' => ['shipping_info.name', Str::random(20), 'max:255'],

            'email address required' => ['shipping_info.email_address', 'abc@ex.com', 'required'],
            'email address not an email' => ['shipping_info.email_address', 'abc@ex.com', 'email'],
            'email address max length' => ['shipping_info.email_address', Str::random(248) . '@ex.com', 'max:255'],
            'email address less than max' => ['shipping_info.email_address', Str::random(10) . '@ex.com', 'max:255'],

            'phone number required' => ['shipping_info.phone_number', '123', 'required'],
            'phone number max length' => ['shipping_info.phone_number', Str::random(255), 'max:255'],
            'phone number less than max' => ['shipping_info.phone_number', Str::random(10), 'max:255'],

            'address line 1 required' => ['shipping_info.address_line_1', 'address line 1', 'required'],
            'address line 1 max length' => ['shipping_info.address_line_1', Str::random(255), 'max:255'],
            'address line 1 less than max' => ['shipping_info.address_line_1', Str::random(10), 'max:255'],

            'address line 2 max length' => ['shipping_info.address_line_2', Str::random(255), 'max:255'],
            'address line 2 less than max' => ['shipping_info.address_line_2', Str::random(10), 'max:255'],

            'city required' => ['shipping_info.city', 'city', 'required'],
            'city max length' => ['shipping_info.city', Str::random(255), 'max:255'],
            'city too less than max' => ['shipping_info.city', Str::random(10), 'max:255'],

            'county required' => ['shipping_info.county', 'county', 'required'],
            'county too max length' => ['shipping_info.county', Str::random(255), 'max:255'],
            'county less than max' => ['shipping_info.county', Str::random(10), 'max:255'],

            'postcode required' => ['shipping_info.postcode', 'AB1 2CD', 'required'],
            'postcode max length' => ['shipping_info.postcode', Str::random(6), 'max:6'],
            'postcode less than max' => ['shipping_info.postcode', Str::random(3), 'max:6'],
        ];
    }

    public function test_delivery_type_set_to_cheapest_option_by_default()
    {
        DeliveryType::factory(3)->sequence(['price' => 1], ['price' => 2], ['price' => 3])->create();

        $cheapestDelivery = DeliveryType::orderBy('price')->first();
        Livewire::test(CheckoutCard::class)->assertSet('delivery_type_id', $cheapestDelivery->id);
    }

    public function test_edit_shipping_set_to_false_when_shipping_fields_pass_validation()
    {
        $address = Address::factory()->make();

        Livewire::test(CheckoutCard::class)
            ->set('shipping_info', $this->generateOrderDataArray($address))
            ->call('validateShipping')
            ->assertHasNoErrors()
            ->assertSet('editShipping', false);
    }

    public function test_edit_shipping_call_sets_edit_shipping_to_true()
    {
        Livewire::test(CheckoutCard::class)
            ->set('editShipping', false)
            ->call('editShipping')
            ->assertSet('editShipping', true);

    }

    /**
     * @dataProvider deliveryValidationThatShouldFail
     */
    public function test_delivery_info_validation_fails($key, $value, $rule)
    {
        $this->withoutExceptionHandling();
        Livewire::test(CheckoutCard::class)
            ->set($key, $value)
            ->call('validateDeliveryInfo')
            ->assertHasErrors([$key => $rule]);
    }

    public function deliveryValidationThatShouldFail(): array
    {
        return [
            'additional delivery info too long' => ['additional_delivery_info', Str::random(256), 'max:255'],

            'delivery type not set' => ['delivery_type_id', '', 'required'],
            'delivery type does not exist' => ['delivery_type_id', 100, 'exists:delivery_types,id'],
        ];

    }

    /**
     * @dataProvider deliveryValidationThatShouldPass
     */
    public function test_delivery_info_validation_passes($key, $value, $rule)
    {
        $this->withoutExceptionHandling();
        Livewire::test(CheckoutCard::class)
            ->set($key, $value)
            ->call('validateDeliveryInfo')
            ->assertHasNoErrors([$key => $rule]);
    }

    public function deliveryValidationThatShouldPass(): array
    {
        return [
            'additional delivery max length' => ['additional_delivery_info', Str::random(255), 'max:255'],
            'additional delivery less than max' => ['additional_delivery_info', Str::random(15), 'max:255'],

            'delivery type set' => ['delivery_type_id', 1, 'required'],
            'delivery type exists' => ['delivery_type_id', 1, 'exists:delivery_types,id'],
        ];
    }

    public function test_sends_order_mailable_when_order_is_placed()
    {
        $this->mock(PaypalPaymentHandler::class, function (MockInterface $mock) {
            $mock->shouldReceive('captureAuthorizedPayment')->once();
        });

        $exampleResponse = $this->generateExamplePaypalResponse();

        $product = Product::factory()->stock(10)->create();
        $user = User::factory()->create();
        $this->be($user);

        CartManager::addToCart($product);

        $address = Address::factory()->make();
        $addressCollection = $this->generateOrderDataArray($address);
        $delivery = DeliveryType::factory()->create();

        Livewire::test(CheckoutCard::class)
            ->set('shipping_info', $addressCollection)
            ->set('delivery_type_id', $delivery->id)
            ->call('createOrder', $exampleResponse)
            ->assertRedirect(route('order.confirmation'))
            ->assertSessionHas('reference_number');

        $order = Order::whereReferenceNumber(session()->get('reference_number'))->first();

        Mail::assertSent(OrderInvoiceMail::class, 1);

        Mail::assertSent(OrderInvoiceMail::class, function ($mail) use ($order) {
            $reference = session()->get('reference_number');

            return
                $mail->hasTo($order->deliveryAddress->email_address) &&
                $mail->hasSubject("Order Placed - {$reference}");
        });
    }

    public function test_redirects_with_error_message_back_to_basket_page_if_ordering_out_of_stock_product_occurs()
    {
        $this->mock(PaypalPaymentHandler::class, function (MockInterface $mock) {
            $mock->shouldReceive('voidAuthorisedPayment')->once();
        });

        $exampleResponse = $this->generateExamplePaypalResponse();

        $address = Address::factory()->make();
        $addressCollection = $this->generateOrderDataArray($address);
        $delivery = DeliveryType::factory()->create();

        $product = Product::factory()->outOfStock()->create();

        CartManager::addToCart($product);

        $user = User::factory()->create();
        $this->be($user);

        Livewire::test(CheckoutCard::class)
            ->set('shipping_info', $addressCollection)
            ->set('delivery_type_id', $delivery->id)
            ->call('createOrder', $exampleResponse)
            ->assertRedirect(route('basket.index'))
            ->assertSessionHas(Alert::DANGER->value, InvalidOrderException::genericMessage()->getMessage());
    }

    public function test_applies_discount_when_discount_code_is_used()
    {
        $product = Product::factory()->stock(2)->create();

        $this->be(User::factory()->create());

        CartManager::addToCart($product);

        $code = DiscountCode::factory()->create(['discount_percentage' => 50, 'code_active_start' => now()->subDay(), 'code_active_end' => now()->addDay()]);

        $totalCost = $product->price + 2.99;
        $discount = round($product->price / 2 + 2.99, 2);

        Livewire::test(CheckoutCard::class)
            ->set('discount_code', $code->code)
            ->call('applyDiscountCode')
            ->assertSet('discountCode.id', $code->id)
            ->assertSet('discountAmount', $product->price / 2)
            ->assertSeeHtml('<p class="text line-through text-red-500">Order Total: £' . $totalCost . '</p>')
            ->assertSeeHtml('<p class="text-xl">Order Total: £' . $discount . '</p>');
    }

    /**
     * @dataProvider discountCodes
     */
    public function test_fails_validation_if_discount_code_is_invalid($start, $end)
    {
        $code = DiscountCode::factory()->create(['code_active_start' => $start, 'code_active_end' => $end]);

        Livewire::test(CheckoutCard::class)
            ->set('discount_code', $code->code)
            ->call('applyDiscountCode')
            ->assertHasErrors('discount_code');
    }

    public function discountCodes(): array
    {
        return [
            'invalid code' => [now()->subDays(2), now()->subDay()],
            'inactive code' => [now()->addDay(), now()->addDays(2)],
        ];
    }
}
