<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartnerWithdrawal;
use App\Support\Frontend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PartnerWithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $query = PartnerWithdrawal::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $withdrawals = $query->paginate(15)->withQueryString();

        return Frontend::render('Admin/PartnerWithdrawals/Index', [
            'withdrawals' => $withdrawals,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function update(Request $request, PartnerWithdrawal $withdrawal)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:paid,rejected'],
            'proof_of_transfer_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'required_if:status,paid'],
            'rejection_reason' => ['nullable', 'string', 'required_if:status,rejected'],
        ]);

        if ($validated['status'] === 'paid' && $request->hasFile('proof_of_transfer_file')) {
            $directory = public_path('img/withdrawals');
            File::ensureDirectoryExists($directory);

            $file = $request->file('proof_of_transfer_file');
            $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
            $file->move($directory, $filename);

            $withdrawal->update([
                'status' => 'paid',
                'proof_of_transfer' => "img/withdrawals/{$filename}",
            ]);
        } elseif ($validated['status'] === 'rejected') {
            $withdrawal->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
            ]);
            
            // Refund balance to user
            $withdrawal->user->increment('balance', $withdrawal->amount);
        }

        return back(303)->with('success', 'Status pencairan berhasil diperbarui.');
    }
}
