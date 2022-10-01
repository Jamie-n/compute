<?php

namespace Tests\Feature\Http\Livewire\Admin\Warehouse;

use App\Http\Livewire\Admin\Warehouse\IndexTable;
use App\Models\DeliveryType;
use App\Models\Order;
use App\Models\User;
use App\Support\States\Delivered;
use App\Support\States\Packing;
use App\Support\States\Processing;
use Livewire\Livewire;
use Tests\TestCase;

class IndexTableTest extends TestCase
{
    public function test_only_shows_orders_which_are_processing_when_mounted()
    {
        $processingOrders = Order::factory(10)->processing()->create();
        Order::factory(15)->packing()->create();

        $orders = Livewire::test(IndexTable::class)->viewData('orders');

        self::assertEquals($processingOrders->count(), $orders->total());
    }

    public function test_order_status_is_set_to_processing_by_default()
    {
        Livewire::test(IndexTable::class)->assertSet('order_status', Processing::getName());
    }

    public function test_orders_are_correctly_filtered_when_updating_order_status()
    {
        Order::factory(10)->processing()->create();
        $packingOrders = Order::factory(15)->packing()->create();

        $orders = Livewire::test(IndexTable::class)
            ->set('order_status', Packing::getName())
            ->viewData('orders');

        self::assertEquals($packingOrders->count(), $orders->total());
    }

    public function test_can_filter_orders_by_reference_number()
    {
        $orders = Order::factory(15)->create();

        $viewData = Livewire::test(IndexTable::class)
            ->set('reference_number', $orders->last()->reference_number)
            ->viewData('orders');

        self::assertEquals($orders->last()->id, $viewData->first()->id);
    }

    public function test_pagination_is_reset_when_filtering()
    {
        $orders = Order::factory(150)->create();

        Livewire::test(IndexTable::class)
            ->set('page', 2)
            ->assertSet('page', 2)
            ->set('reference_number', $orders->last()->reference_number)
            ->assertSet('page', 1)
            ->set('page', 2)
            ->assertSet('page', 2)
            ->set('order_status', Delivered::class)
            ->assertSet('page', 1);
    }

    public function test_system_admin_can_access_shipping_page()
    {
        $user = User::factory()->systemAdmin()->create();

        $this->be($user);

        $this->get(route('admin.shipping.index'))->assertSuccessful();

    }

    public function test_warehouse_operative_can_access_shipping_page()
    {
        $user = User::factory()->warehouseAdmin()->create();

        $this->be($user);

        $this->get(route('admin.shipping.index'))->assertSuccessful();
    }

    public function test_product_admin_cannot_access_shipping_page()
    {
        $user = User::factory()->productAdmin()->create();

        $this->be($user);

        $this->get(route('admin.shipping.index'))->assertForbidden();
    }

    public function test_user_cannot_access_shipping_page()
    {
        $user = User::factory()->create();

        $this->be($user);

        $this->get(route('admin.shipping.index'))->assertForbidden();
    }

    public function test_query_string_sets_search()
    {
        $order = Order::factory()->create();

        $component = Livewire::withQueryParams(['reference-number' => $order->reference_number]);

        $data = $component->test(IndexTable::class)->assertSet('reference_number', $order->reference_number)->viewData('orders');

        self::assertTrue($order->is($data->first()));
    }

    public function test_can_filter_orders_by_delivery_type()
    {
        Order::factory(10)->nextDay()->processing()->create();
        $expressOrders = Order::factory(15)->processing()->expressDelivery()->create();

        $orders = Livewire::test(IndexTable::class)
            ->set('order_status', Processing::getName())
            ->set('delivery_type', DeliveryType::whereName('Express Delivery')->first()->id)
            ->viewData('orders');

        self::assertEquals($expressOrders->count(), $orders->total());
    }
}
