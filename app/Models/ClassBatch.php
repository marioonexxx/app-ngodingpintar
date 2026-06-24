<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

#[Fillable([
    'product_id',
    'batch_number',
    'status',
    'schedule_type',
    'start_date',
    'end_date',
    'start_time',
    'end_time',
    'recurring_days',
    'platform',
    'meeting_link',
    'max_participants',
])]
class ClassBatch extends Model
{
    public const STATUS_OPEN = 'open';

    public const STATUS_CLOSED = 'closed';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'recurring_days' => 'array',
        ];
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function startsAt(): ?CarbonImmutable
    {
        if (! $this->start_date) {
            return null;
        }

        $time = $this->start_time ? substr((string) $this->start_time, 0, 5) : '00:00';

        return CarbonImmutable::createFromFormat(
            'Y-m-d H:i',
            $this->start_date->format('Y-m-d').' '.$time,
            config('app.class_timezone', 'Asia/Jakarta')
        );
    }

    public function hasStarted(?CarbonInterface $now = null): bool
    {
        $startsAt = $this->startsAt();

        if (! $startsAt) {
            return false;
        }

        $now ??= CarbonImmutable::now(config('app.class_timezone', 'Asia/Jakarta'));

        return $startsAt->lessThanOrEqualTo($now);
    }
}
