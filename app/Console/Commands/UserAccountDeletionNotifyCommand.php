<?php

namespace App\Console\Commands;

use App\Actions\User\SendUserDeletionNotification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;

class UserAccountDeletionNotifyCommand extends Command
{
    protected $signature = 'user:account-deletion-notify';

    protected $description = "Notify users who's accounts will be removed in 1 month";

    public function handle()
    {
        $lowerBound = Date::now()->subYear()->subMonths(11)->startOfDay();
        $upperBound = Date::now()->subYear()->subMonths(11)->endOfDay();

        $toNotify = User::whereBetween('last_login_at', [$lowerBound, $upperBound])->get();

        $toNotify->each(function (User $user) {
            app()->make(SendUserDeletionNotification::class)->handle($user);
        });

        return 0;
    }
}
