<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PartnerProfile;
use App\Support\Frontend;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['partnerProfile'])
            ->whereHas('partnerProfile')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('partnerProfile', function ($pq) use ($search) {
                      $pq->where('brand_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('role_type')) {
            $roleType = $request->role_type;
            $query->whereHas('partnerProfile', function ($pq) use ($roleType) {
                if ($roleType === 'vendor') {
                    $pq->whereNotNull('vendor_status')->whereNull('mentor_status');
                } elseif ($roleType === 'mentor') {
                    $pq->whereNotNull('mentor_status')->whereNull('vendor_status');
                } elseif ($roleType === 'both') {
                    $pq->whereNotNull('vendor_status')->whereNotNull('mentor_status');
                }
            });
        }

        return Frontend::render('Admin/Partners', [
            'partners' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'role_type']),
        ]);
    }

    public function updateStatus(Request $request, PartnerProfile $partnerProfile)
    {
        $request->validate([
            'vendor_status' => ['nullable', 'string', 'in:pending,approved,rejected'],
            'mentor_status' => ['nullable', 'string', 'in:pending,approved,rejected'],
        ]);

        if ($request->has('vendor_status')) {
            $partnerProfile->vendor_status = $request->vendor_status;
        }

        if ($request->has('mentor_status')) {
            $partnerProfile->mentor_status = $request->mentor_status;
        }

        $partnerProfile->save();

        if ($partnerProfile->vendor_status === 'approved' || $partnerProfile->mentor_status === 'approved') {
            $partnerProfile->user->forceFill(['role' => User::ROLE_PARTNER])->save();
        } else {
            if ($partnerProfile->user->role === User::ROLE_PARTNER) {
                $partnerProfile->user->forceFill(['role' => User::ROLE_MEMBER])->save();
            }
        }

        return back(303)->with('success', 'Status partner berhasil diperbarui.');
    }

    public function bulkDeactivate(Request $request)
    {
        $request->validate([
            'partner_profile_ids' => ['required', 'array'],
            'partner_profile_ids.*' => ['exists:partner_profiles,id']
        ]);

        $profiles = PartnerProfile::whereIn('id', $request->partner_profile_ids)->get();

        foreach ($profiles as $profile) {
            if ($profile->vendor_status) $profile->vendor_status = 'rejected';
            if ($profile->mentor_status) $profile->mentor_status = 'rejected';
            $profile->save();
            
            if ($profile->user->role === User::ROLE_PARTNER) {
                $profile->user->forceFill(['role' => User::ROLE_MEMBER])->save();
            }
        }

        return back(303)->with('success', 'Partner yang dipilih berhasil dinonaktifkan.');
    }
}
