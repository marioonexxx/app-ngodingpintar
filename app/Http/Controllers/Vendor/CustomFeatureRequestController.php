<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\CustomFeatureRequest;
use App\Support\Frontend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomFeatureRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomFeatureRequest::with(['user', 'transaction', 'product'])
            ->where('vendor_id', $request->user()->id)
            ->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return Frontend::render('Vendor/CustomFeatureRequests', [
            'requests' => $query->paginate(15),
            'statuses' => CustomFeatureRequest::STATUSES,
            'currentStatus' => $status,
        ]);
    }

    public function updateNotes(Request $request, CustomFeatureRequest $customFeatureRequest): RedirectResponse
    {
        abort_if($customFeatureRequest->vendor_id !== $request->user()->id, 403);

        $data = $request->validate([
            'vendor_notes' => ['required', 'string', 'max:2000'],
        ]);

        $customFeatureRequest->update($data);

        return back()->with('success', 'Catatan vendor berhasil disimpan.');
    }
}
