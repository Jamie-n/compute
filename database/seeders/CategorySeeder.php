<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = collect([
            'Desktop Computing',
            'Portable Computing',
            'Components',
            'Monitors',
            'Peripherals',
            'Deals'
        ]);

        $categories->each(fn($category, $order) => Category::create(['name' => $category, 'slug' => Str::slug($category), 'order' => $order]));
    }
}
