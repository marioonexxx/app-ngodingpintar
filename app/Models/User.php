<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'address', 'google_id', 'avatar', 'avatar_url', 'email_verified_at', 'last_login_at', 'bank_name', 'bank_account_name', 'bank_account_number', 'balance'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_MEMBER = 'member';

    public const ROLE_PARTNER = 'partner';

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    public function projectRequests(): HasMany
    {
        return $this->hasMany(ProjectRequest::class, 'user_id');
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(PartnerEarning::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(PartnerWithdrawal::class);
    }

    public function customFeatureRequests(): HasMany
    {
        return $this->hasMany(CustomFeatureRequest::class);
    }

    public function coachingBookings(): HasMany
    {
        return $this->hasMany(CoachingBooking::class, 'user_id');
    }

    public function mentorBookings(): HasMany
    {
        return $this->hasMany(CoachingBooking::class, 'mentor_id');
    }

    public function partnerProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PartnerProfile::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isVendor(): bool
    {
        return $this->role === self::ROLE_PARTNER && $this->partnerProfile?->vendor_status === 'approved';
    }

    public function isMentor(): bool
    {
        return $this->role === self::ROLE_PARTNER && $this->partnerProfile?->mentor_status === 'approved';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'balance' => 'decimal:2',
        ];
    }
}
