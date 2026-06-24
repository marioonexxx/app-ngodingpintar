<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'cart_id',
    'promo_code_id',
    'invoice_number',
    'payment_gateway',
    'payment_proof',
    'payment_reference',
    'payment_url',
    'promo_code',
    'discount_percent',
    'subtotal',
    'discount_total',
    'addon_total',
    'tax_total',
    'total',
    'status',
    'payment_status',
    'paid_at',
    'completed_at',
])]
class Transaction extends Model
{
    use SoftDeletes;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_UNPAID = 'unpaid';

    public const PAYMENT_VERIFYING = 'verifying';

    public const PAYMENT_PAID = 'paid';

    public const PAYMENT_REJECTED = 'rejected';

    public const PAYMENT_EXPIRED = 'expired';

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'addon_total' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'total' => 'decimal:2',
            'discount_percent' => 'integer',
            'paid_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(TransactionAddon::class);
    }

    public function customFeatureRequests(): HasMany
    {
        return $this->hasMany(CustomFeatureRequest::class);
    }

    public function coachingBookings(): HasMany
    {
        return $this->hasMany(CoachingBooking::class);
    }

    public function paymentLogs(): HasMany
    {
        return $this->hasMany(PaymentLog::class);
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    public function refundRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RefundRequest::class);
    }

    public function projectRequest(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProjectRequest::class, 'transaction_id');
    }
}
