<?php

namespace App\Services\Payments;

use App\Models\Transaction;
use Illuminate\Http\Request;

class DokuPaymentService implements PaymentGatewayInterface
{
    public function createPayment(Transaction $transaction): array
    {
        return [
            'gateway' => 'doku',
            'invoice_number' => $transaction->invoice_number,
            'status' => 'pending',
            'payment_url' => null,
        ];
    }

    public function handleWebhook(Request $request): array
    {
        return [
            'gateway' => 'doku',
            'reference' => $request->input('invoice_number') ?? $request->input('order.invoice_number'),
            'payment_status' => $request->input('payment_status', 'unpaid'),
            'raw' => $request->all(),
        ];
    }
}
