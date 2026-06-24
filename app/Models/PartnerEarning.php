<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerEarning extends Model
{
    //
    protected $fillable = [
        'user_id',
        'transaction_id',
        'transaction_item_id',
        'product_id',
        'amount',
        'platform_fee',
        'net_earning',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function transactionItem()
    {
        return $this->belongsTo(TransactionItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
