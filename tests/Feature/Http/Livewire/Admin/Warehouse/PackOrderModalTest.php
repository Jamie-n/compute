<?php

namespace Tests\Feature\Http\Livewire\Admin\Warehouse;

use App\Exceptions\StockQuantityException;
use App\Http\Livewire\Admin\Warehouse\IndexTable;
use App\Http\Livewire\Admin\Warehouse\PackOrderModal;
use App\Mail\OrderShippedMail;
use App\Models\Order;
use App\Models\Product;
use App\Support\Order\OrderManager;
use App\Support\States\Shipped;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class PackOrderModalTest extends TestCase
{
    public function test_emitting_event_shows_modal()
    {
        $this->withoutExceptionHandling();
        $order = Order::factory()->packing()->create();

        Livewire::test(PackOrderModal::class)
            ->emit(PackOrderModal::SHOW, $order)
            ->assertSet('shippingLabelGenerated', false)
            ->assertSet('hidden', false);
    }

    public function test_can_hide_modal()
    {
        $order = Order::factory()->packing()->create();

        Livewire::test(PackOrderModal::class)
            ->emit(PackOrderModal::SHOW, $order)
            ->assertSet('shippingLabelGenerated', false)
            ->assertSet('hidden', false)
            ->call('hide')
            ->assertSet('hidden', true);
    }

    public function test_modal_binds_to_correct_order()
    {
        $order = Order::factory()->packing()->create();

        Livewire::test(PackOrderModal::class)
            ->emit(PackOrderModal::SHOW, $order)
            ->assertSet('order', $order);
    }

    public function test_modal_title_set_correctly()
    {
        $order = Order::factory()->packing()->create();

        Livewire::test(PackOrderModal::class)
            ->emit(PackOrderModal::SHOW, $order)
            ->assertSee("Pack Order: $order->reference_number");
    }

    public function test_packing_required_validation()
    {
        $order = Order::factory()->packing()->create();
        $product = Product::factory()->stock(100)->create();

        (new OrderManager($order))->linkProductToOrder($product, 10);

        Livewire::test(PackOrderModal::class)
            ->emit(PackOrderModal::SHOW, $order)
            ->call('orderPacked')
            ->assertHasErrors(["packing_quantities.$product->slug" => 'required']);
    }

    public function test_packing_quantity_validation()
    {
        $order = Order::factory()->packing()->create();
        $product = Product::factory()->stock(100)->create();

        (new OrderManager($order))->linkProductToOrder($product, 10);

        Livewire::test(PackOrderModal::class)
            ->emit(PackOrderModal::SHOW, $order)
            ->call('orderPacked')
            ->assertHasErrors()
            ->set('packing_quantities', [$product->slug => 15])
            ->call('orderPacked')
            ->assertHasErrors()
            ->set('packing_quantities', [$product->slug => 10])
            ->call('orderPacked')
            ->assertHasNoErrors();
    }

    public function test_correctly_progresses_model_to_shipped_state_when_order_is_packed()
    {
        $order = Order::factory()->packing()->create();
        $product = Product::factory()->stock(100)->create();

        (new OrderManager($order))->linkProductToOrder($product, 10);

        Livewire::test(PackOrderModal::class)
            ->emit(PackOrderModal::SHOW, $order)
            ->set('packing_quantities', [$product->slug => 10])
            ->call('orderPacked')
            ->assertEmitted(IndexTable::REFRESH);

        self::assertEquals(Shipped::getName(), $order->refresh()->status->getName());
    }

    /**
     * @throws StockQuantityException
     */
    public function test_sends_out_shipping_confirmation_email()
    {
        $order = Order::factory()->packing()->create();

        $product = Product::factory()->stock(100)->create();

        (new OrderManager($order))->linkProductToOrder($product, 10);

        Livewire::test(PackOrderModal::class)
            ->emit(PackOrderModal::SHOW, $order)
            ->set('packing_quantities', [$product->slug => 10])
            ->call('orderPacked')
            ->assertEmitted(IndexTable::REFRESH);

        Mail::assertSent(OrderShippedMail::class, 1);

        Mail::assertSent(OrderShippedMail::class, function (Mailable $mailable) use ($order) {
            return $mailable->hasTo($order->deliveryAddress->email_address);
        });
    }

    public function test_exports_shipping_label_correctly()
    {
        $order = Order::factory()->packing()->create();

        $product = Product::factory()->stock(100)->create();

        (new OrderManager($order))->linkProductToOrder($product, 10);

        $response = Livewire::test(PackOrderModal::class)
            ->emit(PackOrderModal::SHOW, $order)
            ->call('generateShippingLabel')
            ->assertSet('shippingLabelGenerated', true);

        $fileName = json_decode($response->lastResponse->getContent())->effects->download->name;

        self::assertEquals("{$order->reference_number}_shipping_label.pdf", $fileName);
    }
}
