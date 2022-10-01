<?php

namespace Tests\Feature\Http\Livewire\Admin\Delivery;

use App\Http\Livewire\Admin\Delivery\DeleteModal;
use App\Http\Livewire\Admin\Delivery\IndexTable;
use App\Models\DeliveryType;
use Livewire;
use Tests\TestCase;

class DeleteModalTest extends TestCase
{
    public function test_can_show_delete_modal()
    {
        $deliveryType = DeliveryType::factory()->create();

        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW, $deliveryType)
            ->assertSet('hidden', false)
            ->assertSet('deliveryType', $deliveryType);
    }

    public function test_can_delete_delivery_type()
    {
        $deliveryType = DeliveryType::factory()->create();

        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW, $deliveryType)
            ->call('delete')
            ->assertSet('hidden', true)
            ->assertEmitted(IndexTable::REFRESH);

        $this->assertSoftDeleted(DeliveryType::class, ['id' => $deliveryType->id]);

    }

    public function test_can_hide_modal()
    {
        $deliveryType = DeliveryType::factory()->create();

        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW, $deliveryType)
            ->assertSet('hidden', false)
            ->call('hide')
            ->assertSet('hidden', true);
    }

    public function test_binds_to_new_model()
    {
        $deliveryType = DeliveryType::factory()->create();
        $deliveryType2 = DeliveryType::factory()->create();

        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW, $deliveryType)
            ->assertSet('deliveryType', $deliveryType)
            ->emit(DeleteModal::SHOW, $deliveryType2)
            ->assertSet('deliveryType', $deliveryType2);
    }

    public function test_modal_title_text_is_correct()
    {
        $deliveryType = DeliveryType::factory()->create();

        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW, $deliveryType)
            ->assertSee('Delete Delivery Option: ' . $deliveryType->name);
    }
}
