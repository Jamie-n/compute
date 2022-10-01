<?php

namespace Tests\Feature\Http\Livewire\Admin\DiscountCode;

use App\Http\Livewire\Admin\DiscountCode\IndexTable;
use App\Models\DiscountCode;
use App\Models\User;
use Livewire;
use Tests\TestCase;

class IndexTableTest extends TestCase
{
    public function test_can_access_index_table_as_admin()
    {
        $user = User::factory()->systemAdmin()->create();
        $this->be($user);

        $this->get(route('admin.discount-codes.index'))
            ->assertSuccessful()
            ->assertSeeLivewire(IndexTable::class);
    }

    public function test_can_access_index_table_as_product_admin()
    {
        $user = User::factory()->productAdmin()->create();
        $this->be($user);

        $this->get(route('admin.discount-codes.index'))
            ->assertSuccessful()
            ->assertSeeLivewire(IndexTable::class);
    }

    public function test_cannot_access_index_table_as_user()
    {
        $user = User::factory()->create();
        $this->be($user);

        $this->get(route('admin.discount-codes.index'))
            ->assertForbidden();
    }

    public function test_cannot_access_index_table_as_warehouse_admin()
    {
        $user = User::factory()->warehouseAdmin()->create();
        $this->be($user);

        $this->get(route('admin.discount-codes.index'))
            ->assertForbidden();
    }

    public function test_shows_discount_codes()
    {
        $codes = DiscountCode::factory(10)->create();

        $user = User::factory()->systemAdmin()->create();
        $this->be($user);

        $data = Livewire::test(IndexTable::class)->viewData('codes');

        self::assertEquals($codes->count(), $data->total());
    }
}
