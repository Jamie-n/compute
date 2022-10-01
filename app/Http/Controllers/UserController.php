<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class UserController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        $account = auth()->user();
        return view('user.index')->with('account', $account);
    }
}
