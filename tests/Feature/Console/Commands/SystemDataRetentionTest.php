<?php

namespace Tests\Feature\Console\Commands;

use App\Models\Brand;
use App\Models\DeliveryType;
use App\Models\DiscountCode;
use App\Models\Product;
use Tests\TestCase;

class SystemDataRetentionTest extends TestCase
{
    public function test_products_are_correctly_deleted_after_cutoff()
    {
        $product = Product::factory()->create(['deleted_at' => now()->subYears(4)]);

        $this->artisan('system:data-retention');

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_brand_is_soft_deleted()
    {
        $brand = Brand::factory()->create(['deleted_at' => now()->subYears(4)]);

        $this->artisan('system:data-retention');

        $this->assertSoftDeleted('brands', ['id' => $brand->id]);
    }

    public function test_delivery_type_is_deleted()
    {
        $deliveryType = DeliveryType::factory()->create(['deleted_at' => now()->subYears(4)]);

        $this->artisan('system:data-retention');

        $this->assertSoftDeleted('delivery_types', ['id' => $deliveryType->id]);
    }


    public function test_discount_code_deleted()
    {
        $code = DiscountCode::factory()->create(['deleted_at' => now()->subYears(4)]);

        $this->artisan('system:data-retention');

        $this->assertSoftDeleted('discount_codes', ['id' => $code->id]);
    }
}
