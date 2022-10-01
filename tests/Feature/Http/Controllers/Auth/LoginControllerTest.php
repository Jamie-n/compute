<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Support\Enums\Alert;
use Date;
use Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User;
use App\Models\User as UserModel;
use Mockery;
use Tests\TestCase;
use Laravel\Socialite\Contracts\Provider;

class LoginControllerTest extends TestCase
{
    /**
     * Mocking code to create a faked oauth callback response.
     * This was adapted from a post by SUPAD on a Laracast thread here:
     * https://laracasts.com/discuss/channels/testing/testing-socialite?page=1&replyId=285539
     * @return void
     */
    public function test_oauth_user_from_mocked_callback_is_turned_into_database_user()
    {
        $user = UserModel::factory()->make();
        $oauthId = 1234567890;

        $socialiteUser = Mockery::mock(User::class);

        $socialiteUser
            ->shouldReceive('getId')
            ->andReturn($oauthId)
            ->shouldReceive('getEmail')
            ->andReturn($user->email)
            ->shouldReceive('getName')
            ->andReturn($user->name);

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with(config('services.google.name'))
            ->andReturn($provider);

        session()->put('provider', config('services.google.name'));

        $this
            ->get(route('socialite.authenticate-callback'))
            ->assertRedirect(route('user.show', Str::slug($user->name)));

        $this->assertDatabaseHas('users', [
            'oauth_id' => $oauthId,
            'name' => $user->name,
            'email' => $user->email,
        ]);

        self::assertEquals($oauthId, auth()->user()->oauth_id);
    }

    /**
     * Mocking code to create a faked oauth callback response.
     * This was adapted from a post by SUPAD on a Laracast thread here:
     * https://laracasts.com/discuss/channels/testing/testing-socialite?page=1&replyId=285539
     * @return void
     */
    public function test_cannot_login_using_a_different_provider_once_email_has_been_assigned()
    {
        $user = UserModel::factory()->create(['oauth_id' => 13579]);

        $oauthId = 1234567890;

        $socialiteUser = Mockery::mock(User::class);

        $socialiteUser
            ->shouldReceive('getId')
            ->andReturn($oauthId)
            ->shouldReceive('getEmail')
            ->andReturn($user->email)
            ->shouldReceive('getName')
            ->andReturn($user->name);

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with(config('services.google.name'))
            ->andReturn($provider);

        session()->put('provider', config('services.google.name'));

        $this
            ->get(route('socialite.authenticate-callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHas(Alert::DANGER->value, 'Your email address has already been registered with another provider');
    }

    public function test_user_last_login_at_date_is_correctly_updated_when_logging_in()
    {
        $lastLogin = Date::now()->subYear();
        $user = UserModel::factory()->create(['last_login_at' => $lastLogin, 'password' => Hash::make('password')]);

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])->assertRedirect();

        $user->refresh();

        self::assertNotEquals($lastLogin->format('d/m/y'), $user->last_login_at->format('d/m/y'));
    }
}
