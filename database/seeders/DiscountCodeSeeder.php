<?php

namespace Database\Seeders;

use App\Models\DiscountCode;
use Illuminate\Database\Seeder;

class DiscountCodeSeeder extends Seeder
{
    public function run()
    {
        $codes = collect([
            ['code' => 'FREE', 'discount_percentage' => 100, 'code_active_start' => now()->subDay(), 'code_active_end' => now()->addYear()],
            ['code' => 'INACTIVE', 'discount_percentage' => 100, 'code_active_start' => now()->subDays(2), 'code_active_end' => now()->subDay()],
            ['code' => 'DISCOUNT10', 'discount_percentage' => 10, 'code_active_start' => now()->subDay(), 'code_active_end' => now()->addYear()],
        ]);

        $codes->each(function (array $code) {
            DiscountCode::factory()->create($code);
        });
    }
}
