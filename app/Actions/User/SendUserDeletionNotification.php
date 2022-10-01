<?php

namespace App\Actions\User;

use App\Mail\UserDeletionNotificationMail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendUserDeletionNotification
{
    public function handle(User $user)
    {

        try {
            Mail::to($user->email)->send(new UserDeletionNotificationMail($user));
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
