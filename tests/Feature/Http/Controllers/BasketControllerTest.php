<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Support\Cart\CartManager;
use Tests\TestCase;

class BasketControllerTest extends TestCase
{
    public function test_can_show_basket_with_no_items()
    {
        $this->get(route('basket.index'))
            ->assertSuccessful()
            ->assertViewIs('basket.index')->assertSee('You Have No Items In Your Basket');
    }

    public function test_can_show_basket_with_items()
    {
        $product = Product::factory()->stock(10)->create();

        CartManager::addToCart($product);

        $this->get(route('basket.index'))
            ->assertSuccessful()
            ->assertViewIs('basket.index')
            ->assertSee($product->name)
            ->assertSee('Sign In');
    }

    public function test_can_show_basket_and_see_checkout_button_when_signed_in()
    {
        $user = User::factory()->create();
        $this->be($user);

        $product = Product::factory()->stock(10)->create();

        CartManager::addToCart($product);

        $this->get(route('basket.index'))
            ->assertSuccessful()
            ->assertViewIs('basket.index')
            ->assertSee('Proceed To Checkout')
            ->assertSee('Order Total:');
    }
}
