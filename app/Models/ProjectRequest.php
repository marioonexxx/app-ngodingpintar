<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'vendor_id',
    'transaction_id',
    'title',
    'category',
    'description',
    'budget',
    'deadline_date',
    'whatsapp',
    'status',
    'rejection_reason',
    'github_link',
    'completion_notes',
    'attachment',
])]
class ProjectRequest extends Model
{
    use SoftDeletes;

    public const STATUS_PENDING_ADMIN = 'pending_admin';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_WAITING_PAYMENT = 'waiting_payment';
    public const STATUS_OPEN = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_COMPLAINED = 'complained';
    public const STATUS_REFUND_PENDING = 'refund_pending';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_CANCELLED = 'cancelled';

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'deadline_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function applicants(): HasMany
    {
        return $this->hasMany(ProjectApplicant::class);
    }

    public function progressUpdates(): HasMany
    {
        return $this->hasMany(ProjectProgress::class);
    }

    public function complaint(): HasOne
    {
        return $this->hasOne(ProjectComplaint::class);
    }
}
