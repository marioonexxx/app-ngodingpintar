<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleOAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        abort_unless(class_exists(\Laravel\Socialite\Facades\Socialite::class), 501, 'Laravel Socialite belum terpasang.');

        return \Laravel\Socialite\Facades\Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        abort_unless(class_exists(\Laravel\Socialite\Facades\Socialite::class), 501, 'Laravel Socialite belum terpasang.');

        $googleUser = \Laravel\Socialite\Facades\Socialite::driver('google')->stateless()->user();

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'NgodingPintar Member',
                'google_id' => $googleUser->getId(),
                'avatar_url' => $googleUser->getAvatar(),
                'password' => Str::random(32),
                'role' => User::ROLE_MEMBER,
                'email_verified_at' => now(),
            ],
        );

        Auth::login($user, remember: true);

        return redirect()->route('member.dashboard');
    }
}
