<?php

namespace App\Services\Payments;

use App\Models\Transaction;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    public function createPayment(Transaction $transaction): array;

    public function handleWebhook(Request $request): array;
}
