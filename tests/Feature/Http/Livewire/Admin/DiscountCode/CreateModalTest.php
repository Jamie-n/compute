<?php

namespace Tests\Feature\Http\Livewire\Admin\DiscountCode;

use App\Http\Livewire\Admin\DiscountCode\CreateModal;
use App\Http\Livewire\Admin\DiscountCode\IndexTable;
use App\Models\DiscountCode;
use Livewire;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateModalTest extends TestCase
{
    public function test_can_show_modal()
    {
        Livewire::test(CreateModal::class)
            ->emit(CreateModal::SHOW)
            ->assertSet('hidden', false);
    }

    public function test_can_create_new_discount_code()
    {
        $code = 'TEST CODE';

        Livewire::test(CreateModal::class)
            ->emit(CreateModal::SHOW)
            ->assertSet('hidden', false)
            ->set('discountCode.code', $code)
            ->set('discountCode.discount_percentage', 15)
            ->set('code_active_start', now()->format('d-m-y'))
            ->set('code_active_end', now()->addDay()->format('d-m-y'))
            ->call('save')
            ->assertSet('hidden', true)
            ->assertEmitted(IndexTable::REFRESH);

        $this->assertDatabaseHas(DiscountCode::class, ['code' => $code, 'discount_percentage' => 15]);
    }

    /**
     * @dataProvider dataWhichShouldFail
     */
    public function test_validation_rules_fail($key, $value, $rule)
    {
        Livewire::test(CreateModal::class)
            ->set($key, $value)
            ->call('save')
            ->assertHasErrors([$key => $rule]);
    }

    public function dataWhichShouldFail(): array
    {
        return [
            'discount code required' => ['discountCode.code', '', 'required'],
            'discount code too long' => ['discountCode.code', Str::random(31), 'max:30'],

            'discount percentage required' => ['discountCode.discount_percentage', '', 'required'],
            'discount percentage not and int' => ['discountCode.discount_percentage', 'abc', 'integer'],
            'discount percentage above 100' => ['discountCode.discount_percentage', 150, 'max:100'],

            'code active start required' => ['code_active_start', '', 'required'],
            'code active start not a date' => ['code_active_start', 'abc', 'date'],

            'code active end required' => ['code_active_end', '', 'required'],
            'code active end not a date' => ['code_active_end', 'abc', 'date']
        ];
    }

    /**
     * @dataProvider dataWhichShouldPass
     */
    public function test_validation_rules_pass($key, $value, $rule)
    {
        Livewire::test(CreateModal::class)
            ->set($key, $value)
            ->call('save')
            ->assertHasNoErrors([$key => $rule]);
    }

    public function dataWhichShouldPass(): array
    {
        return [
            'discount code required' => ['discountCode.code', 'a', 'required'],
            'discount code max' => ['discountCode.code', Str::random(30), 'max:30'],
            'discount under max' => ['discountCode.code', Str::random(15), 'max:30'],

            'discount percentage required' => ['discountCode.discount_percentage', 'a', 'required'],
            'discount percentage is an int' => ['discountCode.discount_percentage', 15, 'integer'],
            'discount percentage 100' => ['discountCode.discount_percentage', 100, 'max:100'],
            'discount percentage under 100' => ['discountCode.discount_percentage', 50, 'max:100'],

            'code active start required' => ['code_active_start', now()->format('d-m-y'), 'required'],
            'code active start not a date' => ['code_active_start', now()->format('d-m-y'), 'date'],

            'code active end required' => ['code_active_end', now()->format('d-m-y'), 'required'],
            'code active end not a date' => ['code_active_end', now()->format('d-m-y'), 'date']
        ];
    }
}
