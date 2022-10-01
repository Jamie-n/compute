<?php

namespace Tests\Feature\Http\Livewire\Admin\Brand;

use App\Http\Livewire\Admin\Brand\CreateModal;
use App\Http\Livewire\Admin\Brand\IndexTable;
use App\Models\Brand;
use Illuminate\Support\Str;
use Livewire\Livewire;
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
        $brand = Brand::factory()->make();

        Livewire::test(CreateModal::class)
            ->set('brand.name', $brand->name)
            ->set('brand.slug', $brand->slug)
            ->call('save')
            ->assertSet('hidden', true)
            ->assertEmitted(IndexTable::REFRESH);

        $this->assertDatabaseHas(Brand::class, ['name' => $brand->name]);
    }

    /**
     * @dataProvider dataThatShouldFail
     */
    public function test_validation_rules_fail($key, $value, $rule)
    {
        Livewire::test(CreateModal::class)
            ->set("brand.{$key}", $value)
            ->call('save')
            ->assertHasErrors(['brand.' . $key => $rule]);
    }

    public function dataThatShouldFail(): array
    {
        return [
            'brand name required' => ['name', '', 'required'],
            'brand name too long' => ['name', Str::random(256), 'max:255'],

            'slug required' => ['slug', '', 'required'],
            'slug too long' => ['slug', Str::random(256), 'max:255'],
        ];
    }

    /**
     * @dataProvider dataThatShouldPass
     */
    public function test_validation_rules_pass($key, $value, $rule)
    {
        Livewire::test(CreateModal::class)
            ->set("brand.{$key}", $value)
            ->call('save')
            ->assertHasNoErrors(['brand' . $key => $rule]);
    }

    public function dataThatShouldPass()
    {
        return [
            'brand name required' => ['name', 'a', 'required'],
            'brand max' => ['name', Str::random(255), 'max:255'],
            'brand less than max' => ['name', Str::random(254), 'max:255'],
            'name string' => ['name', 'abc', 'string'],

            'slug required' => ['slug', 'a', 'required'],
            'slug max' => ['slug', Str::random(255), 'max:255'],
            'slug less than max' => ['slug', Str::random(254), 'max:255'],
            'slug string' => ['slug', 'a', 'string'],
        ];
    }
}
