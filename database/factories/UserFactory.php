<?php

namespace Database\Factories;

use App\Models\User;
use App\Support\Enums\UserRoles;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'slug' => Str::slug(fake()->unique()->name),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => fake()->asciify('********************'), // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }


    /**
     * Assign the created user a system admin role
     * @return UserFactory|m.\Database\Factories\UserFactory.afterCreating
     */
    public function systemAdmin()
    {
        return $this->afterCreating(function(User $user){
            $user->assignRole(UserRoles::SYSTEM_ADMIN->value);
        });
    }

    /**
     * Assign the created user a product admin role
     * @return UserFactory|m.\Database\Factories\UserFactory.afterCreating
     */
    public function productAdmin()
    {
        return $this->afterCreating(function(User $user){
            $user->assignRole(UserRoles::PRODUCT_ADMIN->value);
        });
    }

    /**
     * Assign the created user a warehouse admin role
     * @return UserFactory|m.\Database\Factories\UserFactory.afterCreating
     */
    public function warehouseAdmin()
    {
        return $this->afterCreating(function(User $user){
            $user->assignRole(UserRoles::WAREHOUSE_OPERATIVE->value);
        });
    }
}
