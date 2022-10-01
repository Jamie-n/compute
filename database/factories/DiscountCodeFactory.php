<?php

namespace Database\Factories;

use App\Models\DiscountCode;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DiscountCodeFactory extends Factory
{
    protected $model = DiscountCode::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->asciify('******'),
            'discount_percentage' => $this->faker->randomNumber(),
            'code_active_start' => Carbon::now(),
            'code_active_end' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
