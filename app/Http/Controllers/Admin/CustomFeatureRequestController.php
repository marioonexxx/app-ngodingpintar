<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomFeatureRequest;
use App\Models\Transaction;
use App\Support\Frontend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomFeatureRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomFeatureRequest::with(['user', 'transaction', 'product', 'vendor'])
            ->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('transaction', fn ($t) => $t->where('invoice_number', 'like', "%{$search}%"));
            });
        }

        return Frontend::render('Admin/CustomFeatureRequests', [
            'requests' => $query->paginate(15),
            'statuses' => CustomFeatureRequest::STATUSES,
            'currentStatus' => $status,
            'currentSearch' => $search,
        ]);
    }

    public function updateStatus(Request $request, CustomFeatureRequest $customFeatureRequest): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', CustomFeatureRequest::STATUSES)],
            'quoted_amount' => ['nullable', 'numeric', 'min:0'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $customFeatureRequest->update($data);

        return back()->with('success', 'Status request berhasil diperbarui.');
    }

    public function createInvoice(CustomFeatureRequest $customFeatureRequest): RedirectResponse
    {
        if ($customFeatureRequest->status !== CustomFeatureRequest::STATUS_QUOTED || ! $customFeatureRequest->quoted_amount) {
            return back()->with('error', 'Request harus berstatus "quoted" dengan jumlah penawaran terisi.');
        }

        $transaction = Transaction::create([
            'user_id' => $customFeatureRequest->user_id,
            'invoice_number' => 'MDV-CFR-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
            'payment_gateway' => 'manual',
            'subtotal' => $customFeatureRequest->quoted_amount,
            'addon_total' => 0,
            'discount_total' => 0,
            'total' => $customFeatureRequest->quoted_amount,
            'status' => Transaction::STATUS_PENDING,
            'payment_status' => Transaction::PAYMENT_UNPAID,
        ]);

        $customFeatureRequest->update([
            'status' => CustomFeatureRequest::STATUS_APPROVED,
            'admin_notes' => ($customFeatureRequest->admin_notes ? $customFeatureRequest->admin_notes."\n" : '')
                .'Invoice '.$transaction->invoice_number.' dibuat pada '.now()->format('d M Y H:i'),
        ]);

        return back()->with('success', "Invoice {$transaction->invoice_number} berhasil dibuat.");
    }
}
