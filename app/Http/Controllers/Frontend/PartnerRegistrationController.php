<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\PartnerProfile;
use App\Support\Frontend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PartnerRegistrationController extends Controller
{
    public function index()
    {
        $settings = Setting::whereIn('key', ['enable_vendor_registration', 'enable_mentor_registration'])->pluck('value', 'key');
        
        $enableVendor = filter_var($settings['enable_vendor_registration'] ?? 'true', FILTER_VALIDATE_BOOLEAN);
        $enableMentor = filter_var($settings['enable_mentor_registration'] ?? 'true', FILTER_VALIDATE_BOOLEAN);

        return Frontend::render('Frontend/PartnerRegister/Index', [
            'enableVendor' => $enableVendor,
            'enableMentor' => $enableMentor,
        ]);
    }

    public function createVendor(Request $request)
    {
        $setting = Setting::where('key', 'enable_vendor_registration')->first();
        $enableVendor = filter_var($setting->value ?? 'true', FILTER_VALIDATE_BOOLEAN);

        if (!$enableVendor) {
            return redirect()->route('partner.register.index')->with('error', 'Pendaftaran Vendor saat ini ditutup.');
        }

        $partner = PartnerProfile::where('user_id', $request->user()->id)->first();
        if ($partner && $partner->vendor_status !== 'rejected' && $partner->vendor_status !== null) {
            return redirect()->route('member.partner-status')->with('error', 'Anda sudah mendaftar sebagai vendor.');
        }

        return Frontend::render('Frontend/PartnerRegister/VendorForm', [
            'existingData' => collect($partner)->only([
                'brand_name', 'bio', 'whatsapp', 'github_url', 'website_url', 
                'portfolio_url', 'bank_name', 'bank_account_name', 'bank_account_number'
            ])->toArray()
        ]);
    }

    public function storeVendor(Request $request)
    {
        $setting = Setting::where('key', 'enable_vendor_registration')->first();
        if (!filter_var($setting->value ?? 'true', FILTER_VALIDATE_BOOLEAN)) {
            return redirect()->route('partner.register.index')->with('error', 'Pendaftaran Vendor saat ini ditutup.');
        }

        $data = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'developer_description' => ['nullable', 'string'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account_name' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:255'],
        ]);

        $partner = PartnerProfile::where('user_id', $request->user()->id)->first();

        if ($partner && $partner->vendor_status && $partner->vendor_status !== 'rejected') {
            return redirect()->route('member.partner-status')->with('error', 'Anda sudah mendaftar sebagai vendor sebelumnya.');
        }

        PartnerProfile::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'brand_name' => $partner->brand_name ?? $data['store_name'],
                'bio' => $partner->bio ?? $data['developer_description'],
                'whatsapp' => $data['whatsapp'] ?? $partner->whatsapp ?? null,
                'github_url' => $data['github_url'] ?? $partner->github_url ?? null,
                'website_url' => $data['website_url'] ?? $partner->website_url ?? null,
                'portfolio_url' => $data['portfolio_url'] ?? $partner->portfolio_url ?? null,
                'bank_name' => $data['bank_name'] ?? $partner->bank_name ?? null,
                'bank_account_name' => $data['bank_account_name'] ?? $partner->bank_account_name ?? null,
                'bank_account_number' => $data['bank_account_number'] ?? $partner->bank_account_number ?? null,
                'vendor_status' => 'pending',
                'vendor_rejection_reason' => null
            ]
        );

        return redirect()->route('member.partner-status')->with('success', 'Pendaftaran Vendor berhasil dikirim dan menunggu persetujuan Admin.');
    }

    public function createMentor(Request $request)
    {
        $setting = Setting::where('key', 'enable_mentor_registration')->first();
        $enableMentor = filter_var($setting->value ?? 'true', FILTER_VALIDATE_BOOLEAN);

        if (!$enableMentor) {
            return redirect()->route('partner.register.index')->with('error', 'Pendaftaran Mentor saat ini ditutup.');
        }

        $partner = PartnerProfile::where('user_id', $request->user()->id)->first();
        if ($partner && $partner->mentor_status !== 'rejected' && $partner->mentor_status !== null) {
            return redirect()->route('member.partner-status')->with('error', 'Anda sudah mendaftar sebagai mentor.');
        }

        return Frontend::render('Frontend/PartnerRegister/MentorForm', [
            'existingData' => collect($partner)->only([
                'brand_name', 'bio', 'whatsapp', 'linkedin_url', 'github_url', 
                'website_url', 'expertise_area', 'experience_years', 'certifications',
                'bank_name', 'bank_account_name', 'bank_account_number'
            ])->toArray()
        ]);
    }

    public function storeMentor(Request $request)
    {
        $setting = Setting::where('key', 'enable_mentor_registration')->first();
        if (!filter_var($setting->value ?? 'true', FILTER_VALIDATE_BOOLEAN)) {
            return redirect()->route('partner.register.index')->with('error', 'Pendaftaran Mentor saat ini ditutup.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'expertise_area' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['required', 'integer', 'min:0'],
            'certifications' => ['nullable', 'string'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account_name' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:255'],
            'profile_picture_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $partner = PartnerProfile::where('user_id', $request->user()->id)->first();

        if ($partner && $partner->mentor_status && $partner->mentor_status !== 'rejected') {
            return redirect()->route('member.partner-status')->with('error', 'Anda sudah mendaftar sebagai mentor sebelumnya.');
        }

        $updateData = [
            'brand_name' => $partner->brand_name ?? $data['name'],
            'bio' => $partner->bio ?? $data['bio'],
            'whatsapp' => $data['whatsapp'] ?? $partner->whatsapp ?? null,
            'linkedin_url' => $data['linkedin_url'] ?? $partner->linkedin_url ?? null,
            'github_url' => $data['github_url'] ?? $partner->github_url ?? null,
            'website_url' => $data['website_url'] ?? $partner->website_url ?? null,
            'expertise_area' => $data['expertise_area'],
            'experience_years' => $data['experience_years'],
            'certifications' => $data['certifications'] ?? null,
            'bank_name' => $data['bank_name'] ?? $partner->bank_name ?? null,
            'bank_account_name' => $data['bank_account_name'] ?? $partner->bank_account_name ?? null,
            'bank_account_number' => $data['bank_account_number'] ?? $partner->bank_account_number ?? null,
            'mentor_status' => 'pending',
            'mentor_rejection_reason' => null
        ];

        if ($request->hasFile('profile_picture_file')) {
            $directory = public_path('img/mentors');
            File::ensureDirectoryExists($directory);

            $file = $request->file('profile_picture_file');
            $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
            $file->move($directory, $filename);

            $updateData['profile_picture'] = "img/mentors/{$filename}";
        }

        PartnerProfile::updateOrCreate(
            ['user_id' => $request->user()->id],
            $updateData
        );

        return redirect()->route('member.partner-status')->with('success', 'Pendaftaran Mentor berhasil dikirim dan menunggu persetujuan Admin.');
    }
}

