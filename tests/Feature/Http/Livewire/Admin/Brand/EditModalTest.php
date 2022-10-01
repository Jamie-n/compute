<?php

namespace Tests\Feature\Http\Livewire\Admin\Brand;

use App\Http\Livewire\Admin\Brand\EditModal;
use App\Http\Livewire\Admin\Brand\IndexTable;
use App\Models\Brand;
use Livewire\Livewire;
use Illuminate\Support\Str;
use Tests\TestCase;

class EditModalTest extends TestCase
{
    public function test_can_show_modal()
    {
        $brand = Brand::factory()->create();

        Livewire::test(EditModal::class)
            ->emit(EditModal::SHOW, $brand)
            ->assertSet('hidden', false)
            ->assertSet('brand', $brand);
    }

    public function test_can_hide_modal()
    {
        $brand = Brand::factory()->create();

        Livewire::test(EditModal::class)
            ->emit(EditModal::SHOW, $brand)
            ->assertSet('hidden', false)
            ->call('hide')
            ->assertSet('hidden', true);
    }

    public function test_can_update_brand()
    {
        $brand = Brand::factory()->create();

        Livewire::test(EditModal::class)
            ->emit(EditModal::SHOW, $brand)
            ->assertSet('hidden', false)
            ->set('brand.name', 'test')
            ->set('brand.slug', 'test-slug')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('hidden', true)
            ->assertEmitted(IndexTable::REFRESH);

        $brand->refresh();

        self::assertEquals('test', $brand->name);
        self::assertEquals('test-slug', $brand->slug);
    }

    /**
     * @dataProvider dataWhichShouldFail
     */
    public function test_validation_rules_fail($key, $value, $rule)
    {
        $brand = Brand::factory()->create();

        Livewire::test(EditModal::class)
            ->emit(EditModal::SHOW, $brand)
            ->set('brand.' . $key, $value)
            ->call('save')
            ->assertHasErrors(['brand.' . $key => $rule]);
    }

    public function dataWhichShouldFail()
    {
        return [
            'brand name required' => ['name', '', 'required'],
            'brand name too long' => ['name', Str::random(256), 'max:255'],
            'name array' => ['name', [1, 2, 3], 'string'],

            'slug required' => ['slug', '', 'required'],
            'slug too long' => ['slug', Str::random(256), 'max:255'],
            'slug array' => ['slug', [1, 2, 3], 'string'],
        ];
    }

    public function test_slug_must_be_unique()
    {
        $brand = Brand::factory()->create();

        Brand::factory()->create(['slug' => 'abc']);

        Livewire::test(EditModal::class)
            ->emit(EditModal::SHOW, $brand)
            ->set('brand.slug', 'abc')
            ->call('save')
            ->assertHasErrors(['brand.slug' => 'unique']);
    }
}
