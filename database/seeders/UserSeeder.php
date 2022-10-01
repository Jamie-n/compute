<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Support\Enums\UserRoles;
use App\Support\States\Packing;
use App\Support\States\Processing;
use App\Support\States\Shipped;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        $orders = Order::factory()
            ->count(25)
            ->state(new Sequence(
                ['status' => Processing::class],
                ['status' => Shipped::class],
                ['status' => Packing::class]))
            ->hasAttached(
                Product::factory()
                    ->count(rand(1, 8))
                    ->state(new Sequence(
                        fn($sequence) => ['brand_id' => Brand::all()->random()],
                    )),
                new Sequence(fn() => ['unit_price' => fake()->randomFloat(2, 1, 5000), 'quantity' => rand(1, 10)])
            );

        User::factory()->has($orders, 'orders')->create([
            'email' => 'test@ex.com',
            'name' => 'Test User',
            'slug' => Str::slug('Test User'),
            'password' => Hash::make('J@86&CV4Plt0P2Dex')
        ]);

        User::factory()->systemAdmin()->create([
            'email' => 'admin@compute.com',
            'name' => 'Admin User',
            'slug' => 'product-admin',
            'password' => Hash::make('s5#JrRV3i3%24UOgR')
        ]);

        User::factory()->productAdmin()->create([
            'email' => 'product@compute.com',
            'name' => 'Product Admin',
            'slug' => 'product-admin',
            'password' => Hash::make('^88fC^gY18$vh2KU%')
        ]);

        User::factory()->warehouseAdmin()->create([
            'email' => 'warehouse@compute.com',
            'name' => 'Warehouse Operative',
            'slug' => 'warehouse-operative',
            'password' => Hash::make('m%g9gYr5jBwi!F2$g')
        ]);


    }
}
