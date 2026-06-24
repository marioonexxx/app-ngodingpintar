<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\PartnerWithdrawal;
use App\Support\Frontend;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = $user->withdrawals()->latest();
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $withdrawals = $query->paginate(15)->withQueryString();

        return Frontend::render('Partner/Withdrawals/Index', [
            'user' => $user,
            'withdrawals' => $withdrawals,
            'filters' => $request->only(['status']),
            'partner' => $user->partnerProfile,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $partnerProfile = $user->partnerProfile;

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:100000', 'multiple_of:10000'],
        ], [
            'amount.multiple_of' => 'Jumlah penarikan harus dalam kelipatan Rp 10.000.',
        ]);

        if (!$partnerProfile || !$partnerProfile->bank_name || !$partnerProfile->bank_account_name || !$partnerProfile->bank_account_number) {
            throw ValidationException::withMessages([
                'amount' => 'Anda harus melengkapi informasi rekening bank di menu Profil terlebih dahulu.',
            ]);
        }

        if ($user->balance < $validated['amount']) {
            throw ValidationException::withMessages([
                'amount' => 'Saldo Anda tidak mencukupi untuk penarikan ini.',
            ]);
        }
        
        // Prevent multiple pending requests
        $hasPending = $user->withdrawals()->where('status', 'waiting')->exists();
        if ($hasPending) {
             throw ValidationException::withMessages([
                'amount' => 'Anda masih memiliki pengajuan penarikan yang sedang diproses.',
            ]);
        }

        PartnerWithdrawal::create([
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'bank_name' => $partnerProfile->bank_name,
            'bank_account_name' => $partnerProfile->bank_account_name,
            'bank_account_number' => $partnerProfile->bank_account_number,
            'status' => 'waiting',
        ]);

        $user->decrement('balance', $validated['amount']);

        return back(303)->with('success', 'Pengajuan penarikan berhasil dibuat. Mohon menunggu proses verifikasi admin.');
    }
}
