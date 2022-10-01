<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run()
    {
        $brands = collect([
            'Nvidia',
            'AMD',
            'Intel',
            'Sapphire',
            'EVGA',
            'ASUS',
            'Dell',
            'LG',
            'MSI',
            'Iiyama'
        ]);

        $brands->each(function ($brand) {
            Brand::create([
                'name' => $brand,
                'slug' => Str::slug($brand)
            ]);
        });
    }
}
