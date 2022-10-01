<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'slug' => $this->faker->unique()->slug(),
            'name' => $this->faker->name(),
            'order' => $this->faker->unique()->numberBetween(1, 500),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
