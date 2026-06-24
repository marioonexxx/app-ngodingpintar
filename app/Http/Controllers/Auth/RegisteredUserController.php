<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Frontend;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use App\Models\ProjectRequest;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return Frontend::render('Auth/Register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => User::ROLE_MEMBER,
        ]);

        event(new Registered($user));
        Auth::login($user);

        // Process pending project request if exists
        if ($request->session()->has('pending_project_request')) {
            $pendingData = $request->session()->pull('pending_project_request');
            ProjectRequest::create([
                'user_id' => $user->id,
                'title' => $pendingData['title'],
                'category' => $pendingData['category'],
                'budget' => $pendingData['budget'],
                'deadline_date' => $pendingData['deadline_date'],
                'whatsapp' => $pendingData['whatsapp'],
                'description' => $pendingData['description'],
                'attachment' => $pendingData['attachment'] ?? null,
                'status' => 'pending_admin',
            ]);
            
            // Override redirect to member dashboard after registration if there's a request
            // but since they might need to verify email, we can let them go to intended or verification
            // Let's redirect them to intended URL instead of verification notice if intended is set
            return redirect()->intended(route('verification.notice'));
        }

        return redirect()->route('verification.notice');
    }
}
