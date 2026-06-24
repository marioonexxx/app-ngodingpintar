<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'transaction_id',
    'checkout_addon_id',
    'name',
    'type',
    'price',
    'notes',
])]
class TransactionAddon extends Model
{
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function checkoutAddon(): BelongsTo
    {
        return $this->belongsTo(CheckoutAddon::class);
    }
}
