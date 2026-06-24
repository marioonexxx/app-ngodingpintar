<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['code', 'discount_percent', 'is_active', 'used_count'])]
class PromoCode extends Model
{
    use SoftDeletes;

    public const DISCOUNT_OPTIONS = [5, 10, 20];

    protected function casts(): array
    {
        return [
            'discount_percent' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function isUsable(): bool
    {
        return $this->is_active;
    }
}
