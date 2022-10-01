<?php

namespace Tests\Feature\Http\Livewire\Admin\User;

use App\Http\Livewire\Admin\User\DeleteModal;
use App\Http\Livewire\Admin\User\IndexTable;
use App\Models\User;
use App\Support\Enums\UserRoles;
use Livewire\Livewire;
use Tests\TestCase;

class DeleteModalTest extends TestCase
{
    public function test_emitting_show_event_shows_modal()
    {
        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW)
            ->assertSet('hidden', false);
    }

    public function test_modal_can_be_hidden()
    {
        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW)
            ->assertSet('hidden', false)
            ->call('hide')
            ->assertSet('hidden', true);
    }

    public function test_user_model_is_bound_to_component_when_show_method_called()
    {
        $testUser = User::factory()->create();

        Livewire::test(DeleteModal::class)
            ->call('show', ['user' => $testUser->slug])
            ->assertSet('user.id', $testUser->id);
    }

    public function test_admin_is_soft_deleted_when_delete_is_called()
    {
        $testUser = User::factory()->create();
        $testUser->assignRole(UserRoles::PRODUCT_ADMIN->value);

        Livewire::test(DeleteModal::class)
            ->call('show', ['user' => $testUser->slug])
            ->call('delete');

        $this->assertSoftDeleted(User::class, ['id' => $testUser->id]);
    }

    public function test_refresh_event_is_emitted_when_a_model_is_deleted()
    {
        $testUser = User::factory()->create();
        $testUser->assignRole(UserRoles::PRODUCT_ADMIN->value);

        Livewire::test(DeleteModal::class)
            ->call('show', ['user' => $testUser->slug])
            ->call('delete')
            ->assertEmitted(IndexTable::REFRESH)
            ->assertSet('hidden', true);
    }

    public function test_all_attributes_are_updated_correctly_when_a_new_model_is_selected()
    {
        $testUser = User::factory()->create();

        $testUser2 = User::factory()->create();

        Livewire::test(DeleteModal::class)
            ->call('show', ['user' => $testUser->slug])
            ->assertSet('user.id', $testUser->id)
            ->set('hidden', true)
            ->call('show', ['user' => $testUser2->slug])
            ->assertSet('user.id', $testUser2->id);
    }
}
