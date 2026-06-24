<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Support\Frontend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return Frontend::render('Member/Profile', [
            'user' => $request->user()->only(['name', 'email', 'phone', 'address']),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
        ]);

        $request->user()->update($data);

        return back()->with('success', 'Profile diperbarui.');
    }
}
