<?php

namespace Tests\Feature\Http\Livewire\Admin\User;

use App\Http\Livewire\Admin\User\EditModal;
use App\Http\Livewire\Admin\User\IndexTable;
use App\Models\User;
use App\Support\Enums\UserRoles;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EditModalTest extends TestCase
{
    public function test_emitting_show_event_shows_modal()
    {
        Livewire::test(EditModal::class)
            ->emit(EditModal::SHOW)
            ->assertSet('hidden', false);
    }

    public function test_modal_can_be_hidden()
    {
        Livewire::test(EditModal::class)
            ->emit(EditModal::SHOW)
            ->assertSet('hidden', false)
            ->call('hide')
            ->assertSet('hidden', true);
    }

    public function test_correct_user_is_bound_when_emitting_show_event()
    {
        $user = User::factory()->create();

        Livewire::test(EditModal::class)
            ->emit(EditModal::SHOW, ['user' => $user->slug])
            ->assertSet('user.id', $user->id);
    }

    public function test_user_is_assigned_correct_role_when_updated()
    {
        $user = User::factory()->create();

        $systemAdminRole = Role::whereName(UserRoles::SYSTEM_ADMIN->value)->first();
        $roleArray = [$systemAdminRole->id => true];

        Livewire::test(EditModal::class)
            ->emit(EditModal::SHOW, ['user' => $user->slug])
            ->set('selectedRoles', $roleArray)
            ->call('save')
            ->assertEmitted(IndexTable::REFRESH);

        self::assertTrue($user->fresh()->hasRole($systemAdminRole->name));
    }

    public function test_attributes_are_reset_when_calling_show_for_a_new_model()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $user2->syncRoles([]);

        $systemAdminRole = Role::whereName(UserRoles::SYSTEM_ADMIN->value)->first();
        $roleArray = [$systemAdminRole->id => true];

        Livewire::test(EditModal::class)
            ->emit(EditModal::SHOW, ['user' => $user->slug])
            ->set('selectedRoles', $roleArray)
            ->emit(EditModal::SHOW, ['user' => $user2->slug])
            ->assertSet('selectedRoles', []);
    }
}
