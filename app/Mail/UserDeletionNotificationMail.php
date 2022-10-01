<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;

class UserDeletionNotificationMail extends Mailable
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;

        $this->subject = 'Important: Your account is about to be deactivated';
    }

    public function build(): UserDeletionNotificationMail
    {
        return $this->markdown('emails.user-deletion-notification');
    }
}
