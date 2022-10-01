<?php

namespace Tests\Feature\Http\Livewire\Admin;

use App\Models\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function test_can_see_all_button_options_as_system_admin()
    {
        $this->be(User::factory()->systemAdmin()->create());

        $shouldSee = [
            'User Management',
            'Manage Administrators',
            'Product Management',
            'Manage Products',
            'Manage Brands',
            'Manage Discount Codes',
            'Stock/Warehouse Management',
            'Manage Shipping',
            'Manage Delivery Options'
        ];

        $this->get(route('admin.index'))
            ->assertSee($shouldSee);

    }

    public function test_can_only_see_product_management_options_as_product_manager()
    {
        $this->be(User::factory()->productAdmin()->create());

        $doSee = [
            'Product Management',
            'Manage Products',
            'Manage Brands',
            'Manage Discount Codes',
        ];


        $dontSee = [
            'User Management',
            'Manage Administrators',
            'Stock/Warehouse Management',
            'Manage Shipping',
            'Manage Delivery Options',
        ];

        $this->get(route('admin.index'))
            ->assertDontSee($dontSee)
            ->assertSee($doSee);
    }

    public function test_product_admin_sees_correct_options()
    {
        $this->be(User::factory()->warehouseAdmin()->create());

        $doSee = [
            'Stock/Warehouse Management',
            'Manage Shipping',
            'Manage Delivery Options',
        ];

        $dontSee = [
            'User Management',
            'Manage Administrators',
            'Product Management',
            'Manage Products',
            'Manage Brands',
            'Manage Discount Codes',
        ];

        $this->get(route('admin.index'))
            ->assertDontSee($dontSee)
            ->assertSee($doSee);
    }
}
