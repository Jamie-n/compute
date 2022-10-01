<?php

namespace Tests\Feature\Http\Livewire\Admin\DiscountCode;

use App\Http\Livewire\Admin\DiscountCode\EditModal;
use App\Http\Livewire\Admin\DiscountCode\IndexTable;
use App\Models\DiscountCode;
use Livewire;
use Tests\TestCase;

class EditModalTest extends TestCase
{
    public function test_can_show_modal()
    {
        $code = DiscountCode::factory()->create();

        Livewire::test(EditModal::class)
            ->emit(EditModal::SHOW, $code)
            ->assertSet('hidden', false)
            ->assertSet('discountCode', $code);
    }

    public function test_can_hide_modal()
    {
        $code = DiscountCode::factory()->create();

        Livewire::test(EditModal::class)
            ->emit(EditModal::SHOW, $code)
            ->assertSet('hidden', false)
            ->call('hide')
            ->assertSet('hidden', true);
    }

    public function test_can_update_start_end_date()
    {
        $code = DiscountCode::factory()->create(['code_active_start' => now()->subDays(2), 'code_active_end' => now()->subDay()]);

        Livewire::test(EditModal::class)
            ->emit(EditModal::SHOW, $code)
            ->assertSet('hidden', false)
            ->set('code_active_start', '2021-12-12')
            ->set('code_active_end', '2021-12-13')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('hidden', true)
            ->assertEmitted(IndexTable::REFRESH)
            ->assertSet('code_active_start', '')
            ->assertSet('code_active_end', '');

        $code->refresh();

        self::assertEquals('12-12-21', $code->code_active_start->format('d-m-y'));
        self::assertEquals('13-12-21', $code->code_active_end->format('d-m-y'));
    }

    /**
     * @dataProvider dataWhichShouldFail
     */
    public function test_validation_rules_fail($key, $value, $rule)
    {
        $code = DiscountCode::factory()->create(['code_active_start' => now()->subDays(2), 'code_active_end' => now()->subDay()]);

        Livewire::test(EditModal::class)
            ->emit(EditModal::SHOW, $code)
            ->set($key, $value)
            ->call('save')
            ->assertHasErrors([$key => $rule]);
    }

    public function dataWhichShouldFail()
    {
        return [
            'code start date required' => ['code_active_start', '', 'required'],
            'code start date not date' => ['code_active_start', 'abc', 'date'],

            'code end date required' => ['code_active_end', '', 'required'],
            'code end date not date' => ['code_active_end', 'abc', 'date'],
        ];
    }

    public function test_end_date_before_start_date()
    {
        $code = DiscountCode::factory()->create(['code_active_start' => now()->subDays(2), 'code_active_end' => now()->subDay()]);

        Livewire::test(EditModal::class)
            ->emit(EditModal::SHOW, $code)
            ->set('code_active_end', now()->subDay()->format('d-m-y'))
            ->set('code_active_start', now()->format('d-m-y'))
            ->call('save')
            ->assertHasErrors(['code_active_end' => 'after']);
    }
}
