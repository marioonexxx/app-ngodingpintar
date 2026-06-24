<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\PartnerProfile;
use App\Support\Frontend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        return Frontend::render('Partner/Profile', [
            'partner' => $request->user()->partnerProfile,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'brand_name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'expertise_area' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['nullable', 'integer', 'min:0'],
            'certifications' => ['nullable', 'string'],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_account_name' => ['required', 'string', 'max:255'],
            'bank_account_number' => ['required', 'string', 'max:50'],
            'profile_picture_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $partnerProfile = $request->user()->partnerProfile;

        if ($request->hasFile('profile_picture_file')) {
            $directory = public_path('img/mentors');
            File::ensureDirectoryExists($directory);

            $file = $request->file('profile_picture_file');
            $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
            $file->move($directory, $filename);

            $validated['profile_picture'] = "img/mentors/{$filename}";
            
            if ($partnerProfile->profile_picture && File::exists(public_path($partnerProfile->profile_picture))) {
                File::delete(public_path($partnerProfile->profile_picture));
            }
        }

        unset($validated['profile_picture_file']);

        if (!$partnerProfile) {
            $validated['user_id'] = $request->user()->id;
            PartnerProfile::create($validated);
        } else {
            $partnerProfile->update($validated);
        }

        return back(303)->with('success', 'Profil dan informasi rekening berhasil diperbarui.');
    }
}
