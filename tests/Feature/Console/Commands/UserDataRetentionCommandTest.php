<?php

namespace Tests\Feature\Console\Commands;

use App\Mail\UserDeletionNotificationMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;


class UserDataRetentionCommandTest extends TestCase
{
    /**
     * @dataProvider UserDeletedDataProvider
     */
    public function test_user_is_deleted($date, $shouldBeDeleted)
    {
        $toNotify = User::factory()->create(['last_login_at' => $date]);

        $return = $this->artisan('user:data-retention')->run();

        self::assertEquals(0, $return);

        if ($shouldBeDeleted)
            $this->assertDatabaseMissing(User::class, ['id' => $toNotify->id]);
        else
            $this->assertDatabaseHas(User::class, ['id' => $toNotify->id]);
    }

    public function UserDeletedDataProvider(): array
    {
        return [
            'logged in over 2 years ago' => [Date::now()->subYears(3), true],
            'logged in 2 years ago' => [Date::now()->subYears(2), false],
            'logged in 1 year 11 months 15 days ago' => [Date::now()->subYear()->subMonths(11)->subDays(15), false],
            'logged in 1 year 11 months exactly' => [Date::now()->subYear()->subMonths(11), false],
            'logged in 1 year 10 months 30 days ago' => [Date::now()->subYear()->subMonths(10)->subDays(30), false],
            'logged in 1 year ago' => [Date::now()->subYear(), false],
            'logged in 30 days ago' => [Date::now()->subDays(30), false]
        ];
    }

    public function test_user_orders_are_also_removed()
    {
        $user = User::factory()->create(['last_login_at' => Date::now()->subYears(3)]);
        $order = Order::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create();

        $order->products()->attach($product, ['unit_price' => 1.99, 'quantity' => 2]);

        $this->assertDatabaseHas(Order::class, ['user_id' => $user->id, 'id' => $order->id]);
        $this->assertDatabaseHas('order_product', ['product_id' => $product->id, 'order_id' => $order->id]);

        $return = $this->artisan('user:data-retention')->run();

        self::assertEquals(0, $return);

        $this->assertDatabaseMissing(User::class, ['id' => $user->id]);
        $this->assertDatabaseMissing(Order::class, ['user_id' => $user->id, 'id' => $order->id]);
        $this->assertDatabaseMissing('order_product', ['product_id' => $product->id, 'order_id' => $order->id]);
    }
}
