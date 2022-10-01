<?php

namespace Database\Factories;


use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email_address' => $this->faker->unique()->safeEmail(),
            'phone_number' => $this->faker->phoneNumber(),
            'address_line_1' => $this->faker->streetAddress(),
            'address_line_2' => random_int(0, 1) ? $this->faker->streetAddress() : '',
            'city' => $this->faker->city(),
            'county' => $this->faker->country(),
            'postcode' => $this->faker->lexify('??? ???'),
        ];
    }
}
