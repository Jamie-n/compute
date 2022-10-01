<?php

namespace Tests\Feature\Http\Livewire\Admin\Delivery;

use App\Http\Livewire\Admin\Delivery\IndexTable;
use App\Models\DeliveryType;
use App\Models\User;
use Livewire;
use Tests\TestCase;

class IndexTableTest extends TestCase
{
    public function test_admin_can_access_index_table()
    {
        $this->be(User::factory()->systemAdmin()->create());

        $this->get(route('admin.delivery.index'))
            ->assertSeeLivewire(IndexTable::class);
    }

    public function test_warehouse_admin_can_access_index_table()
    {
        $this->be(User::factory()->warehouseAdmin()->create());

        $this->get(route('admin.delivery.index'))
            ->assertSeeLivewire(IndexTable::class);
    }

    public function test_product_admin_cannot_access_index_table()
    {
        $this->be(User::factory()->productAdmin()->create());

        $this->get(route('admin.delivery.index'))->assertForbidden();
    }

    public function test_user_cannot_access_index_table()
    {
        $this->be(User::factory()->create());

        $this->get(route('admin.delivery.index'))->assertForbidden();
    }

    public function test_shows_delivery_options()
    {
        DeliveryType::factory(30)->create();

        $viewData = Livewire::test(IndexTable::class)
            ->viewData('deliveryTypes');


        self::assertEquals(DeliveryType::count(), $viewData->total());

        self::assertEquals(config('pagination.admin_delivery_types_page_length'), $viewData->perPage());
    }

    public function test_shows_empty_table_message_when_no_options_present()
    {
        //Delete the pre-seeded models
        DeliveryType::query()->delete();

        Livewire::test(IndexTable::class)
            ->assertSeeHtml('<td colspan="4">No Delivery Types Found.</td>');
    }
}
