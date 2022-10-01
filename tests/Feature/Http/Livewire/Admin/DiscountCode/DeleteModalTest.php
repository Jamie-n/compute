<?php

namespace Tests\Feature\Http\Livewire\Admin\DiscountCode;

use App\Http\Livewire\Admin\DiscountCode\DeleteModal;
use App\Models\DiscountCode;
use App\Models\Order;
use Livewire;
use Tests\TestCase;

class DeleteModalTest extends TestCase
{
    public function test_can_bind_to_a_discount_code()
    {
        $code = DiscountCode::factory()->create();

        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW, $code)
            ->assertSet('hidden', false)
            ->assertSet('discountCode', $code);
    }

    public function test_discount_code_can_be_soft_deleted()
    {
        $code = DiscountCode::factory()->create();

        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW, $code)
            ->call('delete')
            ->assertSet('hidden', true);

        $this->assertSoftDeleted(DiscountCode::class, ['id' => $code->id]);
    }

    public function test_can_hide_modal_by_calling_hide()
    {
        $code = DiscountCode::factory()->create();

        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW, $code)
            ->call('hide')
            ->assertSet('hidden', true);

        $this->assertNotSoftDeleted(DiscountCode::class, ['id' => $code->id]);
    }

    public function test_model_is_updated_when_emitting_new_event()
    {
        $code = DiscountCode::factory()->create();
        $code2 = DiscountCode::factory()->create();

        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW, $code)
            ->assertSet('discountCode', $code)
            ->emit(DeleteModal::SHOW, $code2)
            ->assertSet('discountCode', $code2)
            ->assertSet('hidden', false);
    }
}
