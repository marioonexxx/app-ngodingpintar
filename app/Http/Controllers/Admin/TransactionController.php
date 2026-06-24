<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Support\Frontend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'items'])->latest();

        if ($request->filled('status') && $request->status !== 'Semua Order') {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status') && $request->payment_status !== 'Semua Payment') {
            if ($request->payment_status === 'verifying') {
                $query->where('payment_status', Transaction::PAYMENT_VERIFYING)
                      ->where('payment_gateway', 'manual_transfer');
            } else {
                $query->where('payment_status', $request->payment_status);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('email', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = $request->get('per_page', 15);
        if ($perPage === 'all') {
            $perPage = $query->count();
        }

        return Frontend::render('Admin/Transactions', [
            'transactions' => $query->paginate($perPage)->withQueryString(),
            'filters' => $request->only(['status', 'payment_status', 'search', 'per_page']),
        ]);
    }

    public function updateStatus(Request $request, Transaction $transaction): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,processing,completed,cancelled'],
            'payment_status' => ['required', 'in:unpaid,paid,expired'],
        ]);

        $transaction->update([
            ...$data,
            'paid_at' => $data['payment_status'] === Transaction::PAYMENT_PAID ? ($transaction->paid_at ?? now()) : $transaction->paid_at,
            'completed_at' => $data['status'] === Transaction::STATUS_COMPLETED ? ($transaction->completed_at ?? now()) : $transaction->completed_at,
        ]);

        return back()->with('success', 'Status transaksi diperbarui.');
    }

    public function verifyPayment(Request $request, Transaction $transaction): RedirectResponse
    {
        if (
            $transaction->payment_gateway !== 'manual_transfer'
            || $transaction->payment_status !== Transaction::PAYMENT_VERIFYING
        ) {
            return back()->with('error', 'Transaksi ini tidak lagi menunggu verifikasi.');
        }

        $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'rejection_reason' => ['nullable', 'required_if:action,reject', 'string', 'max:500'],
        ]);

        if ($request->action === 'approve') {
            $transaction->update([
                'payment_status' => Transaction::PAYMENT_PAID,
                'status' => Transaction::STATUS_COMPLETED,
                'paid_at' => now(),
                'rejection_reason' => null,
            ]);
            
            $transaction->user->notify(new \App\Notifications\PaymentVerificationNotification($transaction, 'approve'));
            
            return back(303)->with('success', 'Pembayaran berhasil divalidasi dan transaksi selesai.');
        }

        if ($request->action === 'reject') {
            $transaction->update([
                'payment_status' => Transaction::PAYMENT_REJECTED,
                'status' => Transaction::STATUS_CANCELLED,
                'rejection_reason' => $request->rejection_reason,
            ]);
            
            $transaction->user->notify(new \App\Notifications\PaymentVerificationNotification($transaction, 'reject'));
            
            return back(303)->with('success', 'Pembayaran ditolak dan pesanan dibatalkan.');
        }

        return back(303);
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $transaction->delete();
        return back()->with('success', 'Transaksi berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:transactions,id'],
        ]);

        Transaction::whereIn('id', $request->ids)->delete();

        return back()->with('success', count($request->ids) . ' transaksi berhasil dihapus.');
    }

    public function bulkVerify(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:transactions,id'],
        ]);

        $transactions = Transaction::whereIn('id', $request->ids)
            ->where('payment_gateway', 'manual_transfer')
            ->where('payment_status', Transaction::PAYMENT_VERIFYING)
            ->get();

        foreach ($transactions as $transaction) {
            $transaction->update([
                'payment_status' => Transaction::PAYMENT_PAID,
                'status' => Transaction::STATUS_COMPLETED,
                'paid_at' => now(),
                'rejection_reason' => null,
            ]);
            
            $transaction->user->notify(new \App\Notifications\PaymentVerificationNotification($transaction, 'approve'));
        }

        return back()->with('success', count($transactions) . ' pembayaran berhasil divalidasi.');
    }
}
