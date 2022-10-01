<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            DeliveryTypeSeeder::class,
            RolePermissionSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
            UserSeeder::class,
            OrderSeeder::class,
            DiscountCodeSeeder::class
        ]);
    }
}
