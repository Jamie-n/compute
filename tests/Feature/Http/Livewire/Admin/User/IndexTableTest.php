<?php

namespace Tests\Feature\Http\Livewire\Admin\User;

use App\Http\Livewire\Admin\User\IndexTable;
use App\Models\User;
use App\Support\Enums\UserRoles;
use Livewire\Livewire;
use Tests\TestCase;

class IndexTableTest extends TestCase
{
    public function test_shows_index_table_component_when_accessing_component_via_route()
    {
        $user = User::factory()->create();

        $user->assignRole(UserRoles::SYSTEM_ADMIN->value);
        $this->be($user);

        $this->get(route('admin.users.admins.index'))
            ->assertSuccessful()
            ->assertSeeLivewire(IndexTable::class);
    }

    public function test_sees_correct_page_title()
    {
        Livewire::test(IndexTable::class)->assertSee('Manage Administrative Users');
    }

    public function test_only_admin_users_are_shown_on_admin_index_table()
    {
        $admin = User::factory()->create();
        $admin->assignRole(UserRoles::SYSTEM_ADMIN->value);

        $productAdmin = User::factory()->create();
        $productAdmin->assignRole(UserRoles::PRODUCT_ADMIN->value);

        User::factory()->create();

        $users = Livewire::test(IndexTable::class)->viewData('users');

        self::assertEquals(2, $users->count());
    }

    public function test_system_admin_can_access_admin_user_index_page()
    {
        $user = User::factory()->create();

        $user->assignRole(UserRoles::SYSTEM_ADMIN->value);
        $this->be($user);

        $this->assertTrue($user->hasRole(UserRoles::SYSTEM_ADMIN->value));

        $this->get(route('admin.users.admins.index'))->assertSuccessful();

    }

    public function test_product_admin_cannot_access_admin_user_index_page()
    {
        $user = User::factory()->create();

        $user->assignRole(UserRoles::PRODUCT_ADMIN->value);
        $this->be($user);

        $this->assertTrue($user->hasRole(UserRoles::PRODUCT_ADMIN->value));

        $this->get(route('admin.users.admins.index'))->assertForbidden();
    }

    public function test_regular_user_cannot_access_admin_user_index_page()
    {
        $user = User::factory()->create();

        $this->be($user);

        $this->assertTrue($user->roles()->get()->isEmpty());

        $this->get(route('admin.users.admins.index'))->assertForbidden();
    }

    public function test_can_filter_users_by_username()
    {
        $users = User::factory(5)->systemAdmin()->create();
        $user = $users->random();

        $shownResults = Livewire::test(IndexTable::class)
            ->set('search_term', $user->name)
            ->viewData('users');

        self::assertEquals(1, $shownResults->total());

        self::assertTrue($shownResults->first()->is($user));
    }

    public function test_can_filter_users_by_email()
    {
        $users = User::factory(5)->systemAdmin()->create();
        $user = $users->random();

        $shownResults = Livewire::test(IndexTable::class)
            ->set('search_term', $user->name)
            ->viewData('users');

        self::assertEquals(1, $shownResults->total());

        self::assertTrue($shownResults->first()->is($user));
    }

    public function test_filtering_users_only_shows_admin_users()
    {
        $admin = User::factory()->systemAdmin()->create();

        User::factory(10)->create();

        $shownResults = Livewire::test(IndexTable::class)
            ->set('search_term', $admin->name)
            ->viewData('users');

        self::assertEquals(1, $shownResults->total());

        self::assertTrue($shownResults->first()->is($admin));
    }

    public function test_query_string_sets_search()
    {
        $user = User::factory()
            ->productAdmin()
            ->create();

        $component = Livewire::withQueryParams(['search' => $user->name]);

        $data = $component->test(IndexTable::class)
            ->assertSet('search_term', $user->name)
            ->viewData('users');

        self::assertTrue($user->is($data->first()));
    }

    public function test_pagination_is_reset_when_searching()
    {
        $users = User::factory(150)->systemAdmin()->create();

        Livewire::test(IndexTable::class)
            ->set('page', 2)
            ->assertSet('page', 2)
            ->set('search_term', $users->random()->name)
            ->assertSet('page', 1);
    }
}
