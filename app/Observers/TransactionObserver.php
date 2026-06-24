<?php

namespace App\Observers;

use App\Models\Transaction;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        if ($transaction->payment_status === Transaction::PAYMENT_PAID) {
            $this->processPartnerEarnings($transaction);
        }
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        if ($transaction->isDirty('payment_status') && $transaction->payment_status === Transaction::PAYMENT_PAID) {
            $this->processPartnerEarnings($transaction);
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }

    private function processPartnerEarnings(Transaction $transaction): void
    {
        // Prevent duplicate processing
        if (\App\Models\PartnerEarning::where('transaction_id', $transaction->id)->exists()) {
            return;
        }

        $transaction->loadMissing(['items.product']);
        $platformFeePercent = \App\Models\Setting::where('key', 'platform_fee_percent')->value('value') ?? 20;

        foreach ($transaction->items as $item) {
            if (!$item->product) {
                continue;
            }

            $vendorId = $item->product->vendor_id ?? $item->product->mentor_id;

            if ($vendorId) {
                $discountPercent = $transaction->discount_percent ?? 0;
                $finalAmount = $item->subtotal * (1 - ($discountPercent / 100));
                
                $platformFee = $finalAmount * ($platformFeePercent / 100);
                $netEarning = $finalAmount - $platformFee;

                \App\Models\PartnerEarning::create([
                    'user_id' => $vendorId,
                    'transaction_id' => $transaction->id,
                    'transaction_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'amount' => $finalAmount,
                    'platform_fee' => $platformFee,
                    'net_earning' => $netEarning,
                ]);

                \App\Models\User::where('id', $vendorId)->increment('balance', $netEarning);
            }
        }
    }
}
