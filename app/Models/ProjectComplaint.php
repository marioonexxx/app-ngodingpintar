<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'project_request_id',
    'member_reason',
    'member_proof',
    'vendor_response',
    'vendor_proof',
    'admin_decision',
    'status',
])]
class ProjectComplaint extends Model
{
    public const STATUS_PENDING_VENDOR = 'pending_vendor';
    public const STATUS_PENDING_ADMIN = 'pending_admin';
    public const STATUS_RESOLVED_REFUND = 'resolved_refund';
    public const STATUS_RESOLVED_RELEASE = 'resolved_release';

    public function projectRequest(): BelongsTo
    {
        return $this->belongsTo(ProjectRequest::class);
    }
}
