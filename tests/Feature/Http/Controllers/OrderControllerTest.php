<?php

namespace Tests\Feature\Http\Controllers;

use App\Mail\OrderInvoiceMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Support\Cart\CartManager;
use App\Support\Enums\Alert;
use App\Support\Enums\UserRoles;
use App\Support\PayPal\PaypalPaymentHandler;
use App\Support\States\Packing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Mockery\MockInterface;
use Tests\TestCase;


class OrderControllerTest extends TestCase
{
    public function test_can_access_order_index_page_when_authenticated()
    {
        $user = User::factory()->create();

        $this->be($user);

        $this->get(route('order.index'))->assertSuccessful();
    }

    public function test_cannot_access_order_index_page_when_unauthenticated()
    {
        $this->get(route('order.index'))->assertRedirect(route('login'));
    }

    public function test_can_access_edit_order_page_when_authenticated()
    {
        $user = User::factory()->create();

        $this->be($user);

        $order = Order::factory()->for($user)->create();

        $this->get(route('order.edit', $order))->assertSuccessful();
    }


    public function test_cannot_access_order_edit_page_when_unauthenticated()
    {
        $user = User::factory()->create();

        $order = Order::factory()->for($user)->create();

        $this->get(route('order.edit', $order))->assertRedirect(route('login'));
    }

    public function test_cannot_access_order_edit_page_for_other_users_orders()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $this->be($user2);

        $order = Order::factory()->for($user)->create();


        $this->assertThrows(function () use ($order) {
            $this->get(route('order.edit', $order));
        },
            AuthorizationException::class);
    }

    public function test_cannot_access_order_edit_page_when_order_has_reached_shipping_status()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->be($user);

        $order = Order::factory()->for($user)->create();
        $order->update(['status' => Packing::class]);

        $this->assertThrows(function () use ($order) {
            $this->get(route('order.edit', $order));
        }, AuthorizationException::class, 'You cannot edit an order which has begun shipping');

    }

    public function test_can_update_shipping_details()
    {
        $user = User::factory()->create();
        $this->be($user);

        $order = Order::factory()->for($user)->create();

        $inputs = [
            'name' => 'test',
            'email_address' => 'test@ex.com',
            'phone_number' => '1345',
            'address_line_1' => 'test',
            'address_line_2' => 'test',
            'city' => 'test',
            'county' => 'test',
            'postcode' => 'test',
        ];

        $this->patch(route('order.update', $order), $inputs)->assertRedirect(route('order.index'));

        $this->assertDatabaseHas('addresses', $inputs);
    }

    public function test_can_cancel_order()
    {
        $this->mock(PaypalPaymentHandler::class, function (MockInterface $mock) {
            $mock->shouldReceive('voidAuthorisedPayment')->once();
        });

        $user = User::factory()->create();
        $this->be($user);

        $order = Order::factory()->for($user)->create();

        $this->delete(route('order.destroy', $order))->assertRedirect(route('order.index'));

        $this->assertNull(Order::find($order->id));
    }

    public function test_cannot_cancel_order_which_has_begun_shipping()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->be($user);

        $order = Order::factory()->processing()->for($user)->create();
        $order->update(['status' => Packing::class]);

        $this->assertThrows(function () use ($order) {
            $this->delete(route('order.destroy', $order));
        }, AuthorizationException::class, 'You cannot edit an order which has begun shipping');
    }

    public function test_regular_user_can_only_access_order_show_page_for_own_order()
    {
        $user = User::factory()->create();

        $order = Order::factory()->create(['user_id' => $user]);

        $user2 = User::factory()->create();
        $this->be($user2);

        $this->get(route('order.show', $order))->assertForbidden();
    }

    public function test_system_admin_can_view_any_orders()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();

        $order = Order::factory()->create(['user_id' => $user]);

        $user2 = User::factory()->create();
        $user2->assignRole(UserRoles::SYSTEM_ADMIN->value);
        $this->be($user2);

        $this->get(route('order.show', $order))->assertSuccessful();
    }

    public function test_product_admin_cannot_view_any_orders()
    {
        $user = User::factory()->create();

        $order = Order::factory()->create(['user_id' => $user]);

        $user2 = User::factory()->create();
        $user2->assignRole(UserRoles::PRODUCT_ADMIN->value);
        $this->be($user2);

        $this->get(route('order.show', $order))->assertForbidden();
    }

    public function test_edit_order_button_is_hidden_when_order_is_not_processing()
    {
        $user = User::factory()->create();
        Order::factory()->packing()->create(['user_id' => $user->id]);

        $this->be($user);

        $this->get(route('order.index'))->assertDontSee('Edit');
    }

    public function test_can_see_edit_order_button_when_order_is_processing()
    {
        $user = User::factory()->create();
        Order::factory()->processing()->create(['user_id' => $user->id]);

        $this->be($user);

        $this->get(route('order.index'))->assertSee('Edit');
    }

    public function test_can_see_cancel_button_when_order_is_processing()
    {
        $user = User::factory()->create();
        Order::factory()->processing()->create(['user_id' => $user->id]);

        $this->be($user);

        $this->get(route('order.index'))->assertSee('Cancel');
    }

    public function test_cant_see_cancel_button_when_order_is_not_processing()
    {
        $user = User::factory()->create();
        Order::factory()->packing()->create(['user_id' => $user->id]);

        $this->be($user);

        $this->get(route('order.index'))->assertDontSee('Cancel');
    }
}
