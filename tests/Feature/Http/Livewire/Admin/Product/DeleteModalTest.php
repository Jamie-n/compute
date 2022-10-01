<?php

namespace Tests\Feature\Http\Livewire\Admin\Product;

use App\Http\Livewire\Admin\Product\DeleteModal;
use App\Models\Product;
use Livewire\Livewire;
use Tests\TestCase;

class DeleteModalTest extends TestCase
{
    public function test_emitting_show_event_shows_modal()
    {
        $product = Product::factory()->stock(10)->create();

        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW, $product)
            ->assertSet('hidden', false);
    }

    public function test_can_hide_modal()
    {
        $product = Product::factory()->stock(10)->create();

        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW, $product)
            ->assertSet('hidden', false)
            ->call('hide')
            ->assertSet('hidden', true);
    }

    public function test_correctly_binds_to_product_model()
    {
        $product = Product::factory()->stock(10)->create();

        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW, $product)
            ->assertSet('product', $product);
    }


    public function test_deletes_bound_product_when_delete_called()
    {
        $product = Product::factory()->stock(10)->create();

        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW, $product)
            ->call('delete');

        $this->assertSoftDeleted($product->refresh());
    }

    public function test_updates_product_attribute_when_deleting_different_product()
    {
        $product = Product::factory()->stock(10)->create();
        $product2 = Product::factory()->stock(10)->create();

        Livewire::test(DeleteModal::class)
            ->emit(DeleteModal::SHOW, $product)
            ->assertSet('product', $product)
            ->set('hidden', true)
            ->emit(DeleteModal::SHOW, $product2)
            ->assertSet('product', $product2);
    }

}
