<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerProfile extends Model
{
    protected $fillable = [
        'user_id', 'brand_name', 'bio', 'whatsapp', 'profile_picture', 
        'linkedin_url', 'github_url', 'website_url', 'portfolio_url', 
        'expertise_area', 'experience_years', 'certifications', 
        'bank_name', 'bank_account_name', 'bank_account_number', 
        'vendor_status', 'vendor_rejection_reason', 
        'mentor_status', 'mentor_rejection_reason'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
