<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Support\Cart\CartManager;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CheckoutControllerTest extends TestCase
{
    public function test_cannot_access_checkout_index_page_when_not_logged_in()
    {
        $this->withoutExceptionHandling();

        self::assertThrows(function () {
            $this->get(route('checkout.index'));
        }, AuthenticationException::class);
    }

    public function test_logged_in_user_can_access_checkout_index_page()
    {
        $this->be(User::factory()->create());
        $product = Product::factory()->stock(10)->create();

        CartManager::addToCart($product);

        $this->get(route('checkout.index'))->assertSuccessful();
    }

    public function test_authorised_user_is_redirected_when_accessing_checkout_page_with_empty_basket()
    {
        $this->be(User::factory()->create());

        $this->get(route('checkout.index'))
            ->assertRedirect(route('basket.index'));
    }

    public function test_validation_error_shown_when_checking_out_with_out_of_stock_items()
    {
        $this->withoutExceptionHandling();
        $this->be(User::factory()->create());

        $product = Product::factory()->outOfStock()->create();

        CartManager::addToCart($product);

        $this->assertThrows(function () {
            $this->get(route('checkout.index'))
                ->assertRedirect()
                ->assertSessionHasErrors();
        }, ValidationException::class);
    }
}
