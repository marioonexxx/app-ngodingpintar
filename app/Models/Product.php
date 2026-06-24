<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'product_type',
    'membership_id',
    'coaching_topic_id',
    'mentor_id',
    'vendor_id',
    'name',
    'slug',
    'short_description',
    'description',
    'price',
    'sale_price',
    'thumbnail',
    'screenshots',
    'file_path',
    'installation_guide',
    'demo_url',
    'status',
    'rejection_reason',
    'download_limit',
    'session_duration_minutes',
    'meeting_platform',
    'meeting_note',
])]
class Product extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PENDING = 'pending';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_ARCHIVED = 'archived';

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'screenshots' => 'array',
        ];
    }

    public function categories(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class);
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function coachingTopic(): BelongsTo
    {
        return $this->belongsTo(CoachingTopic::class, 'coaching_topic_id');
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    public function scopeWithApprovedRating(Builder $query): Builder
    {
        return $query
            ->withAvg([
                'testimonials as approved_testimonials_avg_rating' => fn (Builder $query) => $query
                    ->where('status', Testimonial::STATUS_APPROVED),
            ], 'rating')
            ->withCount([
                'testimonials as approved_testimonials_count' => fn (Builder $query) => $query
                    ->where('status', Testimonial::STATUS_APPROVED),
            ]);
    }

    public function classProduct(): HasOne
    {
        return $this->hasOne(ClassBatch::class)
            ->ofMany(
                ['batch_number' => 'max'],
                fn (Builder $query) => $query->open()
            );
    }

    public function classBatches(): HasMany
    {
        return $this->hasMany(ClassBatch::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function getEffectivePriceAttribute(): string
    {
        return (string) ($this->sale_price ?? $this->price);
    }
}
