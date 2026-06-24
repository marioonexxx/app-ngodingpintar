<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (blank(config('services.google.client_id')) || blank(config('services.google.client_secret'))) {
            return redirect()->route('login')->with('error', 'Google OAuth belum dikonfigurasi di .env.');
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable) {
            return redirect()->route('login')->with('error', 'Login Google gagal. Silakan coba lagi.');
        }

        $email = $googleUser->getEmail();

        if (! $email) {
            return redirect()->route('login')->with('error', 'Akun Google tidak memiliki email valid.');
        }

        $user = DB::transaction(function () use ($googleUser, $email): User {
            $user = User::query()->where('email', $email)->lockForUpdate()->first();

            if ($user) {
                $user->forceFill([
                    'google_id' => $user->google_id ?: $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar() ?: $user->avatar,
                    'email_verified_at' => $user->email_verified_at ?: now(),
                ])->save();

                return $user;
            }

            return User::create([
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'NgodingPintar Member',
                'email' => $email,
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => Str::random(40),
                'role' => User::ROLE_MEMBER,
                'email_verified_at' => now(),
            ]);
        });

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        // Process pending project request if exists
        if (request()->session()->has('pending_project_request')) {
            $data = request()->session()->pull('pending_project_request');
            \App\Models\ProjectRequest::create([
                'user_id' => $user->id,
                'title' => $data['title'],
                'category' => $data['category'],
                'budget' => $data['budget'],
                'deadline_date' => $data['deadline_date'],
                'whatsapp' => $data['whatsapp'],
                'description' => $data['description'],
                'attachment' => $data['attachment'] ?? null,
                'status' => \App\Models\ProjectRequest::STATUS_PENDING_ADMIN,
            ]);
        }

        return redirect()->intended($user->isAdmin() ? route('admin.dashboard') : route('member.dashboard'));
    }
}
