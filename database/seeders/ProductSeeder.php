<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Category::where('name', '!=', 'Deals')->get()->each(function ($category) {
            Product::factory()
                ->state(new Sequence(fn() => (['stock_quantity' => rand(0, 25)])))
                ->count(30)
                ->state(new Sequence(
                    fn($sequence) => ['brand_id' => Brand::all()->random()],
                ))
                ->hasAttached($category)
                ->create();
        });

        $deals = Category::whereName('Deals')->first();

        Product::factory(30)->state(new Sequence(fn() => ['discount_percentage' => rand(1, 99)]))->state(new Sequence(
            fn($sequence) => ['brand_id' => Brand::all()->random()],
        ))
            ->hasAttached($deals)
            ->create();
    }
}
