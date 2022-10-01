<?php

namespace App\Listeners;

use Date;
use Illuminate\Auth\Events\Login;

class UserLoginListener
{
    public function handle(Login $event): void
    {
        $event->user->update(['last_login_at' => Date::now()]);
    }
}
