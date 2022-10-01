<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {
        Order::factory(10)
            ->processing()
            ->hasAttached(
                Product::factory()
                    ->count(rand(1, 8))->state(new Sequence(
                        fn($sequence) => ['brand_id' => Brand::all()->random()],
                    )),
                new Sequence(fn() => ['unit_price' => fake()->randomFloat(2, 1, 5000), 'quantity' => rand(1, 10)])

            )->create();

        Order::factory(100)
            ->packing()
            ->hasAttached(
                Product::factory()
                    ->count(rand(1, 8))->state(new Sequence(
                        fn($sequence) => ['brand_id' => Brand::all()->random()],
                    )),
                new Sequence(fn() => ['unit_price' => fake()->randomFloat(2, 1, 5000), 'quantity' => rand(1, 10)])
            )->create();

        Order::factory(150)->shipped()->hasAttached(
            Product::factory()
                ->count(rand(1, 8))->state(new Sequence(
                    fn($sequence) => ['brand_id' => Brand::all()->random()],
                )),
            new Sequence(fn() => ['unit_price' => fake()->randomFloat(2, 1, 5000), 'quantity' => rand(1, 10)])
        )->create();

        Order::factory(100)
            ->delivered()
            ->hasAttached(
                Product::factory()
                    ->count(rand(1, 8))->state(new Sequence(
                        fn($sequence) => ['brand_id' => Brand::all()->random()],
                    )),
                new Sequence(fn() => ['unit_price' => fake()->randomFloat(2, 1, 5000), 'quantity' => rand(1, 10)])
            )->create();
    }
}
