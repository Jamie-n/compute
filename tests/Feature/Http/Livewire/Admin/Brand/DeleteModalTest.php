<?php

namespace Tests\Feature\Http\Livewire\Admin\Brand;

use App\Http\Livewire\Admin\Brand\DeleteModal;
use App\Http\Livewire\Admin\Brand\IndexTable;
use App\Models\Brand;
use Livewire\Livewire;
use Tests\TestCase;

class DeleteModalTest extends TestCase
{
    public function test_can_show_delete_modal()
    {
        $brand = Brand::factory()->create();

        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW, $brand)
            ->assertSet('hidden', false)
            ->assertSet('brand', $brand);
    }

    public function test_can_delete_delivery_type()
    {
        $brand = Brand::factory()->create();

        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW, $brand)
            ->call('delete')
            ->assertSet('hidden', true)
            ->assertEmitted(IndexTable::REFRESH);

        $this->assertSoftDeleted(Brand::class, ['id' => $brand->id]);

    }

    public function test_can_hide_modal()
    {
        $brand = Brand::factory()->create();

        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW, $brand)
            ->assertSet('hidden', false)
            ->call('hide')
            ->assertSet('hidden', true);
    }

    public function test_binds_to_new_model()
    {
        $brand = Brand::factory()->create();
        $brand2 = Brand::factory()->create();

        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW, $brand)
            ->assertSet('brand', $brand)
            ->emit(DeleteModal::SHOW, $brand2)
            ->assertSet('brand', $brand2);
    }

    public function test_modal_title_text_is_correct()
    {
        $brand = Brand::factory()->create();

        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW, $brand)
            ->assertSee('Delete Brand: ' . $brand->name);
    }
}
