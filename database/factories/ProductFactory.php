<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'slug' => $this->faker->unique()->slug(2),
            'name' => $this->faker->company(),
            'brand_id' => Brand::factory(),
            'description' => $this->faker->text(),
            'image' => null,
            'price' => $this->faker->randomFloat(2, 0, 3000),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function outOfStock(): ProductFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'stock_quantity' => 0
            ];
        });
    }

    public function stock($quantity): ProductFactory
    {
        return $this->state(function (array $attributes) use ($quantity) {
            return [
                'stock_quantity' => $quantity
            ];
        });
    }

    public function discount($discount): ProductFactory
    {
        return $this->state(function (array $attributes) use ($discount) {
            return [
                'discount_percentage' => $discount
            ];
        });
    }

    public function price($price): ProductFactory
    {
        return $this->state(function (array $attributes) use ($price) {
            return [
                'price' => $price
            ];
        });
    }
}

