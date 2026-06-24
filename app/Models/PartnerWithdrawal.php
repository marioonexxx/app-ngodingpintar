<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerWithdrawal extends Model
{
    //
    protected $fillable = [
        'user_id',
        'amount',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'status',
        'proof_of_transfer',
        'rejection_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
