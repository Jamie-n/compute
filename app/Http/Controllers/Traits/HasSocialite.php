<?php

namespace App\Http\Controllers\Traits;

use App\Models\User;
use App\Support\Enums\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

trait HasSocialite
{
    public function socialiteRedirect(Request $request)
    {
        if (!$request->provider)
            return redirect()->route('login');

        $provider = $request->provider;

        session()->put('provider', $provider);

        return Socialite::driver($provider)
            ->with(["prompt" => "select_account"])
            ->redirect();
    }

    public function socialiteAuthenticate(Request $request)
    {
        $provider = session()->pull('provider');

        $user = Socialite::driver($provider)->user();

        if ($this->hasEmailInDatabaseWithDifferentOauthId($user)) {
            session()->flash(Alert::DANGER->value, 'Your email address has already been registered with another provider');
            return redirect()->route('login');
        }

        $user = User::updateOrCreate(
            [
                'oauth_id' => $user->getId()
            ],
            [
                'name' => $user->getName(),
                'email' => $user->getEmail(),
            ],
        );

        Auth::login($user);

        return $this->authenticated($request, $user);
    }

    public function hasEmailInDatabaseWithDifferentOauthId($user)
    {
        return User::whereEmail($user->getEmail())
            ->where('oauth_id', '!=', $user->getId())
            ->exists();
    }
}
