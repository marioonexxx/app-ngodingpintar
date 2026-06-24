<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Mentor;
use App\Models\Setting;
use App\Support\Frontend;
use Illuminate\Http\Request;

class PartnerStatusController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $vendor = Vendor::where('user_id', $userId)->first();
        $mentor = Mentor::where('user_id', $userId)->first();

        $settings = Setting::whereIn('key', ['enable_vendor_registration', 'enable_mentor_registration'])->pluck('value', 'key');
        
        $enableVendor = filter_var($settings['enable_vendor_registration'] ?? 'true', FILTER_VALIDATE_BOOLEAN);
        $enableMentor = filter_var($settings['enable_mentor_registration'] ?? 'true', FILTER_VALIDATE_BOOLEAN);

        return Frontend::render('Member/PartnerStatus', [
            'vendor' => $vendor,
            'mentor' => $mentor,
            'enableVendor' => $enableVendor,
            'enableMentor' => $enableMentor,
        ]);
    }
}
