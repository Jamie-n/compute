<?php

namespace Tests\Feature\Http\Livewire\Admin\User;

use App\Http\Livewire\Admin\User\CreateModal;
use App\Http\Livewire\Admin\User\IndexTable;
use App\Models\User;
use App\Support\Enums\UserRoles;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CreateModalTest extends TestCase
{

    public function test_emitting_show_event_shows_modal()
    {
        Livewire::test(CreateModal::class)
            ->emit(CreateModal::SHOW)
            ->assertSet('hidden', false);
    }

    public function test_modal_can_be_hidden()
    {
        Livewire::test(CreateModal::class)
            ->emit(CreateModal::SHOW)
            ->assertSet('hidden', false)
            ->call('hide')
            ->assertSet('hidden', true);
    }

    public function test_user_can_be_created_from_modal()
    {
        $adminRole = Role::whereName(UserRoles::SYSTEM_ADMIN->value)->first();

        Livewire::test(CreateModal::class)
            ->set('user.name', 'test')
            ->set('user.email', 'test@ex.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('selected_roles', [$adminRole->id, true])
            ->call('save')
            ->assertEmitted(IndexTable::REFRESH)
            ->assertSet('hidden', true);

        $this->assertDatabaseHas(User::class, [
            'name' => 'test',
            'email' => 'test@ex.com',
        ]);
    }

    public function test_role_is_correctly_assigned_when_a_new_admin_account_is_created()
    {
        $adminRole = Role::whereName(UserRoles::SYSTEM_ADMIN->value)->first();

        Livewire::test(CreateModal::class)
            ->set('user.name', 'test')
            ->set('user.email', 'test@ex.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('selected_roles', [$adminRole->id => true])
            ->call('save')
            ->assertEmitted(IndexTable::REFRESH)
            ->assertSet('hidden', true);

        $user = User::whereName('test')->first();

        self::assertTrue($user->hasRole(UserRoles::SYSTEM_ADMIN->value));
    }

    public function test_newly_created_users_password_is_set_correctly()
    {
        $adminRole = Role::whereName(UserRoles::SYSTEM_ADMIN->value)->first();

        Livewire::test(CreateModal::class)
            ->set('user.name', 'test')
            ->set('user.email', 'test@ex.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('selected_roles', [$adminRole->id => true])
            ->call('save')
            ->assertEmitted(IndexTable::REFRESH)
            ->assertSet('hidden', true);

        $user = User::whereName('test')->first();

        self::assertTrue(Hash::check('password', $user->password));
    }

    public function test_name_rules_work_as_expected()
    {
        $adminRole = Role::whereName(UserRoles::SYSTEM_ADMIN->value)->first();

        Livewire::test(CreateModal::class)
            ->set('user.name')
            ->set('user.email', 'test@ex.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('selected_roles', [$adminRole->id => true])
            ->call('save')
            ->assertHasErrors('user.name');
    }

    public function test_email_type_validation_rule()
    {
        $adminRole = Role::whereName(UserRoles::SYSTEM_ADMIN->value)->first();

        Livewire::test(CreateModal::class)
            ->set('user.name', 'test')
            ->set('user.email', 'test')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('selected_roles', [$adminRole->id => true])
            ->call('save')
            ->assertHasErrors('user.email');
    }

    public function test_password_and_confirmation_field()
    {
        $adminRole = Role::whereName(UserRoles::SYSTEM_ADMIN->value)->first();

        Livewire::test(CreateModal::class)
            ->set('user.name', 'test')
            ->set('user.email', 'test')
            ->set('password', 'password')
            ->set('password_confirmation', 'does_not_match')
            ->set('selected_roles', [$adminRole->id => true])
            ->call('save')
            ->assertHasErrors('password');
    }

    public function test_role_rules()
    {
        Livewire::test(CreateModal::class)
            ->set('user.name', 'test')
            ->set('user.email', 'test')
            ->set('password', 'password')
            ->set('password_confirmation', 'does_not_match')
            ->set('selected_roles', [])
            ->call('save')
            ->assertHasErrors('selected_roles');
    }
}
