<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefundRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RefundRequestController extends Controller
{
    public function index()
    {
        $refunds = RefundRequest::with(['user', 'transaction.projectRequest'])
            ->orderByRaw("FIELD(status, 'pending') DESC")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Admin/RefundRequests/Index', [
            'refunds' => $refunds,
        ]);
    }

    public function process(Request $request, RefundRequest $refundRequest)
    {
        abort_unless($refundRequest->status === 'pending', 400, 'Refund ini sudah diproses.');

        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string'],
            'transfer_proof' => ['required', 'image', 'max:2048'],
        ]);

        $proofPath = $request->file('transfer_proof')->store('refunds', 'public');

        DB::beginTransaction();
        try {
            $refundRequest->update([
                'status' => 'completed',
                'admin_notes' => $validated['admin_notes'],
                'transfer_proof' => $proofPath,
            ]);

            $transaction = $refundRequest->transaction;
            if ($transaction) {
                $transaction->update(['status' => Transaction::STATUS_CANCELLED]);

                $projectRequest = $transaction->projectRequest;
                if ($projectRequest && $projectRequest->status === \App\Models\ProjectRequest::STATUS_REFUND_PENDING) {
                    $projectRequest->update(['status' => \App\Models\ProjectRequest::STATUS_REFUNDED]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Refund berhasil diproses dan dana telah dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses refund.');
        }
    }
}
