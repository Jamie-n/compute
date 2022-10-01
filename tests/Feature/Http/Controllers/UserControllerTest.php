<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Tests\TestCase;

class UserControllerTest extends TestCase
{

    public function test_user_can_view_their_own_account()
    {
        $user = User::factory()->create();

        $this->be($user);

        $this->get(route('user.show', $user))->assertSuccessful();
    }

    public function test_user_cannot_view_other_accounts()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $this->be($user);

        $this->get(route('user.show', $user2))->assertForbidden();


    }
}
