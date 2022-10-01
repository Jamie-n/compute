<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Product;
use App\Support\Cart\CartManager;
use Tests\TestCase;

class ProductsControllerTest extends TestCase
{

    public function test_can_add_item_to_basket()
    {
        $product = Product::factory()->stock(10)->create();

        $this->post(route('basket.add-product', $product))
            ->assertRedirect();

        self::assertEquals(1, CartManager::itemCountInBasket());
    }

    public function test_redirects_after_adding_to_cart()
    {
        $product = Product::factory()->stock(10)->create();

        $this->post(route('basket.add-product', $product))
            ->assertRedirect();
    }

    public function test_reduces_number_of_products_in_cart()
    {
        $product = Product::factory()->stock(10)->create();

        $this->post(route('basket.add-product', $product))
            ->assertRedirect();
        $this->post(route('basket.add-product', $product))
            ->assertRedirect();

        self::assertEquals(2, CartManager::itemCountInBasket());

        $this->post(route('basket.reduce-product', $product));

        self::assertEquals(1, CartManager::itemCountInBasket());
    }

    public function test_can_remove_item_from_cart()
    {
        $product = Product::factory()->stock(10)->create();

        $this->post(route('basket.add-product', $product))
            ->assertRedirect();
        $this->post(route('basket.add-product', $product))
            ->assertRedirect();

        self::assertEquals(2, CartManager::itemCountInBasket());

        $this->post(route('basket.remove-product', $product));

        self::assertEquals(0, CartManager::itemCountInBasket());
    }

    public function test_can_view_product()
    {
        $product = Product::factory()->stock(1)->create();

        $this->get(route('product.show', $product))
            ->assertViewIs('product.show')
            ->assertSee($product->name)
            ->assertSee('In Stock')
            ->assertSuccessful();
    }

    public function test_can_view_product_where_brand_is_soft_deleted()
    {
        $product = Product::factory()->stock(1)->create();

        $product->brand()->delete();

        $this->get(route('product.show', $product))
            ->assertViewIs('product.show')
            ->assertSee($product->brand->name)
            ->assertSuccessful();
    }

    public function test_out_of_stock_is_shown_when_product_is_out_of_stock()
    {
        $product = Product::factory()->outOfStock()->create();

        $product->brand()->delete();

        $this->get(route('product.show', $product))
            ->assertViewIs('product.show')
            ->assertSee('Out of Stock')
            ->assertSuccessful();
    }

    public function test_discount_is_displayed_when_product_is_discounted()
    {
        $product = Product::factory()->discount(50)->price(10)->create();

        $this->get(route('product.show', $product))
            ->assertViewIs('product.show')
            ->assertSee('50%')
            ->assertSee('10')
            ->assertSee('5')
            ->assertSuccessful();
    }
}
