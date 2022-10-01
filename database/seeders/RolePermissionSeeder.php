<?php

namespace Database\Seeders;

use App\Support\Enums\Permissions;
use App\Support\Enums\UserRoles;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        collect(Permissions::cases())->each(function (Permissions $name) {
            Permission::create(['name' => $name]);
        });

        collect(UserRoles::cases())->each(function (UserRoles $name) {
            Role::create(['name' => $name]);
        });

        Role::whereName(UserRoles::SYSTEM_ADMIN->value)->first()->givePermissionTo(Permissions::ADMIN->value,Permissions::MANAGE_PRODUCTS->value, Permissions::MANAGE_USERS->value, Permissions::MANAGE_SHIPPING->value);
        Role::whereName(UserRoles::PRODUCT_ADMIN->value)->first()->givePermissionTo(Permissions::ADMIN->value, Permissions::MANAGE_PRODUCTS->value);
        Role::whereName(UserRoles::WAREHOUSE_OPERATIVE->value)->first()->givePermissionTo(Permissions::ADMIN->value, Permissions::MANAGE_SHIPPING->value);
    }
}
