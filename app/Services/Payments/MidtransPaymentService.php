<?php

namespace App\Services\Payments;

use App\Models\Transaction;
use Illuminate\Http\Request;

class MidtransPaymentService implements PaymentGatewayInterface
{
    public function createPayment(Transaction $transaction): array
    {
        return [
            'gateway' => 'midtrans',
            'invoice_number' => $transaction->invoice_number,
            'status' => 'pending',
            'payment_url' => null,
        ];
    }

    public function handleWebhook(Request $request): array
    {
        return [
            'gateway' => 'midtrans',
            'reference' => $request->input('order_id'),
            'payment_status' => match ($request->input('transaction_status')) {
                'settlement', 'capture' => 'paid',
                'expire' => 'expired',
                default => 'unpaid',
            },
            'raw' => $request->all(),
        ];
    }
}
