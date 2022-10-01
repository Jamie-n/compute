<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\DeliveryType;
use App\Models\Order;
use App\Models\User;
use App\Support\States\Delivered;
use App\Support\States\Packing;
use App\Support\States\Processing;
use App\Support\States\Shipped;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'reference_number' => $this->faker->uuid(),
            'paypal_transaction_id' => Str::random(20),
            'user_id' => User::factory(),
            'order_total' => fake()->randomNumber(1, 1000),
            'delivery_type_id' => DeliveryType::all()->random()->id,
            'additional_delivery_info' => $this->faker->text(100),
            'delivery_address_id' => Address::factory()->create()->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    /**
     * Set an order as processing
     * @return OrderFactory
     */
    public function processing(): OrderFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Processing::class
            ];
        });
    }

    /**
     * Set an order as shipped
     * @return OrderFactory
     */
    public function shipped(): OrderFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Shipped::class
            ];
        });
    }

    /**
     * Set an order as packing
     * @return OrderFactory
     */
    public function packing(): OrderFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Packing::class
            ];
        });
    }

    /**
     * Set an order as packing
     * @return OrderFactory
     */
    public function delivered(): OrderFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Delivered::class
            ];
        });
    }

    public function nextDay(): OrderFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'delivery_type_id' => DeliveryType::whereName('Next Day Delivery (Order Before 3 PM)')->first()->id
            ];
        });
    }

    public function expressDelivery(): OrderFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'delivery_type_id' => DeliveryType::whereName('Express Delivery')->first()->id
            ];
        });
    }

    public function standardDelivery(): OrderFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'delivery_type_id' => DeliveryType::whereName('Standard Delivery')->first()->id
            ];
        });
    }
}
