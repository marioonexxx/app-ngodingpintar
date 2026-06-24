<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassProduct extends Model
{
    protected $fillable = [
        'product_id',
        'schedule_type',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'recurring_days',
        'platform',
        'meeting_link',
        'max_participants',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'recurring_days' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
