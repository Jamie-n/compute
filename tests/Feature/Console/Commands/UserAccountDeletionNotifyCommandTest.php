<?php

namespace Tests\Feature\Console\Commands;

use App\Mail\UserDeletionNotificationMail;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserAccountDeletionNotifyCommandTest extends TestCase
{
    /**
     * @dataProvider lastLoggedInDataProvider
     */
    public function test_user_is_correctly_notified_of_deletion_when($date, $shouldSend)
    {
        $toNotify = User::factory()->create(['last_login_at' => $date]);

        $return = $this->artisan('user:account-deletion-notify')->run();

        self::assertEquals(0, $return);

        if ($shouldSend) {
            Mail::assertSent(UserDeletionNotificationMail::class, 1);

            Mail::assertSent(UserDeletionNotificationMail::class, function (Mailable $mailable) use ($toNotify) {
                return $mailable->hasTo($toNotify->email);
            });
        } else
            Mail::assertNotSent(UserDeletionNotificationMail::class);
    }

    public function lastLoggedInDataProvider(): array
    {
        return [
            'logged in over 2 years ago' => [Date::now()->subYears(2), false],
            'logged in 1 year 11 months 15 days ago' => [Date::now()->subYear()->subMonths(11)->subDays(15), false],
            'logged in 1 year 11 months exactly' => [Date::now()->subYear()->subMonths(11), true],
            'logged in 1 year 10 months 30 days ago' => [Date::now()->subYear()->subMonths(10)->subDays(30), false],
            'logged in 1 year ago' => [Date::now()->subYear(), false],
            'logged in 30 days ago' => [Date::now()->subDays(30), false]
        ];
    }
}
