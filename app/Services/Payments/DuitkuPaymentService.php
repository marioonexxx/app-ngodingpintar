<?php

namespace App\Services\Payments;

use App\Models\Transaction;
use Illuminate\Http\Request;

class DuitkuPaymentService implements PaymentGatewayInterface
{
    public function createPayment(Transaction $transaction): array
    {
        return [
            'gateway' => 'duitku',
            'invoice_number' => $transaction->invoice_number,
            'status' => 'pending',
            'payment_url' => null,
        ];
    }

    public function handleWebhook(Request $request): array
    {
        return [
            'gateway' => 'duitku',
            'reference' => $request->input('merchantOrderId'),
            'payment_status' => $request->input('resultCode') === '00' ? 'paid' : 'unpaid',
            'raw' => $request->all(),
        ];
    }
}
