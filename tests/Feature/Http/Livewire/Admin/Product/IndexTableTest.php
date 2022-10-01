<?php

namespace Tests\Feature\Http\Livewire\Admin\Product;

use App\Http\Livewire\Admin\Product\IndexTable;
use App\Models\Product;
use App\Models\User;
use App\Support\Enums\UserRoles;
use Doctrine\DBAL\Schema\Index;
use Livewire\Livewire;
use Tests\TestCase;

class IndexTableTest extends TestCase
{
    public function test_shows_index_table_component_when_accessing_component_via_route()
    {
        $user = User::factory()->create();

        $user->assignRole(UserRoles::SYSTEM_ADMIN->value);
        $this->be($user);

        $this->get(route('admin.products.index'))
            ->assertSuccessful()
            ->assertSeeLivewire(IndexTable::class);
    }

    public function test_sees_correct_page_title()
    {
        Livewire::test(IndexTable::class)->assertSee('Manage Products');
    }

    public function test_all_products_are_shown_on_index_table()
    {
        Product::factory(50)->create();

        $user = User::factory()->create();
        $user->assignRole(UserRoles::SYSTEM_ADMIN->value);

        $this->be($user);

        $products = Livewire::test(IndexTable::class)
            ->viewData('products');

        self::assertEquals($products->total(), 50);
    }

    public function test_system_admin_can_access_product_index_page()
    {
        $user = User::factory()->create();

        $user->assignRole(UserRoles::SYSTEM_ADMIN->value);
        $this->be($user);

        $this->assertTrue($user->hasRole(UserRoles::SYSTEM_ADMIN->value));

        $this->get(route('admin.products.index'))->assertSuccessful();
    }

    public function test_product_admin_can_access_product_index_page()
    {
        $user = User::factory()->create();

        $user->assignRole(UserRoles::PRODUCT_ADMIN->value);
        $this->be($user);

        $this->assertTrue($user->hasRole(UserRoles::PRODUCT_ADMIN->value));

        $this->get(route('admin.products.index'))->assertSuccessful();
    }

    public function test_regular_user_cannot_access_admin_user_index_page()
    {
        $user = User::factory()->create();

        $this->be($user);

        $this->assertTrue($user->roles()->get()->isEmpty());

        $this->get(route('admin.users.admins.index'))->assertForbidden();
    }

    public function test_can_filter_products_by_name()
    {
        $products = Product::factory(15)->create();

        $product = $products->random();

        $data = Livewire::test(IndexTable::class)
            ->set('search_term', $product->name)
            ->viewData('products');

        self::assertEquals(1, $data->count());

        self::assertTrue($product->is($data->first()));
    }

    public function test_can_fuzzy_search_products_by_name()
    {
        $product = Product::factory()->stock(10)->create();

        $data = Livewire::test(IndexTable::class)
            ->set('search_term', substr($product->name, 0, 3))
            ->viewData('products');

        self::assertTrue($product->is($data->first()));
    }

    public function test_query_string_sets_search()
    {
        $product = Product::factory()->stock(10)->create();

        $component = Livewire::withQueryParams(['search' => $product->name]);

        $data = $component->test(IndexTable::class)->assertSet('search_term', $product->name)->viewData('products');

        self::assertTrue($product->is($data->first()));
    }

    public function test_pagination_is_reset_when_searching()
    {
        $products = Product::factory(150)->create();

        Livewire::test(IndexTable::class)
            ->set('page', 2)
            ->assertSet('page', 2)
            ->set('search_term', $products->random()->name)
            ->assertSet('page', 1);
    }
}
