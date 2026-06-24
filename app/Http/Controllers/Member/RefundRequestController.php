<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\RefundRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RefundRequestController extends Controller
{
    public function create(Transaction $transaction)
    {
        abort_unless($transaction->user_id === request()->user()->id, 403);
        
        // Cek apakah transaksi berhak di-refund. Misalnya statusnya cancelled atau ada project yg refund_pending
        // Dalam konteks ini, kita asumsikan transaction yang boleh di-refund adalah yang payment_status = 'paid' 
        // tapi project-nya 'refund_pending' atau transaksinya dibatalkan oleh admin
        $isEligible = false;
        
        // Cek project request
        $projectRequest = \App\Models\ProjectRequest::where('transaction_id', $transaction->id)->first();
        if ($projectRequest && $projectRequest->status === \App\Models\ProjectRequest::STATUS_REFUND_PENDING) {
            $isEligible = true;
        }
        
        // Cek transaction status
        if ($transaction->status === Transaction::STATUS_CANCELLED && $transaction->isPaid()) {
            $isEligible = true;
        }

        abort_unless($isEligible, 403, 'Transaksi ini tidak memenuhi syarat untuk direfund.');

        // Cek apakah sudah pernah mengajukan
        $existing = RefundRequest::where('transaction_id', $transaction->id)->first();
        abort_if($existing, 403, 'Anda sudah mengajukan refund untuk transaksi ini.');

        return Inertia::render('Member/Transactions/Refund', [
            'transaction' => $transaction,
        ]);
    }

    public function store(Request $request, Transaction $transaction)
    {
        abort_unless($transaction->user_id === $request->user()->id, 403);
        
        $existing = RefundRequest::where('transaction_id', $transaction->id)->first();
        abort_if($existing, 403, 'Anda sudah mengajukan refund untuk transaksi ini.');

        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:100'],
        ]);

        RefundRequest::create([
            'transaction_id' => $transaction->id,
            'user_id' => $request->user()->id,
            'bank_name' => $validated['bank_name'],
            'account_name' => $validated['account_name'],
            'account_number' => $validated['account_number'],
            'amount' => $transaction->total,
            'status' => RefundRequest::STATUS_PENDING,
        ]);

        return redirect()->route('member.transactions.history')->with('success', 'Pengajuan refund berhasil dikirim. Admin akan segera memproses pengembalian dana ke rekening Anda.');
    }
}
