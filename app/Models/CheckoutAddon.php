<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'slug',
    'description',
    'type',
    'price',
    'is_active',
])]
class CheckoutAddon extends Model
{
    public const TYPE_PAID = 'paid';

    public const TYPE_CUSTOM_REQUEST = 'custom_request';

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function isPaid(): bool
    {
        return $this->type === self::TYPE_PAID;
    }

    public function isCustomRequest(): bool
    {
        return $this->type === self::TYPE_CUSTOM_REQUEST;
    }
}
