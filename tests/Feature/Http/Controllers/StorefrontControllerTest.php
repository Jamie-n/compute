<?php

namespace Tests\Feature\Http\Controllers;


use App\Models\Category;
use App\Models\Product;
use Tests\TestCase;


class StorefrontControllerTest extends TestCase
{
    public function test_can_view_storefront()
    {
        Product::factory(15)->create();

        $data = $this->get(route('storefront.index'))->assertViewIs('storefront.index')->viewData('products');

        self::assertEquals(Product::count(), $data->total());
    }

    public function test_pagination_length_follows_config_values()
    {
        Product::factory(15)->create();

        $data = $this->get(route('storefront.index'))->assertViewIs('storefront.index')->viewData('products');

        self::assertEquals(config('pagination.product_index_page_length'), $data->perPage());
    }

    public function test_shows_discount_when_viewing_a_product()
    {
        $product = Product::factory()->discount(50)->price(10)->create();

        $this->get(route('product.show', $product))
            ->assertViewIs('product.show')
            ->assertSee('50%')
            ->assertSee('10')
            ->assertSee('5')
            ->assertSuccessful();

    }

    public function test_shows_discount_when_viewing_a_product_on_category_page()
    {
        $product = Product::factory()->discount(50)->price(10)->create();
        $category = Category::factory()->create();

        $product->categories()->sync($category);

        $this->get(route('storefront.show', $category))
            ->assertViewIs('storefront.show')
            ->assertSee('50%')
            ->assertSee('10')
            ->assertSee('5')
            ->assertSuccessful();
    }
}
