<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->macProcessor(),
            'slug' => $this->faker->unique()->slug(2),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
