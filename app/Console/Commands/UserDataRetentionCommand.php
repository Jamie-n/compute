<?php

namespace App\Console\Commands;

use App\Actions\User\SendUserDeletionNotification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;

class UserDataRetentionCommand extends Command
{
    protected $signature = 'user:data-retention';

    protected $description = 'Delete users which have not logged into the service for 2 or more years';

    public function handle()
    {
        $toDelete = User::whereDate('last_login_at', '<', Date::now()->subYears(2));

        $toDelete->forceDelete();

        return 0;
    }
}
