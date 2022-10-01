<?php

namespace Database\Seeders;

use App\Models\DeliveryType;
use Illuminate\Database\Seeder;

class DeliveryTypeSeeder extends Seeder
{
    public function run()
    {
        $delivery = collect(['Standard Delivery' => 2.99, 'Express Delivery' => 5.99, 'Next Day Delivery (Order Before 3 PM)' => 8.99]);

        $delivery->each(function ($price, $deliveryType) {
            DeliveryType::create(['name' => $deliveryType, 'price' => $price]);
        });
    }
}
