<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use App\Support\Enums\UserRoles;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    function test_customer_cannot_access_admin_index()
    {
        $defaultUser = User::factory()->create();

        $this->be($defaultUser);

        $this->get(route('admin.index'))->assertForbidden();
    }

    function test_system_admin_can_reach_index_page()
    {
        $systemAdmin = User::factory()->create();

        $systemAdmin->assignRole(UserRoles::SYSTEM_ADMIN->value);

        $this->be($systemAdmin);

        $this->get(route('admin.index'))->assertSuccessful();
    }

    function test_product_admin_can_reach_index_page()
    {
        $systemAdmin = User::factory()->create();

        $systemAdmin->assignRole(UserRoles::PRODUCT_ADMIN->value);

        $this->be($systemAdmin);

        $this->get(route('admin.index'))->assertSuccessful();
    }
}
