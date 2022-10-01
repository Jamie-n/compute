<?php

namespace Tests\Feature\Http\Livewire\Admin\Delivery;

use App\Http\Livewire\Admin\Delivery\CreateModal;
use App\Http\Livewire\Admin\Delivery\IndexTable;
use App\Models\DeliveryType;
use Illuminate\Support\Str;
use Livewire;
use Tests\TestCase;

class CreateModalTest extends TestCase
{
    public function test_can_show_modal()
    {
        Livewire::test(CreateModal::class)
            ->emit(CreateModal::SHOW)
            ->assertSet('hidden', false);
    }

    public function test_can_hide_modal()
    {
        Livewire::test(CreateModal::class)
            ->emit(CreateModal::SHOW)
            ->assertSet('hidden', false)
            ->call('hide')
            ->assertSet('hidden', true);
    }

    public function test_can_create_new_delivery_option()
    {
        $option = DeliveryType::factory()->make();

        Livewire::test(CreateModal::class)
            ->set('deliveryType.name', $option->name)
            ->set('deliveryType.description', $option->description)
            ->set('deliveryType.price', $option->price)
            ->call('save')
            ->assertSet('hidden', true)
            ->assertEmitted(IndexTable::REFRESH);

        $this->assertDatabaseHas(DeliveryType::class, ['name' => $option->name]);

    }

    /**
     * @dataProvider dataThatShouldFail
     */
    public function test_validation_rules_fail($key, $value, $rule)
    {
        Livewire::test(CreateModal::class)
            ->set("deliveryType.{$key}", $value)
            ->call('save')
            ->assertHasErrors(['deliveryType.' . $key => $rule]);
    }

    public function dataThatShouldFail(): array
    {
        return [
            'name required' => ['name', '', 'required'],
            'name too long' => ['name', Str::random(31), 'max:30'],

            'description to long' => ['description', Str::random(256), 'max:255'],

            'price required' => ['price', '', 'required'],
            'price not numeric' => ['price', 'abc', 'numeric'],
        ];
    }

    /**
     * @dataProvider dataThatShouldPass
     */
    public function test_validation_rules_pass($key, $value, $rule)
    {
        Livewire::test(CreateModal::class)
            ->set("deliveryType.{$key}", $value)
            ->call('save')
            ->assertHasNoErrors(['deliveryType' . $key => $rule]);
    }

    public function dataThatShouldPass()
    {
        return [
            'name required' => ['name', 'abc', 'required'],
            'name max length' => ['name', Str::random(30), 'max:30'],
            'name less than max' => ['name', Str::random(29), 'max:30'],

            'description max' => ['description', Str::random(255), 'max:255'],
            'description less than max' => ['description', Str::random(254), 'max:255'],

            'price required' => ['price', 'abc', 'required'],
            'price not numeric' => ['price', 1.99, 'numeric'],
        ];
    }
}
