<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Frontend;
use Illuminate\Http\Request;

class PartnerSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::whereIn('key', ['enable_vendor_registration', 'enable_mentor_registration', 'platform_fee_percent'])->pluck('value', 'key');
        
        return Frontend::render('Admin/PartnerSettings', [
            'enableVendor' => filter_var($settings['enable_vendor_registration'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
            'enableMentor' => filter_var($settings['enable_mentor_registration'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
            'platformFeePercent' => (int) ($settings['platform_fee_percent'] ?? 20),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'enable_vendor_registration' => ['boolean'],
            'enable_mentor_registration' => ['boolean'],
            'platform_fee_percent' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        Setting::updateOrCreate(
            ['key' => 'enable_vendor_registration'],
            ['value' => $request->boolean('enable_vendor_registration') ? 'true' : 'false', 'type' => 'boolean']
        );

        Setting::updateOrCreate(
            ['key' => 'enable_mentor_registration'],
            ['value' => $request->boolean('enable_mentor_registration') ? 'true' : 'false', 'type' => 'boolean']
        );

        Setting::updateOrCreate(
            ['key' => 'platform_fee_percent'],
            ['value' => $request->integer('platform_fee_percent'), 'type' => 'integer']
        );

        return back(303)->with('success', 'Pengaturan partner berhasil disimpan.');
    }
}
