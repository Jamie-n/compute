<?php

namespace Tests\Feature\Http\Livewire\Admin\Brand;


use App\Http\Livewire\Admin\Brand\IndexTable;
use App\Models\Brand;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class IndexTableTest extends TestCase
{
    public function test_can_access_index_table_as_admin()
    {
        $user = User::factory()->systemAdmin()->create();
        $this->be($user);

        $this->get(route('admin.brand.index'))
            ->assertSuccessful()
            ->assertSeeLivewire(IndexTable::class);
    }

    public function test_can_access_index_table_as_product_admin()
    {
        $user = User::factory()->productAdmin()->create();
        $this->be($user);

        $this->get(route('admin.brand.index'))
            ->assertSuccessful()
            ->assertSeeLivewire(IndexTable::class);
    }

    public function test_cannot_access_index_table_as_user()
    {
        $user = User::factory()->create();
        $this->be($user);

        $this->get(route('admin.brand.index'))
            ->assertForbidden();
    }

    public function test_cannot_access_index_table_as_warehouse_admin()
    {
        $user = User::factory()->warehouseAdmin()->create();
        $this->be($user);

        $this->get(route('admin.brand.index'))
            ->assertForbidden();
    }

    public function test_shows_brands()
    {
        $brands = Brand::factory(10)->create();

        $user = User::factory()->systemAdmin()->create();
        $this->be($user);

        $data = Livewire::test(IndexTable::class)
            ->assertSee(['Manage Brands', 'Edit', 'Delete'])
            ->viewData('brands');

        self::assertEquals($brands->count(), $data->total());
    }

    public function test_shows_empty_table_message_when_no_options_present()
    {
        Livewire::test(IndexTable::class)
            ->assertSee('No Brands Found.');
    }
}
