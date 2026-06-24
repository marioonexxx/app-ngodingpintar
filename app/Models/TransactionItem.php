<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'transaction_id',
    'product_id',
    'class_batch_id',
    'product_name',
    'quantity',
    'unit_price',
    'subtotal',
    'download_count',
    'download_expires_at',
])]
class TransactionItem extends Model
{
    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'download_expires_at' => 'datetime',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function classBatch(): BelongsTo
    {
        return $this->belongsTo(ClassBatch::class);
    }
}
