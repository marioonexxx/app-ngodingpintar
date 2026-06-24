<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentLog;
use App\Models\Transaction;
use App\Services\Payments\DokuPaymentService;
use App\Services\Payments\DuitkuPaymentService;
use App\Services\Payments\MidtransPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function __invoke(Request $request, string $gateway): JsonResponse
    {
        $service = match ($gateway) {
            'doku' => app(DokuPaymentService::class),
            'midtrans' => app(MidtransPaymentService::class),
            'duitku' => app(DuitkuPaymentService::class),
            default => abort(404),
        };

        $result = $service->handleWebhook($request);
        $transaction = Transaction::query()
            ->where('invoice_number', $result['reference'])
            ->orWhere('payment_reference', $result['reference'])
            ->first();

        PaymentLog::create([
            'transaction_id' => $transaction?->id,
            'gateway' => $gateway,
            'event' => 'webhook',
            'status' => $result['payment_status'],
            'reference' => $result['reference'],
            'payload' => $result['raw'],
            'received_at' => now(),
        ]);

        if ($transaction && $result['payment_status'] === Transaction::PAYMENT_PAID) {
            $transaction->update([
                'payment_status' => Transaction::PAYMENT_PAID,
                'status' => Transaction::STATUS_PROCESSING,
                'paid_at' => $transaction->paid_at ?? now(),
            ]);
        }

        return response()->json(['ok' => true]);
    }
}
