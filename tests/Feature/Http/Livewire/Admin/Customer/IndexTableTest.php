<?php

namespace Tests\Feature\Http\Livewire\Admin\Customer;

use App\Http\Livewire\Admin\Customer\IndexTable;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class IndexTableTest extends TestCase
{
    public function test_system_admin_can_view_index_table()
    {
        $this->be(User::factory()->systemAdmin()->create());

        $this->get(route('admin.users.customers.index'))
            ->assertSuccessful()
            ->assertSeeLivewire(IndexTable::class)
            ->assertSee('View Registered Customers');
    }

    public function test_product_admin_cant_view_index_table()
    {
        $this->be(User::factory()->productAdmin()->create());

        $this->get(route('admin.users.customers.index'))->assertForbidden();
    }

    public function test_warehouse_admin_cant_view_index_table()
    {
        $this->be(User::factory()->warehouseAdmin()->create());

        $this->get(route('admin.users.customers.index'))->assertForbidden();
    }

    public function test_user_cant_view_index_table()
    {
        $this->be(User::factory()->create());

        $this->get(route('admin.users.customers.index'))->assertForbidden();
    }

    public function test_can_see_all_users()
    {
        User::factory(50)->create();

        $data = Livewire::test(IndexTable::class)
            ->viewData('users');

        self::assertEquals(User::count(), $data->total());
    }

    public function test_can_search_for_users_by_name()
    {
        User::factory(30)->create();
        $user = User::all()->random();

        $viewData = Livewire::test(IndexTable::class)->set('search_term', $user->name)->viewData('users');

        self::assertEquals($user, $viewData->first());
    }

    public function test_can_search_for_users_by_email()
    {
        User::factory(30)->create();
        $user = User::all()->random();

        $viewData = Livewire::test(IndexTable::class)->set('search_term', $user->email)->viewData('users');

        self::assertEquals($user, $viewData->first());
    }

    public function test_shows_correct_account_type_icons_for_local_user()
    {
        $user = User::factory()->create(['oauth_id' => '']);

        Livewire::test(IndexTable::class)->set('search_term', $user->email)
            ->assertSee(['Local Account', 'fas fa-microchip'])
            ->assertDontSee(['oAuth Account', 'fab fa-facebook', 'fab fa-google']);
    }

    public function test_shows_correct_icons_for_oauth_user()
    {
        $user = User::factory()->create(['oauth_id' => 'abc']);

        Livewire::test(IndexTable::class)->set('search_term', $user->email)
            ->assertDontSee(['Local Account', 'fas fa-microchip'])
            ->assertSee(['oAuth Account', 'fab fa-facebook', 'fab fa-google']);
    }

    public function test_table_contains_correct_items()
    {
        $user = User::factory()->create(['oauth_id' => 'abc']);

        Livewire::test(IndexTable::class)->set('search_term', $user->email)
            ->assertDontSee(['Local Account', 'fas fa-microchip'])
            ->assertSee([$user->name, $user->email, $user->created_at->format('d/m/Y H:i:s')]);
    }

    public function test_shows_empty_table_message_when_no_options_present()
    {
        Livewire::test(IndexTable::class)
            ->assertSee('No Users Found.');
    }

}

