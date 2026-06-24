<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Support\Frontend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionController extends Controller
{
    public function active(Request $request)
    {
        return Frontend::render('Member/ActiveTransactions', [
            'bankAccounts' => BankAccount::where('is_active', true)->orderBy('bank_name')->get(),
            'transactions' => $request->user()->transactions()
                ->with(['items.product', 'items.classBatch'])
                ->whereIn('status', [Transaction::STATUS_PENDING, Transaction::STATUS_PROCESSING])
                ->latest()
                ->paginate(10),
        ]);
    }

    public function history(Request $request)
    {
        return Frontend::render('Member/TransactionHistory', [
            'transactions' => $request->user()->transactions()
                ->with(['items.product', 'items.classBatch', 'testimonials', 'refundRequests', 'projectRequest'])
                ->where(function ($query): void {
                    $query->whereIn('status', [Transaction::STATUS_COMPLETED, Transaction::STATUS_CANCELLED])
                        ->orWhere('payment_status', Transaction::PAYMENT_PAID);
                })
                ->latest()
                ->paginate(10),
        ]);
    }

    public function download(Request $request, TransactionItem $transactionItem)
    {
        $transactionItem->load('transaction', 'product');
        abort_unless($transactionItem->transaction->user_id === $request->user()->id, 403);
        abort_unless($transactionItem->transaction->isPaid(), 403, 'Produk hanya bisa diunduh setelah pembayaran paid.');
        abort_unless($transactionItem->product?->file_path, 404);

        $transactionItem->increment('download_count');

        $filePath = $transactionItem->product->file_path;

        if (filter_var($filePath, FILTER_VALIDATE_URL)) {
            return redirect()->away($filePath);
        }

        return Storage::download($filePath);
    }

    public function uploadProof(Request $request, Transaction $transaction): RedirectResponse
    {
        abort_unless($transaction->user_id === $request->user()->id, 403);
        abort_unless($transaction->payment_gateway === 'manual_transfer', 400);
        abort_unless(
            in_array($transaction->payment_status, [
                Transaction::PAYMENT_UNPAID,
                Transaction::PAYMENT_VERIFYING,
            ], true),
            400
        );

        $request->validate([
            'payment_proof' => ['required', 'image', 'max:2048'], // max 2MB
        ]);

        if ($request->hasFile('payment_proof')) {
            $oldPath = $transaction->payment_proof;
            $path = $request->file('payment_proof')->store('payment-proofs', 'public');
            
            $transaction->update([
                'payment_proof' => $path,
                'payment_status' => Transaction::PAYMENT_VERIFYING,
            ]);

            if ($oldPath && $oldPath !== $path) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        return back()->with('success', 'Bukti pembayaran berhasil disimpan. Silakan tunggu verifikasi admin.');
    }

    public function viewProof(Request $request, Transaction $transaction): StreamedResponse
    {
        abort_unless($transaction->user_id === $request->user()->id, 403);
        abort_unless($transaction->payment_proof, 404);
        abort_unless(Storage::disk('public')->exists($transaction->payment_proof), 404);

        return Storage::disk('public')->response($transaction->payment_proof);
    }
}
