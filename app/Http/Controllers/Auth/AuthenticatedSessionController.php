<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\Frontend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\ProjectRequest;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        $this->storeIntendedUrl(request());

        return Frontend::render('Auth/Login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();

        // Process pending project request if exists
        if ($request->session()->has('pending_project_request')) {
            $data = $request->session()->pull('pending_project_request');
            ProjectRequest::create([
                'user_id' => $user->id,
                'title' => $data['title'],
                'category' => $data['category'],
                'budget' => $data['budget'],
                'deadline_date' => $data['deadline_date'],
                'whatsapp' => $data['whatsapp'],
                'description' => $data['description'],
                'attachment' => $data['attachment'] ?? null,
                'status' => 'pending_admin',
            ]);
        }

        return redirect()->intended($user->isAdmin() ? route('admin.dashboard') : route('member.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function storeIntendedUrl(Request $request): void
    {
        $redirect = $request->query('redirect');

        if (! is_string($redirect) || $redirect === '') {
            return;
        }

        $path = parse_url($redirect, PHP_URL_PATH) ?: '/';
        $query = parse_url($redirect, PHP_URL_QUERY);

        if (! str_starts_with($path, '/') || str_starts_with($path, '//')) {
            return;
        }

        $request->session()->put('url.intended', url($path.($query ? "?{$query}" : '')));
    }
}
